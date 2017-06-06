<?php

namespace flipbox\queue\actions\jobs;

class SingleQueue extends AbstractJob
{
    /**
     * @return array
     */
    protected function performAction()
    {
        $job = $this->createJobFromRequest();

        if ($this->queue->post($job)) {
            return $this->transformSuccessResponse($job);
        } else {
            return $this->handleFailedToPostJobException();
        }
    }
}
