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
class Dummy extends AbstractQueue
{
    /**
     * @inheritdoc
     */
    protected function postJob(JobInterface $job, array $options = []): bool
    {
        $this->run($job);
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function fetchJob()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function deleteJob(JobInterface $job): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function releaseJob(JobInterface $job): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getSize(): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function purge(): bool
    {
        return true;
    }
}
