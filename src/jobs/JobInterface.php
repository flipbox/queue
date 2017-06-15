<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\jobs;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface JobInterface
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * @param int|string $id
     * @return static
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function run();

    /**
     * @param $item
     * @param null $default
     * @return mixed
     */
    public function getHeader($item, $default = null);

    /**
     * @param $item
     * @param $value
     */
    public function setHeader($item, $value);

    /**
     * Create a Yii config for this job
     *
     * @return array
     */
    public function toConfig(): array;
}
