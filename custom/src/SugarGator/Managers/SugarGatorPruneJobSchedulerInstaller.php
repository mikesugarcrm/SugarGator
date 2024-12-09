<?php

namespace Sugarcrm\Sugarcrm\custom\SugarGator\Managers;

use BeanFactory;
use DBManagerFactory;
use Doctrine\DBAL\Exception;
use SugarQuery;
use SugarQueryException;
use TimeDate;

class SugarGatorPruneJobSchedulerInstaller
{
    public string $jobClass = "class::Sugarcrm\\Sugarcrm\\custom\\SugarGator\\Jobs\\PruneLogRecords";

    /**
     * @throws SugarQueryException
     */
    public function createScheduler(): void
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->select("id");
        $sugarQuery->from(BeanFactory::newBean("Schedulers"));
        $sugarQuery->where()->equals("job", $this->jobClass);
        $result = $sugarQuery->execute();

        if (count($result) !== 0) {
            return;
        }

        $job = BeanFactory::newBean("Schedulers");
        $job->name = translate('LBL_SUGARCRM_SUGARCRM_CUSTOM_SUGARGATOR_JOBS_PRUNELOGRECORDS', 'Schedulers');
        $job->job = $this->jobClass;
        $job->date_time_start = TimeDate::getInstance()->nowDb();
        $job->date_time_end = null;
        $job->job_interval = "*/5::*::*::*::*";
        $job->status = "Active";
        $job->catch_up = true;
        $job->save();
    }


    /**
     * @throws Exception
     */
    public function deactivateAndSoftDelete(): void
    {
        $qb = DBManagerFactory::getConnection()->createQueryBuilder();

        $qb->update("schedulers", "s")
            ->set("s.status", $qb->createPositionalParameter("Inactive"))
            ->set("s.deleted", $qb->createPositionalParameter(1))
            ->where(
                $qb->expr()->eq("s.job", $qb->createPositionalParameter($this->jobClass))

            );
        $qb->executeStatement();
    }

}
