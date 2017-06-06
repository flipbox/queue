<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\queues;

use flipbox\queue\jobs\JobInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface MultipleQueueInterface extends QueueInterface
{
    /**
     * @return QueueInterface[];
     */
    public function getQueues();

    /**
     * @param $index
     * @return QueueInterface;
     */
    public function getQueue($index);

    /**
     * @param JobInterface $job
     * @param $index
     * @return bool
     */
    public function postToQueue(JobInterface $job, $index): bool;
}
