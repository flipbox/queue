<?php

namespace flipbox\queue\actions\jobs;

use flipbox\queue\jobs\JobInterface;
use flipbox\queue\queues\QueueInterface;
use Yii;
use yii\base\Action;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

abstract class AbstractJob extends Action
{
    /**
     * @var QueueInterface
     */
    public $queue = 'queue';

    /**
     * @var callable
     */
    public $checkAccess;

    /**
     * @return array
     */
    abstract protected function performAction();

    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        $response = Yii::$app->getResponse();
        $response->format = $response::FORMAT_JSON;
        $this->queue = Instance::ensure($this->queue, QueueInterface::class);
    }

    /**
     * Endpoint to post a job to queue.
     * @return mixed
     * @throws \yii\web\ServerErrorHttpException When failed to post.
     */
    public function run()
    {
        // Check access
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        return $this->performAction();
    }

    /**
     * @param JobInterface $job
     * @return array
     */
    protected function transformSuccessResponse(JobInterface $job): array
    {
        return ArrayHelper::toArray($job);
    }

    /**
     * @return JobInterface
     * @throws \yii\web\ServerErrorHttpException When malformed request.
     */
    protected function createJobFromRequest()
    {
        $request = Yii::$app->getRequest();

        $jobArray = $request->getBodyParam('properties', []);
        $jobArray['class'] = $request->getBodyParam('class');

        $job = Yii::createObject($jobArray);
        if (!$job instanceof JobInterface) {
            throw new ServerErrorHttpException('Invalid job');
        }

        return $job;
    }

    /**
     * @throws ServerErrorHttpException
     */
    protected function handleFailedToPostJobException()
    {
        throw new ServerErrorHttpException('Failed to post job');
    }
}
