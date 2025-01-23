<?php

use Sugarcrm\Sugarcrm\custom\SugarGator\Config\SugarGatorConfigurator;

class SugarGatorApi extends ModuleApi
{
    public SugarGatorConfigurator $sgcfg;

    public function registerApiRest(): array
    {
        return [
            'get_configs' => [
                'reqType' => 'GET',
                'path' => ['SugarGator', '?'],
                'pathVars' => ['logger', 'channel'],
                'method' => 'getChanelConfigs',
                'shortHelp' => 'Returns config values for a SugarGator logging channel',
                'longHelp' => '',
            ],

            'get_options' => [
                'reqType' => 'GET',
                'path' => ['SugarGator', 'enum', '?'],
                'pathVars' => ['logger', 'field_type', 'function'],
                'method' => 'getEnumOptions',
                'shortHelp' => 'Returns options for SugarGator-specific config menus',
                'longHelp' => '',
            ],

            'set_configs' => [
                'reqType' => 'PUT',
                'path' => ['SugarGator', '?'],
                'pathVars' => ['logger', 'channel'],
                'method' => 'setChanelConfigs',
                'shortHelp' => 'Sets config values for a SugarGator logging channel',
                'longHelp' => '',
            ],
        ];
    }


    public function __construct()
    {
        $this->sgcfg = new SugarGatorConfigurator();
        parent::__construct();
    }


    /**
     * @throws SugarApiExceptionInvalidParameter
     */
    public function getChanelConfigs(ServiceBase $api, array $args): array
    {
        $this->validateChannel($args['channel']);
        $sugarGatorHandlerConfigs = $this->getChannelHandlerSettings($args['channel']);

        return [
            'channel' => $args['channel'],
            'log_level' => $sugarGatorHandlerConfigs['level'],
            'max_num_records' => $sugarGatorHandlerConfigs['max_num_records'],
            'prune_records_older_than_days' => $sugarGatorHandlerConfigs['prune_records_older_than_days'],
        ];
    }


    /**
     * @throws SugarApiExceptionInvalidParameter
     */
    public function setChanelConfigs(ServiceBase $api, array $args): array
    {
        global $sugar_config;
        $this->validateChannel($args['channel']);

        $cfg = new Configurator();
        $cfg->loadConfig();

        $channel = $args['channel'];
        $handlerIndex = $this->sgcfg->getSugarGatorHandlerIndex($sugar_config['logger']['channels'][$channel]['handlers']);
        $cfg->config['logger']['channels'][$channel]['handlers'][$handlerIndex]['level'] = $args['log_level'];
        $cfg->config['logger']['channels'][$channel]['handlers'][$handlerIndex]['max_num_records'] = $args['max_num_records'];
        $cfg->config['logger']['channels'][$channel]['handlers'][$handlerIndex]['prune_records_older_than_days'] = $args['prune_records_older_than_days'];
        $cfg->saveConfig();
        return $this->getChanelConfigs($api, $args);
    }


    /**
     * @throws SugarApiExceptionInvalidParameter
     */
    public function validateChannel(string $channel): bool
    {
        global $sugar_config;

        if (!isset($sugar_config['logger']['channels'][$channel])) {
            throw new SugarApiExceptionInvalidParameter("There is no logging channel named '$channel'.");
        }

        $handlers = $sugar_config['logger']['channels'][$channel]['handlers'];
        if (!$this->sgcfg->channelHasASugarGator($handlers)) {
            throw new SugarApiExceptionInvalidParameter("Logging channel '$channel' does not use the SugarGator logger");
        }

        return true;
    }


    /**
     * @throws SugarApiExceptionInvalidParameter
     */
    public function getChannelHandlerSettings(string $channel): array
    {
        global $sugar_config;
        $handlers = $sugar_config['logger']['channels'][$channel]['handlers'];

        $handler = $handlers[$this->sgcfg->getSugarGatorHandlerIndex($handlers)];
        if (empty($handler) || !is_array($handler)) {
            throw new SugarApiExceptionInvalidParameter("Cannot retrieve SugarGator settings for channel '$channel'");
        }
        return $handler;
    }


    public function getEnumOptions(ServiceBase $api, array $args): array
    {
        $options = [];
        $logsBean = BeanFactory::newBean('sg_LogsAggregator');
        $methodName = $args['function'];
        if (method_exists($logsBean, $methodName)) {
            throw new SugarApiExceptionInvalidParameter("sg_LogsAggregator does not implement the method '$methodName'.");
        }

        $options = $logsBean->$methodName();
        if (!is_array($options)) {
            throw new SugarApiExceptionInvalidParameter("sg_LogsAggregator method '$methodName' does not return a list of options.");
        }

        return $options;
    }
}
