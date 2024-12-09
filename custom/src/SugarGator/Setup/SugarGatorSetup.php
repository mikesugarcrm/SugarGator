<?php


namespace Sugarcrm\Sugarcrm\custom\SugarGator\Setup;
use Sugarcrm\Sugarcrm\custom\SugarGator\ACLs\SugarGatorACL;
use Sugarcrm\Sugarcrm\custom\SugarGator\Config\SugarGatorConfigurator;
use Sugarcrm\Sugarcrm\custom\SugarGator\Managers\SugarGatorPruneJobSchedulerInstaller;

class SugarGatorSetup
{
    public static function setup(): void
    {
        $acl = new SugarGatorACL();
        $acl->setSugarGatorACLs();

        $configurator = new SugarGatorConfigurator();
        $configurator->configure();

        $jobInstaller = new SugarGatorPruneJobSchedulerInstaller();
        $jobInstaller->createScheduler();
    }


    public static function tearDown(): void
    {
        $acl = new SugarGatorACL();
        $acl->deleteSugarGatorACLs();

        $configurator = new SugarGatorConfigurator();
        $configurator->unconfigure();

        $jobInstaller = new SugarGatorPruneJobSchedulerInstaller();
        $jobInstaller->deactivateAndSoftDelete();
    }
}
