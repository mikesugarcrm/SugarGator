<?php

use Sugarcrm\Sugarcrm\custom\SugarGator\Setup\SugarGatorSetup;
$GLOBALS['log']->fatal("Pre Uninstall script running");
SugarGatorSetup::tearDown();
$GLOBALS['log']->fatal("Pre Uninstall script done");
