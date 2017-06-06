<?php

namespace flipbox\queue\jobs;

use flipbox\queue\models\Settings as SettingsModel;
use UrbanIndo\Yii2\Queue\Job;

/**
 * @method SettingsModel getSettings()
 */
abstract class AbstractCallable extends Job
{
    /**
     * @return callable
     */
    abstract protected function route(): callable;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->route = $this->route();
    }
}
