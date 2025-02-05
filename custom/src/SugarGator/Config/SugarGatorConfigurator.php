<?php

namespace Sugarcrm\Sugarcrm\custom\SugarGator\Config;

use Configurator;

class SugarGatorConfigurator
{
    public function configure()
    {
        global $sugar_config;
        $GLOBALS['log']->fatal("SugarGator configurator is configuring");
        $cfg = new Configurator();
        $cfg->loadConfig();

        if (!isset($cfg->config['logger']['channels']['sugarcrm']['handlers'])) {
            $cfg->config['logger']['channels']['sugarcrm']['handlers'] = [];
        }

        foreach ($sugar_config['logger']['channels'] as $channel => $settings) {
            if ($this->channelHasASugarGator($settings['handlers'])) {
                $handlerIndex = $this->getSugarGatorHandlerIndex($settings['handlers']);
                // set this to the most restrictive setting initially. Otherwise, we can flood the system with logs.
                if ($cfg->config['logger']['channels'][$channel]['handlers'][$handlerIndex]['level'] != 'emergency') {
                    $cfg->config['logger']['channels'][$channel]['handlers'][$handlerIndex]['level'] = 'emergency';
                } else {
                    continue;
                }
            } else {
                $cfg->config['logger']['channels'][$channel]['handlers']['SugarGator'] = [
                    'level' => 'emergency',
                    'type' => 'SugarGator',
                    'name' => $channel,
                    'max_num_records' => 100,
                    'prune_records_older_than_days' => 2
                ];
            }
        }

        // create settings for the stock $GLOBALS['log']->method() logger to use the SugarGator.
        // This should be disabled by default.
        $cfg->config['logger']['channels']['sugarcrm']['handlers']['SugarGator']['level'] = 'off';

        $cfg->saveConfig();
        $GLOBALS['log']->fatal("SugarGator configurator is done with configuring!");
    }


    public function unconfigure(): void
    {
        global $sugar_config;
        $GLOBALS['log']->fatal("SugarGator configurator is uninstalling!");
        $cfg = new Configurator();
        $cfg->loadConfig();

        foreach ($cfg->config['logger']['channels'] as $channel => $settings) {
            if (!is_array($settings) || !isset($settings['handlers'])) {
                continue;
            }
            $sugar_config['logger']['channels'][$channel]['handlers'] = [];
            $sugarGatorHandlerIndex = $this->getSugarGatorHandlerIndex($settings['handlers']);
            if ($sugarGatorHandlerIndex === false) {
                continue;
            }

            if (isset($cfg->config['logger']['channels'][$channel]['handlers'][$sugarGatorHandlerIndex])) {
                $handlersToKeep = [];
                foreach ($settings['handlers'] as $handlerIndex => $handler) {
                    if ($sugarGatorHandlerIndex != $handlerIndex) {
                        $handlersToKeep[] = $handler;
                    }
                }
                $cfg->config['logger']['channels'][$channel]['handlers'] = $handlersToKeep;

                $GLOBALS['log']->fatal("removing cfg->config['logger']['channels']['$channel']['handlers'][$handlerIndex]");
            } else {
                $GLOBALS['log']->fatal("cannot remove cfg->config['logger']['channels']['$channel']['handlers'][$handlerIndex] - channel is not set");
            }
        }

        $cfg->saveConfig();
        $GLOBALS['log']->fatal("SugarGator configurator has finished uninstalling!");
    }


    public function getSugarGatorHandlerIndex($settings)
    {
        $handlerIndex = false;
        foreach ($settings as $index => $handlerSettings) {
            if ($handlerSettings['type'] == 'SugarGator') {
                $handlerIndex = $index;
                break;
            }
        }
        return $handlerIndex;
    }


    public function channelHasASugarGator($settings): bool
    {
        foreach ($settings as $index => $handlerSettings) {
            if ($handlerSettings['type'] == 'SugarGator') {
                return true;
            }
        }
        return false;
    }
}
