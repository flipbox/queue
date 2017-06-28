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
interface QueueInterface
{

    /**
     * Post new job to the queue. This should trigger event EVENT_BEFORE_POST and
     * EVENT_AFTER_POST.
     *
     * @param JobInterface $job
     * @return bool
     */
    public function post(JobInterface $job, array $options =[]): bool;

    /**
     * Return next job from the queue. This should trigger event EVENT_BEFORE_FETCH
     * and event EVENT_AFTER_FETCH
     *
     * @return JobInterface|boolean the job or false if not found.
     */
    public function fetch();

    /**
     * Run the job. This should trigger event EVENT_BEFORE_RUN
     * and event EVENT_AFTER_RUN
     *
     * @param JobInterface $job
     * @return void
     * @throws \yii\base\Exception Exception.
     */
    public function run(JobInterface $job);

    /**
     * Delete the job. This should trigger event EVENT_BEFORE_DELETE and
     * EVENT_AFTER_DELETE.
     *
     * @param JobInterface $job
     * @return bool
     */
    public function delete(JobInterface $job): bool;

    /**
     * Release the job. This should trigger event EVENT_BEFORE_RELEASE and
     * EVENT_AFTER_RELEASE.
     *
     * @param JobInterface $job
     * @return bool
     */
    public function release(JobInterface $job): bool;

    /**
     * Returns the number of queue size.
     *
     * @return int
     */
    public function getSize(): int;

    /**
     * Purge the whole queue.
     *
     * @return bool
     */
    public function purge(): bool;
}
