<?php
$viewdefs['base']['view']['SugarGatorDashlet'] = array(
    /*
    'filter' => array(
        'module' => array(
            'sg_LogsAggregator',
        ),
        'view' => array(
            'list',
            'record',
        ),
    ),
    */
    'dashlets' => array(
        array(
            'label' => 'LBL_SUGARGATOR_DASHLET',
            'description' => 'LBL_SUGARGATOR_DASHLET_DESCRIPTION',
            'config' => array(
                'limit' => 5,
                'auto_refresh' => 0,
            ),
            'preview' => array(
                'limit' => 5,
                'auto_refresh' => 0,
            ),
        ),
    ),

    'fields' => array(
        array(
            'name' => 'channel',
            'label' => 'LBL_SUGARGATOR_DASHLET_CHANNEL',
            'type' => 'enum',
            'function' => 'getAllLoggingChannels',
            'function_bean' => 'sg_LogsAggregator',
        ),
        array(
            'name' => 'log_level',
            'label' => 'LBL_SUGARGATOR_DASHLET_LEVEL',
            'type' => 'enum',
            'function' => 'getLogLevels',
            'function_bean' => 'sg_LogsAggregator',
        ),
        array(
            'name' => 'max_num_records',
            'label' => 'LBL_SUGARGATOR_DASHLET_MAX_NUM_RECORDS',
            'type' => 'int',
            'length' => 3,
        ),
        array(
            'name' => 'prune_records_older_than_days',
            'label' => 'LBL_SUGARGATOR_DASHLET_PRUNE_RECORDS_OLDER_THAN_DAYS',
            'type' => 'int',
            'length' => 3,
        ),
    ),

    'buttons' => array(
        array(
            'name' => 'update_configs',
            'label' => 'Update Configs',
            'type' => 'button',
            'length' => 3,
            'events' =>
                [
                    'click' => 'button:update_configs:click',
                ],
        )
    ,),
);
