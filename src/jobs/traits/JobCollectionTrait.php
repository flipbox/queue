<?php

namespace flipbox\queue\jobs\traits;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\queue\events\RegisterJobs;
use flipbox\queue\jobs\JobInterface;
use yii\base\Event;
use yii\base\Exception;

trait JobCollectionTrait
{

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
     * @var JobInterface[]
     */
    protected $jobs;

    /**
     * @param $name
     * @param Event|null $event
     * @return mixed
     */
    public abstract function trigger($name, Event $event = null);

    /**
     * @param array $jobs
     * @return static
     */
    public function setJobs(array $jobs = [])
    {

        $this->jobs = null;

        return $this->ensureJobs()
            ->addJobs($jobs);

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
     * @return $this
     */
    public function addJob($key, $job)
    {
        $this->ensureJobs();

        if(is_string($job)) {
            $job = $this->jobConfig($job);
        }

        if(!$job instanceof JobInterface) {
            $job = Craft::createObject($job);
        }

        $this->jobs[$key] = $job;
        return $this;
    }

    /**
     * @return JobInterface[]
     */
    public function getJobs()
    {
        $this->ensureJobs();
        return $this->jobs;
    }

    /**
     * @param $identifier
     * @return JobInterface
     */
    public function findJob($identifier)
    {

        $this->ensureJobs();

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
        return Craft::createObject($config);
    }

    /**
     * Ensure the jobs are all loaded
     *
     * @return $this
     */
    protected function ensureJobs()
    {

        if (null === $this->jobs) {

            $this->jobs = [];

            // Trigger en event
            $event = new RegisterJobs();

            $this->trigger(
                RegisterJobs::EVENT_REGISTER_JOBS,
                $event
            );

            $this->addJobs($event->jobs);

        }

        return $this;

    }

}
