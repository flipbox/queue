<?php

namespace flipbox\queue\actions;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use Exception;
use flipbox\queue\models\Settings as SettingsModel;
use flipbox\queue\Queue;
use SuperClosure\Serializer;
use UrbanIndo\Yii2\Queue\Job;
use yii\base\Action;

/**
 * @method SettingsModel getSettings()
 */
class SQS extends Action
{

    /**
     * @var callable
     */
    public $checkAccess;

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
     * Run the action
     */
    public function performAction()
    {
        $body = Craft::$app->getRequest()->getBodyParams();

        if (!isset($body['Messages']) || count($body['Messages']) <= 0) {
            return;
        }

        // Run the job
        Queue::getInstance()->getQueue()->run(
            $this->createJobFromMessage($body['Messages'][0])
        );
    }

    /**
     * Create job from SQS message.
     *
     * @param array $message The message.
     * @return \UrbanIndo\Yii2\Queue\Job
     */
    private function createJobFromMessage(array $message)
    {
        $job = $this->deserialize($message['Body']);
        $job->header['ReceiptHandle'] = $message['ReceiptHandle'];
        $job->id = $message['MessageId'];
        return $job;
    }

    /**
     * Deserialize job to be executed.
     *
     * @param string $message The json string.
     * @return Job The job.
     * @throws \yii\base\Exception If there is no route detected.
     */
    protected function deserialize($message)
    {
        $job = $this->deserializeMessage($message);
        if (!isset($job['route'])) {
            throw new \yii\base\Exception('No route detected');
        }
        $route = $job['route'];
        $signature = [];
        if (isset($job['type']) && $job['type'] == Job::TYPE_CALLABLE) {
            $serializer = new Serializer();
            $signature['route'] = $route;
            $route = $serializer->unserialize($route);
        }
        $data = ArrayHelper::getValue($job, 'data', []);
        $obj = new Job([
            'route' => $route,
            'data' => $data,
        ]);
        $obj->header['signature'] = $signature;
        return $obj;
    }

    /**
     * @param string $array The message to be deserialize.
     * @return array
     * @throws Exception Exception.
     */
    protected function deserializeMessage($array)
    {
        $queue = Queue::getInstance()->getQueue();

        switch ($queue->serializer) {
            case $queue::SERIALIZER_PHP:
                $data = unserialize($array);
                break;
            case $queue::SERIALIZER_JSON:
                $data = Json::decode($array);
                break;
        }
        if (empty($data)) {
            throw new Exception('Can not deserialize message');
        }
        return $data;
    }
}
