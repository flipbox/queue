<?php

namespace flipbox\queue\models;

use Flipbox\Yii2\Queue\Queues\MultipleQueue;
use yii\base\Model;

class Settings extends Model
{
    /**
     * @var array
     */
    public $component = [
        'class' => MultipleQueue::class,
        'module' => 'queue'
    ];
}
