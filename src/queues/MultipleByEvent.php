<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\queues;

use flipbox\queue\events\RegisterQueues;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class MultipleByEvent extends Multiple
{
    /**
     * The register queues event name
     */
    const EVENT_REGISTER_QUEUES = 'registerQueues';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $event = new RegisterQueues(
            $this->queues
        );
        $this->trigger(
            self::EVENT_REGISTER_QUEUES,
            $event
        );
        $this->queues = $event->queues;
        parent::init();
    }
}
