<?php

namespace Sugarcrm\Sugarcrm\custom\Logger\Handler\Factory;

use SugarConfig;
use Sugarcrm\Sugarcrm\custom\Logger\Handler\SugarGatorHandler;
use Sugarcrm\Sugarcrm\Logger\Formatter\BackwardCompatibleFormatter;
use Sugarcrm\Sugarcrm\Logger\Handler\Factory;

class SugarGator implements Factory
{

    /**
     * @inheritDoc
     */
    public function create($level, array $config)
    {
        $handler = new SugarGatorHandler($level, true);
        $handler->configure($config);

        $dateFormat = $config['dateFormat'] ?? SugarConfig::getInstance()->get('logger.file.dateFormat');

        $formatter = new BackwardCompatibleFormatter($dateFormat);
        $handler->setFormatter($formatter);

        return $handler;
    }
}
