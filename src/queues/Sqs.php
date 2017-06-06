<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\queues;

use Aws\Sqs\SqsClient;
use flipbox\queue\jobs\JobInterface;
use yii\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Sqs extends AbstractQueue
{

    /**
     * The SQS url.
     *
     * @var string
     */
    public $url;

    /**
     * The config for SqsClient.
     *
     * This will be used for SqsClient::factory($config);
     * @var array
     */
    public $config = [];

    /**
     * Due to ability of the queue message to be visible automatically after
     * a certain of time, this is not required.
     *
     * @inheritdoc
     */
    public $releaseOnFailure = false;

    /**
     * Stores the SQS client.
     * @var \Aws\Sqs\SqsClient
     */
    private $client;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->client = new SqsClient($this->config);
    }

    /**
     * @inheritdoc
     */
    public function fetchJob()
    {
        $message = $this->client->receiveMessage([
            'QueueUrl' => $this->url,
            'AttributeNames' => ['ApproximateReceiveCount'],
            'MaxNumberOfMessages' => 1,
        ]);
        if (isset($message['Messages']) && count($message['Messages']) > 0) {
            return $this->createJobFromMessage($message['Messages'][0]);
        } else {
            return false;
        }
    }

    /**
     * Create job from SQS message.
     *
     * @param array $message
     * @return JobInterface
     */
    private function createJobFromMessage($message)
    {
        $job = $this->deserialize($message['Body']);
        $job->setHeader('ReceiptHandle', $message['ReceiptHandle']);
        $job->setId($message['MessageId']);
        return $job;
    }

    /**
     * @inheritdoc
     */
    public function postJob(JobInterface $job): bool
    {
        $model = $this->client->sendMessage([
            'QueueUrl' => $this->url,
            'MessageBody' => $this->serialize($job),
        ]);
        if ($model !== null) {
            $job->setId($model['MessageId']);
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function deleteJob(JobInterface $job): bool
    {
        $receiptHandle = $job->getHeader('ReceiptHandle');

        if (!empty($receiptHandle)) {
            $response = $this->client->deleteMessage([
                'QueueUrl' => $this->url,
                'ReceiptHandle' => $receiptHandle,
            ]);

            return $response !== null;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function releaseJob(JobInterface $job): bool
    {
        $receiptHandle = $job->getHeader('ReceiptHandle');

        if (!empty($receiptHandle)) {
            $response = $this->client->changeMessageVisibility([
                'QueueUrl' => $this->url,
                'ReceiptHandle' => $receiptHandle,
                'VisibilityTimeout' => 0,
            ]);

            return $response !== null;
        }

        return false;
    }

    /**
     * Returns the SQS client used.
     *
     * @return \Aws\Sqs\SqsClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @inheritdoc
     */
    public function getSize(): int
    {
        $response = $this->getClient()->getQueueAttributes([
            'QueueUrl' => $this->url,
            'AttributeNames' => [
                'ApproximateNumberOfMessages'
            ]
        ]);
        $attributes = $response->get('Attributes');
        return ArrayHelper::getValue(
            $attributes,
            'ApproximateNumberOfMessages',
            0
        );
    }

    /**
     * @inheritdoc
     */
    public function purge(): bool
    {
        $response = $this->getClient()->getQueueAttributes([
            'QueueUrl' => $this->url,
        ]);
        return $response !== null;
    }
}
