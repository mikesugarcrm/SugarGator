<?php
$module_name = 'sg_LogsAggregator';
$viewdefs[$module_name] = 
array (
  'base' => 
  array (
    'view' => 
    array (
      'list' => 
      array (
        'panels' => 
        array (
          0 => 
          array (
            'label' => 'LBL_PANEL_1',
            'fields' => 
            array (
              0 => 
              array (
                'name' => 'name',
                'label' => 'LBL_NAME',
                'default' => true,
                'enabled' => true,
                'link' => true,
              ),
              1 => 
              array (
                'name' => 'log_level',
                'label' => 'LBL_LOG_LEVEL',
                'enabled' => true,
                'readonly' => true,
                'default' => true,
              ),
              2 => 
              array (
                'name' => 'pid',
                'label' => 'LBL_PID',
                'enabled' => true,
                'readonly' => true,
                'default' => true,
              ),
              3 => 
              array (
                'name' => 'channel',
                'label' => 'LBL_CHANNEL',
                'enabled' => true,
                'readonly' => true,
                'default' => true,
              ),
              4 => 
              array (
                'name' => 'assigned_user_name',
                'label' => 'LBL_ASSIGNED_TO_NAME',
                'default' => true,
                'enabled' => true,
                'link' => true,
              ),
              5 => 
              array (
                'name' => 'date_entered',
                'enabled' => true,
                'default' => true,
              ),
              6 => 
              array (
                'name' => 'team_name',
                'label' => 'LBL_TEAM',
                'default' => false,
                'enabled' => true,
              ),
            ),
          ),
        ),
        'orderBy' => 
        array (
          'field' => 'date_modified',
          'direction' => 'desc',
        ),
      ),
    ),
  ),
);
