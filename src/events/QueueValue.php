<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\events;

use flipbox\queue\jobs\JobInterface;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueueValue extends Event
{
    /**
     * @var JobInterface
     */
    public $job;

    /**
     * The return value after a job is being executed.
     *
     * @var mixed
     */
    public $value;
}
