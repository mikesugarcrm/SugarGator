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

// THIS CONTENT IS GENERATED BY MBPackage.php
$manifest = array (
  'built_in_version' => '13.3.0',
  'acceptable_sugar_versions' => 
  array (
    0 => '13.*',
    1 => '14.*',
  ),
  'acceptable_sugar_flavors' => 
  array (
    0 => 'ENT',
    1 => 'ULT',
  ),
  'readme' => '',
  'key' => 'sg',
  'author' => 'Mike Andersen',
  'description' => 'Sugar Logs Aggregator',
  'icon' => '',
  'is_uninstallable' => true,
  'name' => 'SugarGator',
  'published_date' => '2024-11-19 16:50:39',
  'type' => 'module',
  'version' => 0.10,
  'remove_tables' => 'prompt',
);


$installdefs = array (
  'id' => 'sugargator',
  'beans' => 
  array (
    0 => 
    array (
      'module' => 'sg_LogsAggregator',
      'class' => 'sg_LogsAggregator',
      'path' => 'modules/sg_LogsAggregator/sg_LogsAggregator.php',
      'tab' => false,
    ),
  ),
  'layoutdefs' => 
  array (
  ),
  'relationships' => 
  array (
  ),
  'copy' => 
  array (
    0 => 
    array (
      'from' => '<basepath>/SugarModules/modules/sg_LogsAggregator',
      'to' => 'modules/sg_LogsAggregator',
    ),
    1 => 
    array (
      'from' => '<basepath>/custom/Extension/application/Ext/Include/sugargator.php',
      'to' => 'custom/Extension/application/Ext/Include/sugargator.php',
    ),
    2 => 
    array (
      'from' => '<basepath>/custom/Extension/application/Ext/Language/en_us.sugargator.php',
      'to' => 'custom/Extension/application/Ext/Language/en_us.sugargator.php',
    ),
    3 => 
    array (
      'from' => '<basepath>/custom/Extension/modules/sg_LogsAggregator/Ext/Vardefs/readonly_fields.php',
      'to' => 'custom/Extension/modules/sg_LogsAggregator/Ext/Vardefs/readonly_fields.php',
    ),
    4 => 
    array (
      'from' => '<basepath>/custom/include/SugarLogger/SugarGator.php',
      'to' => 'custom/include/SugarLogger/SugarGator.php',
    ),
    5 => 
    array (
      'from' => '<basepath>/custom/modules/sg_LogsAggregator/clients/base/filters/default/default.php',
      'to' => 'custom/modules/sg_LogsAggregator/clients/base/filters/default/default.php',
    ),
    6 => 
    array (
      'from' => '<basepath>/custom/modules/sg_LogsAggregator/metadata/SearchFields.php',
      'to' => 'custom/modules/sg_LogsAggregator/metadata/SearchFields.php',
    ),
    7 => 
    array (
      'from' => '<basepath>/custom/src/Logger/Handler/Factory/SugarGator.php',
      'to' => 'custom/src/Logger/Handler/Factory/SugarGator.php',
    ),
    8 => 
    array (
      'from' => '<basepath>/custom/src/Logger/Handler/SugarGatorHandler.php',
      'to' => 'custom/src/Logger/Handler/SugarGatorHandler.php',
    ),
    9 =>
    array (
      'from' => '<basepath>/custom/Extension/modules/sg_LogsAggregator/Ext/Vardefs/indices.php',
      'to' => 'custom/Extension/modules/sg_LogsAggregator/Ext/Vardefs/indices.php',
    ),
    10 =>
    array (
      'from' => '<basepath>/custom/Extension/modules/Schedulers/Ext/Language/en_us.sugar_gator_pruner.php',
      'to' => 'custom/Extension/modules/Schedulers/Ext/Language/en_us.sugar_gator_pruner.php',
    ),
    11 =>
    array (
      'from' => '<basepath>/custom/Extension/modules/Schedulers/Ext/ScheduledTasks/sugarGatorPrunerJob.php',
      'to' => 'custom/Extension/modules/Schedulers/Ext/ScheduledTasks/sugarGatorPrunerJob.php',
    ),
    12 =>
    array (
      'from' => '<basepath>/custom/src/SugarGator/Config/SugarGatorConfigurator.php',
      'to' => 'custom/src/SugarGator/Config/SugarGatorConfigurator.php',
    ),
    13 =>
    array (
      'from' => '<basepath>/custom/src/SugarGator/ACLs/SugarGatorACL.php',
      'to' => 'custom/src/SugarGator/ACLs/SugarGatorACL.php',
    ),
    14 =>
    array (
      'from' => '<basepath>/custom/src/SugarGator/Managers/SugarGatorPruneJobSchedulerInstaller.php',
      'to' => 'custom/src/SugarGator/Managers/SugarGatorPruneJobSchedulerInstaller.php',
    ),
    15 =>
    array (
      'from' => '<basepath>/custom/src/SugarGator/Setup/SugarGatorSetup.php',
      'to' => 'custom/src/SugarGator/Setup/SugarGatorSetup.php',
    ),
    16 =>
    array (
      'from' => '<basepath>/custom/src/SugarGator/Jobs/PruneLogRecords.php',
      'to' => 'custom/src/SugarGator/Jobs/PruneLogRecords.php',
    ),
   17 =>
   array (
      'from' => '<basepath>/custom/clients/base/views/profileactions/profileactions.php',
      'to' => 'custom/clients/base/views/profileactions/profileactions.php',
   ),
   18 =>
   array (
      'from' => '<basepath>/custom/Extension/application/Ext/Language/en_us.sugargator_profile_menu_label.php',
      'to' => 'custom/Extension/application/Ext/Language/en_us.sugargator_profile_menu_label.php',
   ),
  19 =>
      array (
          'from' => '<basepath>/scripts/sugargator_post_install.php',
          'to' => 'scripts/sugargator_post_install.php',
      ),
  20 =>
      array (
          'from' => '<basepath>/scripts/sugargator_pre_uninstall.php',
          'to' => 'scripts/sugargator_pre_uninstall.php',
      ),
  21 =>
      array (
          'from' => '<basepath>/custom/clients/base/api/SugarGatorApi.php',
          'to' => 'custom/clients/base/api/SugarGatorApi.php',
      ),
  22 =>
      array (
          'from' => '<basepath>/custom/clients/base/views/SugarGatorDashlet/SugarGatorDashlet.hbs',
          'to' => 'custom/clients/base/views/SugarGatorDashlet/SugarGatorDashlet.hbs',
      ),
  23 =>
      array (
          'from' => '<basepath>/custom/clients/base/views/SugarGatorDashlet/SugarGatorDashlet.js',
          'to' => 'custom/clients/base/views/SugarGatorDashlet/SugarGatorDashlet.js',
      ),
  24 =>
      array (
          'from' => '<basepath>/custom/clients/base/views/SugarGatorDashlet/SugarGatorDashlet.php',
          'to' => 'custom/clients/base/views/SugarGatorDashlet/SugarGatorDashlet.php',
      ),
  25 =>
      array (
          'from' => '<basepath>/custom/modules/sg_LogsAggregator/clients/base/fields/enum/enum.js',
          'to' => 'custom/modules/sg_LogsAggregator/clients/base/fields/enum/enum.js',
      ),
  ),
  'post_execute' => 
  array (
    0 => '<basepath>/scripts/sugargator_post_install.php',
  ),
  'pre_uninstall' =>
  array (
      0 => '<basepath>/scripts/sugargator_pre_uninstall.php',
  ),
  'language' => 
  array (
    0 => 
    array (
      'from' => '<basepath>/SugarModules/language/application/en_us.lang.php',
      'to_module' => 'application',
      'language' => 'en_us',
    ),
  ),
  'image_dir' => '<basepath>/icons',
);
