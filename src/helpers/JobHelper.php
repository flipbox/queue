<?php

namespace flipbox\queue\helpers;

use craft\base\Plugin;
use flipbox\queue\jobs\JobInterface;
use flipbox\queue\models\Settings as SettingsModel;
use flipbox\queue\queues\QueueInterface;
use flipbox\spark\helpers\ObjectHelper;
use yii\di\Instance;

/**
 * @method SettingsModel getSettings()
 */
class JobHelper
{
    /**
     * @param $config
     * @return JobInterface
     */
    public static function create($config)
    {
        // Get class from config
        $class = ObjectHelper::checkConfig($config, JobInterface::class);

        return new $class($config);
    }
}
