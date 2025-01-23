<?PHP
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/**
 * THIS CLASS IS FOR DEVELOPERS TO MAKE CUSTOMIZATIONS IN
 */

use Monolog\Logger;

require_once('modules/sg_LogsAggregator/sg_LogsAggregator_sugar.php');
class sg_LogsAggregator extends sg_LogsAggregator_sugar {

    public $disable_row_level_security = true;
    public function getAllLoggingChannels(): array
    {
        global $sugar_config;
        $channels = ['' => ''];
        foreach ($sugar_config['logger']['channels'] as $channel => $settings) {
            $channels[$channel] = $channel;
        }

        return $channels;
    }


    public function getLogLevels(): array
    {
        $levels = [];
        $monologLevels = array_flip(Logger::getLevels());

        // add sugar log levels with best guesses for index value - it's only used for sorting here.
        $monologLevels[301] = 'warn';
        $monologLevels[201] = 'deprecated';
        $monologLevels[501] = 'fatal';
        $monologLevels[601] = 'security';

        ksort($monologLevels);

        foreach ($monologLevels as $intVal => $logLevel) {
            $logMethod = strtolower($logLevel);
            $levels[$logMethod] = $logMethod;
        }
        return $levels;
    }
}
