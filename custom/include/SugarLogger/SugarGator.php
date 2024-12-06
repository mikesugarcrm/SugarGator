<?php

//namespace Sugarcrm\Sugarcrm\custom\inc\SugarLogger;
use Doctrine\DBAL\Connection;

class SugarGator extends SugarLogger implements LoggerTemplate
{
    public SugarBean $bean;
    public int $max_num_records = 10000;
    public int $prune_records_older_than_days = 30; // in days.
    public string $defaultChannel = 'sugarcrm';
    public string $channel = '';
    public $config = [];
    public DBManager $db;
    public Connection $conn;


    public function __construct()
    {
        parent::__construct();
        $this->channel = $this->config['channel'] = $this->defaultChannel;
        LoggerManager::setLogger('default', 'SugarGator');
    }


    public function configure(array $config = []): void
    {
        if (empty($config)) {
            $config = SugarConfig::getInstance()->get('logger');
        }
        $this->config = $config;

        if (isset($this->config['name'])) {
            $this->config['channel'] = $this->config['name'];
        } else {
            $this->config['channel'] = $this->defaultChannel;
        }

        $this->setConfigValue('max_num_records');
        $this->setConfigValue('prune_records_older_than_days');
        $this->setConfigValue('channel');
    }


    // if this log entry is coming from the default $GLOBALS['log']->fatal("foo") logging, we want to record it on the bean
    // and ensure that it's also written to the sugarcrm.log (or whichever file its using).
    public function copyToDefaultLogger(): bool
    {
        if (empty($this->channel) || $this->channel == $this->defaultChannel) {
            return true;
        }

        $fileHandlerFound = false;
        $cfg = SugarConfig::getInstance();
        $channelHandlers = $cfg->get("logger.channels.{$this->channel}.handlers");
        foreach ($channelHandlers as $index => $handler) {
            if (isset($handler['type']) && $handler['type'] == 'File') {
                $fileHandlerFound = true;
            }
        }

        // if there is a file handler, it will take care of writing to its log file, we don't need to log anything else.
        // But if there is no file handler, we should write this entry to the sugarcrm log.
        if (!$fileHandlerFound) {
            return true;
        }

        return false;
    }


    public function setConfigValue(string $key): void
    {
        if (isset($this->config[$key])) {
            $this->$key = $this->config[$key];
        }
    }


    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }


    public function log($level, $message): void
    {
        global $current_user;
        if ($this->copyToDefaultLogger()) {
            parent::log($level, $message);
        }

        if (is_array($message) && safeCount($message) == 1) {
            $message = array_shift($message);
        }

        $logsBean = BeanFactory::newBean('sg_LogsAggregator');

        if (is_null($logsBean) || !is_a($logsBean, 'sg_LogsAggregator')) {
            return;
        }

        $this->bean = $logsBean;

        $this->bean->channel = $this->channel;
        $this->bean->pid = getmypid();
        $this->bean->log_level = $level;
        $this->bean->description = $message;
        $this->bean->name = substr($message, 0, 255);
        $this->bean->assigned_user_id = $current_user->id;
        $this->bean->save();
    }


    /**
     * Needs to be called via daily scheduler job.
     *
     * @return void
     * @throws DateInvalidOperationException
     * @throws DateMalformedIntervalStringException
     * @throws Exception
     */
    protected function pruneLogEntries(): void
    {
        $this->db = DBManagerFactory::getInstance();
        $this->conn = DBManagerFactory::getInstance()->getConnection();
        $this->pruneOldLogs($this->getPruneByDate());
        $this->pruneLogsOverMaxNumRecords();
    }


    protected function pruneAuditTable(): void
    {
        $sql = "delete from sg_logsaggregator_audit where parent_id not in (select id from sg_logsaggregator)";
        try {
            $this->conn->executeStatement($sql);
        } catch (Exception $e) {
            $GLOBALS['log']->error("SugarGator could not prune logs audit table with query:\n$sql\nException: {$e->getMessage()}");
        }
    }


    protected function pruneLogsOverMaxNumRecords(): void
    {
        $sql = "delete from sg_logsaggregator where id in (select id from (select id from sg_logsaggregator where channel = ? order by date_entered DESC limit ?,?) log_ids)";
        $params = [$this->channel, 100, $this->max_num_records];
        try {
            $this->conn->executeStatement($sql, $params);
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("SugarGator could not prune logs by max_num_records with query\n$sql\n max_num_records = '$this->max_num_records'\nException:\n" . $e->getMessage());
        }
    }


    protected function pruneOldLogs(string $pruneDate): void
    {
        if (empty($pruneDate)) {
            return;
        }

        $sql = "delete from sg_logsaggregator where channel = ? and date_entered < ?";
        $params = [$this->channel, $pruneDate];
        try {
            $this->conn->executeStatement($sql, $params);
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("SugarGator could not prune logs by date with query\n$sql\n pruneDate = '$pruneDate'\nException:\n" . $e->getMessage());
        }
    }


    /**
     * @throws DateInvalidOperationException
     * @throws DateMalformedIntervalStringException
     */
    protected function getPruneByDate(): string
    {
        $dateIntervalToPrune = new DateInterval('PT' . $this->prune_records_older_than_days . 'd');
        $sugarDateTime = new SugarDateTime();
        $sugarDateTime->sub($dateIntervalToPrune);
        return $sugarDateTime->asDbDate();
    }
}
