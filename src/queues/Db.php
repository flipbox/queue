<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\queues;

use flipbox\queue\jobs\JobInterface;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\di\Instance;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Db extends AbstractQueue
{
    /**
     * Status when the job is ready.
     */
    const STATUS_READY = 0;

    /**
     * Status when the job is running by the worker.
     */
    const STATUS_ACTIVE = 1;

    /**
     * Status when the job is deleted.
     */
    const STATUS_DELETED = 2;

    /**
     * The database used for the queue.
     *
     * This will use default `db` component from Craft application.
     * @var string|\yii\db\Connection
     */
    public $db = 'db';

    /**
     * The name of the table to store the queue.
     *
     * The table should be pre-created as follows for MySQL:
     *
     * ```php
     * CREATE TABLE queue (
     *     id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
     *     status TINYINT NOT NULL DEFAULT 0,
     *     timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
     *     data LONGBLOB
     * );
     * ```
     * @var string
     */
    public $tableName = '{{%queue}}';

    /**
     * Whether to do hard delete of the deleted job, instead of just flagging the
     * status.
     * @var boolean
     */
    public $hardDelete = true;

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
    protected function fetchJob()
    {
        //Avoiding multiple job.
        $transaction = $this->db->beginTransaction();
        $row = $this->fetchLatestRow();
        if ($row == false || !$this->flagRunningRow($row)) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();

        $job = $this->deserialize($row['data']);
        $job->setId($row['id']);
        $job->setHeader('timestamp', $row['timestamp']);

        return $job;
    }

    /**
     * Fetch latest ready job from the table.
     *
     * Due to the use of AUTO_INCREMENT ID, this will fetch the job with the
     * largest ID.
     *
     * @return array
     */
    protected function fetchLatestRow()
    {
        return (new Query())
            ->select('*')
            ->from($this->tableName)
            ->where(['status' => self::STATUS_READY])
            ->orderBy(['id' => SORT_ASC])
            ->limit(1)
            ->one($this->db);
    }

    /**
     * Flag a row as running. This will update the row ID and status if ready.
     *
     * @param array $row The row to update.
     * @return boolean Whether successful or not.
     */
    protected function flagRunningRow(array $row)
    {
        $updated = $this->db->createCommand()
            ->update(
                $this->tableName,
                ['status' => self::STATUS_ACTIVE],
                [
                    'id' => $row['id'],
                    'status' => self::STATUS_READY,
                ]
            )->execute();
        return $updated == 1;
    }

    /**
     * @inheritdoc
     */
    protected function postJob(JobInterface $job, array $options = []): bool
    {
        return $this->db->createCommand()->insert(
            $this->tableName,
            [
                'timestamp' => new Expression('NOW()'),
                'data' => $this->serialize($job),
            ]
        )->execute() == 1;
    }

    /**
     * @inheritdoc
     */
    public function deleteJob(JobInterface $job): bool
    {
        if ($this->hardDelete) {
            return $this->db->createCommand()->delete(
                $this->tableName,
                ['id' => $job->getId()]
            )->execute() == 1;
        } else {
            return $this->db->createCommand()->update(
                $this->tableName,
                ['status' => self::STATUS_DELETED],
                ['id' => $job->getId()]
            )->execute() == 1;
        }
    }

    /**
     * @inheritdoc
     */
    public function releaseJob(JobInterface $job): bool
    {
        return $this->db->createCommand()->update(
            $this->tableName,
            ['status' => self::STATUS_READY],
            ['id' => $job->getId()]
        )->execute() == 1;
    }

    /**
     * @inheritdoc
     */
    public function getSize(): int
    {
        return (new Query())
            ->select('*')
            ->from($this->tableName)
            ->where(['status' => self::STATUS_READY])
            ->count('*', $this->db);
    }

    /**
     * @inheritdoc
     */
    public function purge(): bool
    {
        return false;
    }
}
