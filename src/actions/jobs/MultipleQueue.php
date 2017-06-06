<?php

namespace flipbox\queue\actions;

use flipbox\queue\actions\jobs\AbstractJob;
use flipbox\queue\queues\MultipleQueueInterface;
use Yii;
use yii\di\Instance;
use yii\web\ServerErrorHttpException;

/**
 * @property MultipleQueueInterface $queue
 */
class MultipleQueue extends AbstractJob
{
    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->queue = Instance::ensure($this->queue, MultipleQueueInterface::class);
    }

    /**
     * @return array
     * @throws ServerErrorHttpException When failed to post.
     */
    public function performAction()
    {
        $job = $this->createJobFromRequest();

        $index = Yii::$app->getRequest()->post('index');
        if (!isset($index)) {
            $this->handleRequiredIndexException();
        }

        if ($this->queue->postToQueue($job, $index)) {
            return $this->transformSuccessResponse($job);
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
