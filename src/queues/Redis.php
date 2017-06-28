<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\queues;

use flipbox\queue\jobs\JobInterface;
use yii\di\Instance;
use yii\helpers\Json;
use yii\redis\Connection;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Redis extends AbstractQueue
{
    /**
     * Stores the redis connection.
     * @var string|array|Connection
     */
    public $db = 'redis';

    /**
     * The name of the key to store the queue.
     * @var string
     */
    public $key = 'queue';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
    }

    /**
     * @inheritdoc
     */
    public function deleteJob(JobInterface $job): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function fetchJob()
    {
        $json = $this->db->lpop($this->key);
        if ($json == false) {
            return false;
        }
        $data = Json::decode($json);
        $job = $this->deserialize($data['data']);
        $job->setId($data['id']);
        $job->setHeader('serialized', $data['data']);
        return $job;
    }

    /**
     * @inheritdoc
     */
    protected function postJob(JobInterface $job, array $options = []): bool
    {
        return $this->db->rpush($this->key, Json::encode([
            'id' => uniqid('queue_', true),
            'data' => $this->serialize($job),
        ]));
    }

    /**
     * @inheritdoc
     */
    protected function releaseJob(JobInterface $job): bool
    {
        return $this->db->rpush(
            $this->key,
            Json::encode([
                'id' => $job->getId(),
                'data' => $job->getHeader('serialized')
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getSize(): int
    {
        return $this->db->llen($this->key);
    }

    /**
     * @inheritdoc
     */
    public function purge(): bool
    {
        return $this->db->del($this->key);
    }
}
