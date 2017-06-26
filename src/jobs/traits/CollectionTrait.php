<?php

namespace flipbox\queue\jobs\traits;

use craft\helpers\ArrayHelper;
use flipbox\queue\helpers\JobHelper;
use flipbox\queue\jobs\JobInterface;
use yii\base\Exception;

trait CollectionTrait
{

    use JobTrait;

    /**
     * @var JobInterface[]
     */
    protected $jobs;

    /**
     * @param string $class
     * @return array
     */
    protected function jobConfig(string $class): array
    {
        return [
            'class' => $class
        ];
    }

    /**
     * @param array $jobs
     * @return static
     */
    public function setJobs(array $jobs = [])
    {
        $this->jobs = null;

        return $this->addJobs($jobs);
    }

    /**
     * @param array $jobs
     * @return static
     */
    protected function addJobs(array $jobs = [])
    {
        foreach ($jobs as $key => $job) {
            if (is_numeric($key) || empty($key)) {
                $key = null;
            }
            $this->addJob($key, $job);
        }

        return $this;
    }

    /**
     * @param $key
     * @param $job
     * @return static
     */
    public function addJob($key, $job)
    {
        if (is_string($job)) {
            $job = $this->jobConfig($job);
        }

        if (!$job instanceof JobInterface) {
            $job = JobHelper::create($job);
        }

        $this->jobs[$key] = $job;

        return $this;
    }

    /**
     * @return JobInterface[]
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param $identifier
     * @return JobInterface
     */
    public function findJob($identifier)
    {
        return ArrayHelper::getValue($this->jobs, $identifier);
    }

    /**
     * @param $identifier
     * @return JobInterface
     * @throws Exception
     */
    public function getJob($identifier)
    {
        if (!$job = $this->findJob($identifier)) {
            throw new Exception("Job not found.");
        }

        return $job;
    }

    /**
     * @param $identifier
     * @param array $config
     * @return JobInterface
     */
    public function createJob($identifier, array $config = [])
    {
        $config['class'] = $this->getJob($identifier);

        return JobHelper::create($config);
    }
}
