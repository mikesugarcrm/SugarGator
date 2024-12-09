<?php

use Sugarcrm\Sugarcrm\custom\SugarGator\Setup\SugarGatorSetup;

if (function_exists("post_install") === false) {
function post_install()
{
    SugarGatorSetup::setup();
}


