<?php

namespace flipbox\queue\actions;

use Craft;
use flipbox\queue\actions\jobs\AbstractJob;
use UrbanIndo\Yii2\Queue\Queues\MultipleQueue as MultipleQueueQueue;
use yii\di\Instance;
use yii\web\ServerErrorHttpException;

/**
 * @property MultipleQueueQueue $queue
 */
class MultipleQueue extends AbstractJob
{
    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->queue = Instance::ensure($this->queue, MultipleQueueQueue::class);
    }

    /**
     * @return array
     * @throws ServerErrorHttpException When failed to post.
     */
    public function performAction()
    {
        $job = $this->createJobFromRequest();

        $index = Craft::$app->getRequest()->post('index');
        if (!isset($index)) {
            $this->handleRequiredIndexException();
        }

        if ($this->queue->postToQueue($job, $index)) {
            return ['status' => 'okay', 'jobId' => $job->id];
        } else {
            return $this->handleFailedToPostJobException();
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    protected function handleRequiredIndexException()
    {
        throw new \InvalidArgumentException('Index needed');
    }
}
