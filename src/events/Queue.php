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
class Queue extends Event
{
    /**
     * @var JobInterface
     */
    public $job;

    /**
     * Whether the next process should continue or not.
     * @var boolean
     */
    public $isValid = true;
}
