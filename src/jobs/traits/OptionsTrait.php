<?php

/**
 * @author    Flipbox Factory
 * @copyright Copyright (c) 2017, Flipbox Digital
 * @link      https://github.com/flipbox/queue/releases/latest
 * @license   https://github.com/flipbox/queue/blob/master/LICENSE
 */

namespace flipbox\queue\jobs\traits;

use flipbox\queue\options\Options;
use flipbox\queue\options\OptionsInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait OptionsTrait
{
    /**
     * @var OptionsInterface
     */
    private $options;

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        if($this->options === null) {
            $this->options = new Options;
        }
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function setId($options)
    {
        if(!$options instanceof OptionsInterface) {
            $options = new Options($options);
        }
        $this->options = $options;
        return $this;
    }
}
