<?php

namespace flipbox\queue\actions\jobs;

use Craft;
use craft\helpers\Json;
use flipbox\queue\models\Settings as SettingsModel;
use UrbanIndo\Yii2\Queue\Job;
use UrbanIndo\Yii2\Queue\Queue;
use yii\base\Action;
use yii\di\Instance;
use yii\web\ServerErrorHttpException;

/**
 * @method SettingsModel getSettings()
 */
abstract class AbstractJob extends Action
{
    /**
     * The queue to process.
     * @var string|array|\UrbanIndo\Yii2\Queue\Queue
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
        $response = Craft::$app->getResponse();
        $response->format = $response::FORMAT_JSON;
        $this->queue = Instance::ensure($this->queue, Queue::class);
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
     * @return Job
     * @throws \yii\web\ServerErrorHttpException When malformed request.
     */
    protected function createJobFromRequest()
    {
        $request = Craft::$app->getRequest();

        $route = $request->getBodyParam('route');
        $data = $request->getBodyParam('data', []);

        if (empty($route)) {
            throw new ServerErrorHttpException('Failed to post job');
        }

        if (is_string($data)) {
            $data = Json::decode($data);
        }

        return new Job([
            'route' => $route,
            'data' => $data
        ]);
    }

    /**
     * @throws ServerErrorHttpException
     */
    protected function handleFailedToPostJobException()
    {
        throw new ServerErrorHttpException('Failed to post job');
    }
}
