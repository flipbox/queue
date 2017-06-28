<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\jobs;

use flipbox\queue\events\AfterJobRun;
use flipbox\queue\events\BeforeJobRun;
use flipbox\queue\jobs\traits\JobTrait;
use flipbox\queue\jobs\traits\OptionsTrait;
use flipbox\queue\Queue;
use flipbox\queue\queues\MultipleQueueInterface;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractJob extends Component implements JobInterface
{

    use JobTrait, OptionsTrait;

    /**
     * Event executed before a job is being executed.
     */
    const EVENT_BEFORE_RUN = 'beforeRun';

    /**
     * Event executed after a job is being executed.
     */
    const EVENT_AFTER_RUN = 'afterRun';

    /**
     * @return mixed
     */
    abstract protected function runInternal();

    /**
     * @param null $index
     * @return bool
     */
    public function toQueue($index = null): bool
    {
        $queue = Queue::getInstance()->getQueue();

        if($queue instanceof MultipleQueueInterface) {
            return $queue->postToQueue($this, $index);
        }

        return Queue::getInstance()->getQueue()->post($this);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // Before event
        if (!$this->beforeRun()) {
            return false;
        }

        // Run the actual job
        $value = $this->runInternal();

        // After event
        $this->afterRun($value);

        return $value;
    }

    /**
     * @return bool
     */
    protected function beforeRun()
    {
        $this->trigger(
            self::EVENT_BEFORE_RUN,
            $beforeEvent = new BeforeJobRun()
        );

        return $beforeEvent->isValid;
    }

    /**
     * @param $value
     */
    protected function afterRun($value)
    {
        $this->trigger(
            self::EVENT_BEFORE_RUN,
            $beforeEvent = new AfterJobRun([
                'value' => $value
            ])
        );
    }

    /**
     * @return array
     */
    public function toConfig(): array
    {
        return [
            'class' => get_class($this),
            'options' => $this->getOptions()->toConfig()
        ];
    }
}
