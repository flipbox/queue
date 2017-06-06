<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\strategies;

use flipbox\queue\jobs\JobInterface;
use flipbox\queue\queues\MultipleQueueInterface;
use yii\base\Object;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class Strategy extends Object implements StrategyInterface
{
    /**
     * @var MultipleQueueInterface
     */
    protected $queue;

    /**
     * Sets the queue.
     *
     * @param MultipleQueueInterface $queue
     * @return void
     */
    public function setQueue(MultipleQueueInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Implement this for the strategy of getting job from the queue.
     *
     * @return JobInterface[]|false
     */
    abstract protected function getJobFromQueues();

    /**
     * Returns the job.
     *
     * @return JobInterface|boolean
     */
    public function fetch()
    {
        $return = $this->getJobFromQueues();

        if ($return === false) {
            return false;
        }

        list($job, $index) = $return;

        $job->setHeader(self::HEADER_MULTIPLE_QUEUE_INDEX, $index);

        return $job;
    }

    /**
     * Delete the job from the queue.
     *
     * @param JobInterface $job The job.
     * @return boolean whether the operation succeed.
     */
    public function delete(JobInterface $job)
    {
        $index = $job->getHeader(self::HEADER_MULTIPLE_QUEUE_INDEX, null);

        if (!isset($index)) {
            return false;
        }

        $queue = $this->queue->getQueue($index);

        if (!isset($index)) {
            return false;
        }

        return $queue->delete($job);
    }
}
