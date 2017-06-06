<?php

namespace flipbox\queue\jobs;

use Craft;

class HelloWorld extends AbstractCallable
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->route = $this->route();
    }

    /**
     *
     */
    public function route(): callable
    {
        $data = $this->data;
        return function () use ($data) {
            Craft::info('Hello world.');
        };
    }
}
