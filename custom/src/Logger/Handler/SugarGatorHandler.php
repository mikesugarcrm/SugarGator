<?php

namespace Sugarcrm\Sugarcrm\custom\Logger\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use SugarGator;

class SugarGatorHandler extends AbstractProcessingHandler
{
    public SugarGator $sugarGator;

    public function __construct($level, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->sugarGator = new SugarGator();
    }


    public function configure(array $config = [])
    {
        $this->sugarGator->configure($config);
    }


    /**
     * @inheritDoc
     */
    protected function write(array $record): void
    {
        $this->sugarGator->setChannel($record['channel']);
        $this->sugarGator->log($record['level_name'], $record['message']);
    }
}
