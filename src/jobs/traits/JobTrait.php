<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\jobs\traits;

use yii\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait JobTrait
{
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
}
