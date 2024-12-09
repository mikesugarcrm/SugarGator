<?php

//namespace Sugarcrm\Sugarcrm\custom\inc\SugarLogger;
use Doctrine\DBAL\Connection;

class SugarGator extends SugarLogger implements LoggerTemplate
{
    public SugarBean $bean;
    public int $max_num_records = 10000;
    public int $prune_records_older_than_days = 30; // in days.
    public string $defaultChannel = 'sugarcrm';
    public string $channel = '';
    public $config = [];
    public DBManager $db;
    public Connection $conn;


    public function __construct()
    {
        parent::__construct();
        $this->channel = $this->config['channel'] = $this->defaultChannel;
        LoggerManager::setLogger('default', 'SugarGator');
    }


    public function configure(array $config = []): void
    {
        if (empty($config)) {
            $config = SugarConfig::getInstance()->get('logger');
        }
        $this->config = $config;

        if (isset($this->config['name'])) {
            $this->config['channel'] = $this->config['name'];
        } else {
            $this->config['channel'] = $this->defaultChannel;
        }

        $this->setConfigValue('max_num_records');
        $this->setConfigValue('prune_records_older_than_days');
        $this->setConfigValue('channel');
    }


    // if this log entry is coming from the default $GLOBALS['log']->fatal("foo") logging, we want to record it on the bean
    // and ensure that it's also written to the sugarcrm.log (or whichever file its using).
    public function copyToDefaultLogger(): bool
    {
        if (empty($this->channel) || $this->channel == $this->defaultChannel) {
            return true;
        }

        $fileHandlerFound = false;
        $cfg = SugarConfig::getInstance();
        $channelHandlers = $cfg->get("logger.channels.{$this->channel}.handlers");
        foreach ($channelHandlers as $index => $handler) {
            if (isset($handler['type']) && $handler['type'] == 'File') {
                $fileHandlerFound = true;
            }
        }

        // if there is a file handler, it will take care of writing to its log file, we don't need to log anything else.
        // But if there is no file handler, we should write this entry to the sugarcrm log.
        if (!$fileHandlerFound) {
            return true;
        }

        return false;
    }


    public function setConfigValue(string $key): void
    {
        if (isset($this->config[$key])) {
            $this->$key = $this->config[$key];
        }
    }


    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }


    public function log($level, $message): void
    {
        global $current_user;
        if ($this->copyToDefaultLogger()) {
            parent::log($level, $message);
        }

        if (is_array($message) && safeCount($message) == 1) {
            $message = array_shift($message);
        }

        if (is_array($message) && safeCount($message) > 1) {
            $message = implode("\n", $message);
        }

        $logsBean = BeanFactory::newBean('sg_LogsAggregator');

        if (is_null($logsBean) || !is_a($logsBean, 'sg_LogsAggregator')) {
            return;
        }

        $this->bean = $logsBean;

        $this->bean->channel = $this->channel;
        $this->bean->pid = getmypid();
        $this->bean->log_level = $level;
        $this->bean->description = $message;
        $this->bean->name = substr($message, 0, 255);
        $this->bean->assigned_user_id = $current_user->id;
        $this->bean->save();
    }
}
