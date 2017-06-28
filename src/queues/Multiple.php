<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\queues;

use flipbox\queue\jobs\JobInterface;
use flipbox\queue\strategies\Random;
use flipbox\queue\strategies\StrategyInterface;
use Craft;
use yii\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Multiple extends AbstractQueue implements MultipleQueueInterface
{
    /**
     * @var QueueInterface[]
     */
    public $queues = [];

    /**
     * @var StrategyInterface
     */
    public $strategy = ['class' => Random::class];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Add queues
        foreach ($this->queues as $id => $queue) {
            $this->queues[$id] = Craft::createObject($queue);
        }

        if (!$this->strategy instanceof StrategyInterface) {
            $this->strategy = Craft::createObject($this->strategy);
        }
        $this->strategy->setQueue($this);
    }

    /**
     * @inheritdoc
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * @inheritdoc
     */
    public function getQueue($index)
    {
        return ArrayHelper::getValue($this->queues, $index);
    }

    /**
     * @inheritdoc
     */
    protected function deleteJob(JobInterface $job): bool
    {
        return $this->strategy->delete($job);
    }

    /**
     * @inheritdoc
     */
    protected function fetchJob()
    {
        return $this->strategy->fetch();
    }

    /**
     * @inheritdoc
     */
    protected function postJob(JobInterface $job, array $options = []): bool
    {
        reset($this->queues);
        return $this->postToQueue($job, key($this->queues));
    }

    /**
     * Post new job to a specific queue.
     * @param JobInterface $job
     * @param integer $index
     * @return bool
     */
    public function postToQueue(JobInterface $job, $index): bool
    {
        $queue = $this->getQueue($index);
        if ($queue === null) {
            return false;
        }
        return $queue->post($job);
    }

    /**
     * @inheritdoc
     */
    protected function releaseJob(JobInterface $job): bool
    {
        $queue = $this->getQueue(
            $job->getHeader(StrategyInterface::HEADER_MULTIPLE_QUEUE_INDEX)
        );
        return $queue->release($job);
    }

    /**
     * @inheritdoc
     */
    public function getSize(): int
    {
        return array_sum(array_map(function (QueueInterface $queue) {
            return $queue->getSize();
        }, $this->queues));
    }

    /**
     * @inheritdoc
     */
    public function purge(): bool
    {
        foreach ($this->queues as $queue) {
            $queue->purge();
        }
        return true;
    }
}
