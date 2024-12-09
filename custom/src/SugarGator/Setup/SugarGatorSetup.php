<?php

use Sugarcrm\Sugarcrm\custom\SugarGator\ACLs\SugarGatorACL;

class SugarGatorSetup
{
    public function __construct()
    {

    }


    public static function setup(): void
    {
        $acl = new SugarGatorACL();
        $acl->setSugarGatorACLs();

        $configurator = new SugarGatorConfigurator();
        $configurator->configure();
    }


    public static function tearDown(): void
    {
        $acl = new SugarGatorACL();
        $acl->deleteSugarGatorACLs();

        $configurator = new SugarGatorConfigurator();
        $configurator->unconfigure();
    }
}
