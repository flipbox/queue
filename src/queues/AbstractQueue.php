<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\queues;

use flipbox\queue\events\Queue as QueueEvent;
use flipbox\queue\events\QueueValue as QueueValueEvent;
use flipbox\queue\jobs\JobInterface;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractQueue extends Component implements QueueInterface
{
    /**
     * Event executed before a job is posted to the queue.
     */
    const EVENT_BEFORE_POST = 'beforePost';

    /**
     * Event executed before a job is posted to the queue.
     */
    const EVENT_AFTER_POST = 'afterPost';

    /**
     * Event executed before a job is being fetched from the queue.
     */
    const EVENT_BEFORE_FETCH = 'beforeFetch';

    /**
     * Event executed after a job is being fetched from the queue.
     */
    const EVENT_AFTER_FETCH = 'afterFetch';

    /**
     * Event executed before a job is being deleted from the queue.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * Event executed after a job is being deleted from the queue.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';

    /**
     * Event executed before a job is being released from the queue.
     */
    const EVENT_BEFORE_RELEASE = 'beforeRelease';

    /**
     * Event executed after a job is being released from the queue.
     */
    const EVENT_AFTER_RELEASE = 'afterRelease';

    /**
     * Event executed before a job is being executed.
     */
    const EVENT_BEFORE_RUN = 'beforeRun';

    /**
     * Event executed after a job is being executed.
     */
    const EVENT_AFTER_RUN = 'afterRun';

    /**
     * This will release automatically on execution failure. i.e. when
     * the `run` method returns false or catch exception.
     *
     * @var bool
     */
    public $releaseOnFailure = true;

    /**
     * @inheritdoc
     */
    public function post(JobInterface $job): bool
    {
        $this->trigger(
            self::EVENT_BEFORE_POST,
            $beforeEvent = new QueueEvent(['job' => $job])
        );

        if (!$beforeEvent->isValid) {
            return false;
        }

        $return = $this->postJob($job);
        if (!$return) {
            return false;
        }

        $this->trigger(
            self::EVENT_AFTER_POST,
            new QueueEvent(['job' => $job])
        );
        return true;
    }

    /**
     * Post new job to the queue.  Override this for queue implementation.
     *
     * @param JobInterface $job
     * @return bool
     */
    abstract protected function postJob(JobInterface $job): bool;

    /**
     * @inheritdoc
     */
    public function fetch()
    {
        $this->trigger(self::EVENT_BEFORE_FETCH);

        $job = $this->fetchJob();
        if ($job == false) {
            return false;
        }

        $this->trigger(
            self::EVENT_AFTER_FETCH,
            new QueueEvent(['job' => $job])
        );
        return $job;
    }

    /**
     * Return next job from the queue. Override this for queue implementation.
     *
     * @return JobInterface|bool
     */
    abstract protected function fetchJob();

    /**
     * @inheritdoc
     */
    public function run(JobInterface $job)
    {
        $this->trigger(
            self::EVENT_BEFORE_RUN,
            $beforeEvent = new QueueEvent(['job' => $job])
        );

        if (!$beforeEvent->isValid) {
            return;
        }

        try {
            $value = $job->run();
        } catch (\Exception $e) {
            $class = get_class($job);

            Yii::error(
                "Fatal Error: Error running job '{$class}'. Id: {$job->getId()} Message: {$e->getMessage()}",
                'queue'
            );

            if ($this->releaseOnFailure) {
                $this->release($job);
            }

            throw new Exception(
                "Error running job '{$class}'. " .
                "Message: {$e->getMessage()}. " .
                "File: {$e->getFile()}[{$e->getLine()}]. Stack Trace: {$e->getTraceAsString()}",
                500
            );
        }

        $this->trigger(
            self::EVENT_AFTER_RUN,
            new QueueValueEvent(['job' => $job, 'value' => $value])
        );

        if ($value === false) {
            if ($this->releaseOnFailure) {
                $this->release($job);
            }

            return;
        }

        $this->delete($job);

        return;
    }

    /**
     * @inheritdoc
     */
    public function delete(JobInterface $job): bool
    {
        $this->trigger(
            self::EVENT_BEFORE_DELETE,
            $beforeEvent = new QueueEvent(['job' => $job])
        );
        if (!$beforeEvent->isValid) {
            return false;
        }

        $return = $this->deleteJob($job);
        if (!$return) {
            return false;
        }

        $this->trigger(
            self::EVENT_AFTER_DELETE,
            new QueueEvent(['job' => $job])
        );
        return true;
    }

    /**
     * Delete the job. Override this for the queue implementation.
     *
     * @param JobInterface $job
     * @return bool
     */
    abstract protected function deleteJob(JobInterface $job): bool;

    /**
     * @inheritdoc
     */
    public function release(JobInterface $job): bool
    {
        $this->trigger(
            self::EVENT_BEFORE_RELEASE,
            $beforeEvent = new QueueEvent(['job' => $job])
        );
        if (!$beforeEvent->isValid) {
            return false;
        }

        $return = $this->releaseJob($job);
        if (!$return) {
            return false;
        }

        $this->trigger(
            self::EVENT_AFTER_RELEASE,
            new QueueEvent(['job' => $job])
        );
        return true;
    }

    /**
     * Release the job. Override this for the queue implementation.
     *
     * @param JobInterface $job
     * @return bool
     */
    abstract protected function releaseJob(JobInterface $job): bool;

    /**
     * @param string $message
     * @return JobInterface
     * @throws Exception
     */
    protected function deserialize($message): JobInterface
    {
        // Deserialize
        $jobArray = Json::decode($message);

        // Create job
        $job = Yii::createObject($jobArray);
        if (!$job instanceof JobInterface) {
            throw new Exception('Invalid job');
        }

        return $job;
    }

    /**
     * @param JobInterface $job The job to serialize.
     * @return string JSON string.
     */
    protected function serialize(JobInterface $job)
    {
        $jobArray = ArrayHelper::toArray($job);
        $jobArray['class'] = get_class($job);
        return Json::encode($jobArray);
    }
}
