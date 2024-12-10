<?php

namespace Sugarcrm\Sugarcrm\custom\SugarGator\Config;

use Configurator;

class SugarGatorConfigurator
{
    public function configure()
    {
        global $sugar_config;
        $cfg = new Configurator();
        $cfg->loadConfig();

        foreach ($sugar_config['logger']['channels'] as $channel => $settings) {
            if ($this->channelHasASugarGator($settings['handlers'])) {
                break;
            }

            $cfg->config['logger']['channels'][$channel]['handlers'][] = [
                'level' => 'debug',
                'type' => 'SugarGator',
                'name' => $channel,
                'max_num_records' => 100,
                'prune_records_older_than_days' => 2
            ];
        }
        $cfg->saveConfig();
    }


    public function unconfigure(): void
    {
        global $sugar_config;
        $cfg = new Configurator();
        $cfg->loadConfig();

        foreach ($sugar_config['logger']['channels'] as $channel => $settings) {
            $handlerIndex = $this->getSugarGatorHandlerIndex($settings);
            if ($handlerIndex === false) {
                continue;
            }

            unset($cfg->config['logger']['channels'][$channel]['handlers'][$handlerIndex]);
            unset($sugar_config['logger']['channels'][$channel]['handlers'][$handlerIndex]);
        }
        $cfg->saveConfig();
    }


    public function getSugarGatorHandlerIndex($settings): int|bool
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
