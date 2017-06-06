<?php

namespace flipbox\queue;

use craft\base\Plugin;
use flipbox\queue\models\Settings as SettingsModel;
use UrbanIndo\Yii2\Queue\Queue as YiiQueue;
use yii\di\Instance;

/**
 * @method SettingsModel getSettings()
 */
class Queue extends Plugin
{
    /**
     * @return SettingsModel
     */
    public function createSettingsModel()
    {
        return new SettingsModel();
    }

    /**
     * @return YiiQueue
     */
    public function getQueue()
    {
        return $this->get('queue');
    }

    /**
     * @param string $id
     * @param bool $throwException
     * @return null|object
     */
    public function get($id, $throwException = true)
    {
        if (!$this->has('queue')) {
            $this->set('queue', $this->getFromConfig());
        }
        return parent::get($id, $throwException);
    }

    /**
     * @return object
     */
    protected function getFromConfig()
    {
        return Instance::ensure($this->getSettings()->component, YiiQueue::className());
    }
}
