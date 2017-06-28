<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\options;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Options implements OptionsInterface, \JsonSerializable
{

    private $delay = 0;

    /**
     * @inheritdoc
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * @inheritdoc
     */
    public function setDelay(int $delay)
    {
        $this->delay = $delay;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toConfig(): array
    {
        return [
            'delay' => $this->getDelay()
        ];
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->toConfig();
    }
}
