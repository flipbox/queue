<?php

namespace flipbox\queue\jobs;

use Craft;
use flipbox\queue\jobs\traits\JobCollectionTrait;
use yii\base\Component;

abstract class AbstractCollection extends Component implements JobInterface
{

    use JobCollectionTrait;

    /**
     * @return bool
     */
    public function run()
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
            $job = Craft::createObject($job);
        }
        return $job->run();
    }
}
