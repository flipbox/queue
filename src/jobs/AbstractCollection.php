<?php

namespace flipbox\queue\jobs;

use flipbox\queue\helpers\JobHelper;
use flipbox\queue\jobs\traits\CollectionTrait;

abstract class AbstractCollection extends AbstractJob
{

    use CollectionTrait;

    /**
     * @var bool
     */
    public $toQueue = true;

    /**
     * @return bool
     */
    protected function runInternal()
    {
        $success = true;

        foreach ($this->getJobs() as $job) {
            if (!$this->runJob($job)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param string $job
     * @return mixed
     */
    protected function runJob($job)
    {
        if (is_string($job)) {
            $job = $this->jobConfig($job);
        }

        if (!$job instanceof JobInterface) {
            $job = JobHelper::create($job);
        }

        if ($this->toQueue) {
            return $job->toQueue();
        }

        return $job->run();
    }

    /**
     * @return array
     */
    public function toConfig(): array
    {
        return array_merge(
            parent::toConfig(),
            [
                'jobs' => $this->getJobsConfig()
            ]
        );
    }

    /**
     * @return array
     */
    protected function getJobsConfig()
    {
        $jobsConfig = [];
        foreach ($this->getJobs() as $job) {
            $jobsConfig[] = $job->toConfig();
        }
        return $jobsConfig;
    }
}
