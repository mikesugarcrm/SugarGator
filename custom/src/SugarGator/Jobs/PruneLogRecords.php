<?php

namespace Sugarcrm\Sugarcrm\custom\SugarGator\Jobs;

use BeanFactory;
use DateInterval;
use DBManagerFactory;
use Exception;
use SchedulersJob;
use SugarBean;
use SugarConfig;
use Doctrine\DBAL\Connection;
use SugarDateTime;

class PruneLogRecords implements \RunnableSchedulerJob
{
    public SchedulersJob $job;
    public array $channels = [];
    public array $channelConfigs = [];
    public SugarBean $seed;
    public int $max_num_records = 10000;
    public int $prune_records_older_than_days = 30;
    public Connection $conn;

    /**
     * @inheritDoc
     */
    public function setJob(SchedulersJob $job): void
    {
        $this->job = $job;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function run($data): bool
    {
        $this->seed = BeanFactory::newBean('sg_LogsAggregator');
        $this->conn = DBManagerFactory::getInstance()->getConnection();
        $this->retrieveAllSugarGatorLoggerChannels();
        $this->pruneLoggingRecords();
        return true;
    }


    public function pruneLoggingRecords(): void
    {
        if (!$this->loggingTableExists()) {
            $this->log("Logging table '{$this->seed->table_name}' doesn't exist - nothing to prune.");
            return;
        }

        foreach ($this->channelConfigs as $channel => $config) {
            $this->pruneSoftDeletedRecords();
            $this->pruneRecordsOverMaxAge($channel);
            $this->pruneRecordsOverMaxNum($channel);
        }
    }


    public function pruneSoftDeletedRecords(): void
    {
        $sql = "delete from {$this->seed->table_name} where deleted = ?";
        $params = [1];
        $deletedRows = 0;

        try {
            $deletedRows = $this->conn->executeStatement($sql, $params);
        } catch (Exception $e) {
            $this->log("Could not delete soft-deleted records from {$this->seed->table_name}. {$e->getMessage()}");
            return;
        }

        if ($deletedRows > 0) {
            $this->log("Deleted $deletedRows soft-deleted log records.");
        }
    }


    public function pruneRecordsOverMaxNum(string $channel): void
    {
        $maxNum = $this->getSugarGatorConfigValue($channel, 'max_num_records');
        $deletedRows = 0;

        if ($maxNum <= 0) {
            $this->log("Cannot prune records by max number of records. max_num_records is '$maxNum' for channel '$channel'.");
            return;
        }

        $sql = "
                delete from {$this->seed->table_name} 
                where id in (
                    select id from (
                        select id 
                        from sg_logsaggregator 
                        where channel = ? 
                        and deleted = ?
                        order by date_entered DESC 
                        limit ?,?
                        ) log_ids
                    )";
        // note: using PHP_INT_MAX here is horrible but necessary because mysql doesn't support using an offset without a limit.
        $params = [$channel, 0, $maxNum, PHP_INT_MAX];

        try {
            $deletedRows = $this->conn->executeStatement($sql, $params);
        } catch (Exception $e) {
            $this->log("could not prune logs for channel '$channel' by max_num_records with query\n$sql\n max_num_records = '$maxNum'\nException:\n" . $e->getMessage());
            return;
        }

        $this->log("Deleted $deletedRows records for channel '$channel' that exceeded $maxNum records.");
    }


    public function pruneRecordsOverMaxAge(string $channel): void
    {
        $numOfDays = $this->getSugarGatorConfigValue($channel, 'prune_records_older_than_days');
        $deletedRows = 0;

        if ($numOfDays <= 0) {
            $this->log("Cannot prune logs by date: prune_records_older_than_days is '$numOfDays' for channel '$channel");
            return;
        }

        $pruneDate = '';
        $sql = "delete from {$this->seed->table_name} where channel = ? and date_entered < ? and deleted = ?";

        try {
            $pruneDate = $this->getPruneByDate($numOfDays);
            $params = [$channel, $pruneDate, 0];
            $deletedRows = $this->conn->executeStatement($sql, $params);
        } catch (Exception $e) {
            $this->log("could not prune logs for channel '$channel' by date for $numOfDays days with query\n$sql\n pruneDate = '$pruneDate'\nException:\n" . $e->getMessage());
            return;
        }
        $this->log("Deleted $deletedRows records for channel '$channel' older than $pruneDate.");
    }


    /**
     * @throws Exception
     */
    protected function getPruneByDate(int $numOfDays): string
    {
        $dateIntervalToPrune = new DateInterval('P' . $numOfDays . 'D');

        $sugarDateTime = new SugarDateTime();

        try {
            $sugarDateTime->sub($dateIntervalToPrune);
        } catch (Exception $e) {
            $this->log("Cannot get prune by date - SugarDateTime->sub() failed: " . $e->getMessage());
            throw $e;
        }

        return $sugarDateTime->asDbDate();
    }


    public function getSugarGatorConfigValue(string $channel, $configName): int
    {
        $defaultConfigValue = 0;

        if (isset($this->$configName)) {
            $defaultConfigValue = $this->$configName;
        }

        if (!isset($this->channelConfigs[$channel])) {
            return $defaultConfigValue;
        }

        if (!isset($this->channelConfigs[$channel][$configName])) {
            return $defaultConfigValue;
        }

        return $this->channelConfigs[$channel][$configName];
    }


    public function retrieveAllSugarGatorLoggerChannels(): void
    {
        // there is no "logger" to retrieve for the sugarcrm.log, so create the settings here:
        $this->channelConfigs['sugarcrm'] = [
            'type' => 'SugarGator',
            'name' => 'sugarcrm',
            'max_num_records' => 10000,
            'prune_records_older_than_days' => 30,
        ];

        $cfg = SugarConfig::getInstance();
        $loggers = $cfg->get('logger.channels', []);
        foreach ($loggers as $channelName => $settings) {
            $this->storeSugarGatorHandlerConfig($channelName, $settings);
        }
    }


    public function storeSugarGatorHandlerConfig(string $channelName, array $settings): void
    {
        if (!isset($settings['handlers']) || !is_array($settings['handlers'])) {
            return;
        }

        foreach ($settings['handlers'] as $handlerConfig) {
            if (isset($handlerConfig['type']) && $handlerConfig['type'] === 'SugarGator') {
                $this->channelConfigs[$channelName] = $handlerConfig;
            }
        }
    }


    public function loggingTableExists(): bool
    {
        return DBManagerFactory::getInstance()->tableExists($this->seed->table_name);
    }


    public function log(string $msg): void
    {
        if (strlen($this->job->message. "\n" . $msg) < 4000) {
            $this->job->message .= "\n" . $msg;
        }
        $GLOBALS['log']->fatal('PruneLogRecords: ' . $msg);
    }
}
