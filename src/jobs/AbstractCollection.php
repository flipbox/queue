<?php

namespace flipbox\queue\jobs;

use flipbox\queue\helpers\JobHelper;
use flipbox\queue\jobs\traits\JobCollectionTrait;

abstract class AbstractCollection extends AbstractJob
{

    use JobCollectionTrait;

    /**
     * @return bool
     */
    public function runInternal()
    {
        foreach ($this->getJobs() as $job) {
            $this->runJob($job);
        }
        return true;
    }

    /**
     * @param string $job
     * @return mixed
     */
    protected function runJob($job)
    {

        if(is_string($job)) {
            $job = $this->jobConfig($job);
        }

        if(!$job instanceof JobInterface) {
            $job = JobHelper::create($job);
        }
        return $job->run();
    }
}
