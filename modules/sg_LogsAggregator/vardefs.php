<?php

/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

$dictionary['sg_LogsAggregator'] = array(
    'table' => 'sg_logsaggregator',
    'audited' => false,
    'activity_enabled' => false,
    'duplicate_merge' => true,
    'fields' => array (
  'channel' => 
  array (
    'required' => false,
    'readonly' => true,
    'name' => 'channel',
    'vname' => 'LBL_CHANNEL',
    'type' => 'enum',
    'function' => 'getAllLoggingChannels',
    'function_bean' => 'sg_LogsAggregator',
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'audited' => false,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'full_text_search' => 
    array (
      'enabled' => '0',
      'boost' => '1',
      'searchable' => false,
    ),
    'calculated' => false,
    'len' => '255',
    'size' => '20',
  ),
  'pid' => 
  array (
    'required' => false,
    'readonly' => true,
    'name' => 'pid',
    'vname' => 'LBL_PID',
    'type' => 'varchar',
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => 'Process ID',
    'help' => 'Process ID',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'audited' => false,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'full_text_search' => 
    array (
      'enabled' => '0',
      'boost' => '1',
      'searchable' => false,
    ),
    'calculated' => false,
    'len' => '8',
    'size' => '20',
  ),
  'log_level' => 
  array (
    'required' => false,
    'readonly' => true,
    'name' => 'log_level',
    'vname' => 'LBL_LOG_LEVEL',
    'type' => 'enum',
    'function' => 'getLogLevels',
    'function_bean' => 'sg_LogsAggregator',
    'visibility_grid' => array(
        'trigger' => 'channel',
        'values' => array(
            '' => array(),
            'sugarcrm' => array(
                // don't allow the 'debug' option for sugarcrm.log - it causes infinite looping errors.
                //'debug' => 'debug',
                'info' => 'info',
                'warn' => 'warn',
                'deprecated' => 'deprecated',
                'error' => 'error',
                'fatal' => 'fatal',
                'security' => 'security',
                'off' => 'off',
            ),
            'not_sugarcrm' => array(
                // custom monolog channels may use debug. It's only the sugarcrm log that seems to have a problem at that level.
                'debug' => 'debug',
                'info' => 'info',
                'notice' => 'notice',
                'warning' => 'warning',
                'error' => 'error',
                'critical' => 'critical',
                'alert' => 'alert',
                'emergency' => 'emergency',
            ),
        ),
    ),
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => 'Log Level',
    'help' => 'Log Level',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'audited' => false,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'full_text_search' => 
    array (
      'enabled' => '0',
      'boost' => '1',
      'searchable' => false,
    ),
    'calculated' => false,
    'len' => '32',
    'size' => '20',
  ),
),
    'relationships' => array (
),
    'optimistic_locking' => true,
    'unified_search' => true,
    'full_text_search' => false,
);

if (!class_exists('VardefManager')){
}
VardefManager::createVardef('sg_LogsAggregator','sg_LogsAggregator', array('basic','team_security','assignable','taggable'));
