<?php

namespace flipbox\queue\jobs\traits;

use flipbox\queue\events\RegisterJobs;
use yii\base\Event;

trait EventCollectionTrait
{
    use CollectionTrait {
        addJob as parentAddJob;
        addJobs as parentAddJobs;
        findJob as parentFindJob;
        getJobs as parentGetJobs;
    }

    /**
     * @param $name
     * @param Event|null $event
     * @return mixed
     */
    public abstract function trigger($name, Event $event = null);

    /**
     * @inheritdoc
     */
    protected function addJobs(array $jobs = [])
    {
        $this->ensureJobs();
        return $this->parentAddJobs($jobs);
    }

    /**
     * @inheritdoc
     */
    public function addJob($key, $job)
    {
        $this->ensureJobs();
        return $this->parentAddJob($key, $job);
    }

    /**
     * @inheritdoc
     */
    public function getJobs()
    {
        $this->ensureJobs();
        return $this->parentGetJobs();
    }

    /**
     * @inheritdoc
     */
    public function findJob($identifier)
    {
        $this->ensureJobs();
        return $this->parentFindJob($identifier);
    }

    /**
     * Ensure the jobs are all loaded
     *
     * @return static
     */
    protected function ensureJobs()
    {
        if (null === $this->jobs) {
            $this->jobs = [];

            $this->trigger(
                RegisterJobs::EVENT_REGISTER_JOBS,
                $event = new RegisterJobs()
            );

            $this->addJobs($event->jobs);
        }

        return $this;
    }
}
