<?php

namespace flipbox\queue\actions\jobs;

use flipbox\queue\models\Settings as SettingsModel;

/**
 * @method SettingsModel getSettings()
 */
class SingleQueue extends AbstractJob
{
    /**
     * @return array
     */
    protected function performAction()
    {
        $job = $this->createJobFromRequest();

        if ($this->queue->post($job)) {
            return ['status' => 'okay', 'jobId' => $job->id];
        } else {
            return $this->handleFailedToPostJobException();
        }
    }
}
