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
interface OptionsInterface
{
    /**
     * @return int
     */
    public function getDelay(): int;

    /**
     * @param int $delay
     * @return static
     */
    public function setDelay(int $delay);

    /**
     * Create a Yii config for the options
     *
     * @return array
     */
    public function toConfig(): array;
}
