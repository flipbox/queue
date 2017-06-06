<?php

namespace flipbox\queue\models;

use flipbox\queue\queues\MultipleByEvent;
use yii\base\Model;

class Settings extends Model
{
    /**
     * @var array
     */
    public $component = [
        'class' => MultipleByEvent::class
    ];
}
