<?php

namespace flipbox\queue\events;

use flipbox\queue\jobs\JobInterface;
use yii\base\Event;

class RegisterJobs extends Event
{
    /**
     *  The event to register jobs
     */
    const EVENT_REGISTER_JOBS = 'registerJobs';

    /**
     * @var JobInterface[]
     */
    public $jobs = [];
}
