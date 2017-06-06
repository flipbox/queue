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

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface StrategyInterface
{
    /**
     * Additional header for the job.
     */
    const HEADER_MULTIPLE_QUEUE_INDEX = 'MultipleQueueIndex';

    /**
     * @param MultipleQueueInterface $queue
     * @return mixed
     */
    public function setQueue(MultipleQueueInterface $queue);

    /**
     * Returns the job.
     *
     * @return JobInterface|boolean
     */
    public function fetch();

    /**
     * Delete the job from the queue.
     *
     * @param JobInterface $job
     * @return bool
     */
    public function delete(JobInterface $job);
}
