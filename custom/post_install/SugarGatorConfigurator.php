<?php
global $sugar_config;
$cfg = new Configurator();
$cfg->loadConfig();

foreach ($sugar_config['logger']['channels'] as $channel => $settings) {
    foreach ($settings as $index => $handlerSettings) {
        if ($handlerSettings['type'] == 'SugarGator') {
            break 2;
        }
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
