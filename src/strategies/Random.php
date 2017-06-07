<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\strategies;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Random extends AbstractStrategy
{
    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        srand();
    }

    /**
     * The number of attempt before returning false.
     *
     * @var int
     */
    public $maxAttempt = 5;

    /**
     * @inheritdoc
     */
    protected function getJobFromQueues()
    {
        $attempt = 0;
        $queues = $this->queue->getQueues();
        $count = count($queues);
        $keys = array_keys($queues);
        while ($attempt < $this->maxAttempt) {
            $index = rand(0, $count - 1);
            $key = $keys[$index];
            $queue = $this->queue->getQueue($key);
            $job = $queue->fetch();
            if ($job !== false) {
                return [$job, $key];
            }
            $attempt++;
        }
        return false;
    }
}
