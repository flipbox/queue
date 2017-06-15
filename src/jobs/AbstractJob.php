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
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractJob extends Component implements JobInterface
{

    /**
     * Event executed before a job is being executed.
     */
    const EVENT_BEFORE_RUN = 'beforeRun';

    /**
     * Event executed after a job is being executed.
     */
    const EVENT_AFTER_RUN = 'afterRun';

    /**
     * The ID of the message. This should be set on the job receive.
     * @var integer
     */
    protected $id;

    /**
     * Stores the header.
     * This can be different for each queue provider.
     *
     * @var mixed
     */
    protected $header = [];

    /**
     * @return mixed
     */
    abstract protected function runInternal();

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
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHeader($item, $default = null)
    {
        return ArrayHelper::getValue($this->header, $item, $default);
    }

    /**
     * @inheritdoc
     */
    public function setHeader($item, $value)
    {
        $this->header[$item] = $value;
        return $this;
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
            'class' => get_class($this)
        ];
    }
}
