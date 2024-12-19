<?php

use Sugarcrm\Sugarcrm\custom\SugarGator\Setup\SugarGatorSetup;
$GLOBALS['log']->fatal("Post Install script running");
SugarGatorSetup::setup();
$GLOBALS['log']->fatal("Post Install script done");
