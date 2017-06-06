# Queue system for Craft CMS
[![Latest Version](https://img.shields.io/github/release/flipbox/queue.svg?style=flat-square)](https://github.com/flipbox/queue/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/flipbox/queue/master.svg?style=flat-square)](https://travis-ci.org/flipbox/queue)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/flipbox/queue.svg?style=flat-square)](https://scrutinizer-ci.com/g/flipbox/queue/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/flipbox/queue.svg?style=flat-square)](https://scrutinizer-ci.com/g/flipbox/queue)
[![Total Downloads](https://img.shields.io/packagist/dt/flipboxdigital/queue.svg?style=flat-square)](https://packagist.org/packages/flipbox/queue)

This package provides a robust queue system for [Craft CMS](https://craftcms.com).

## Installation

To install, use composer:
```bash
composer require flipboxdigital/queue
```

In the Craft Control Panel, go to Settings → Plugins and click the "Install" button for **Queue**.

## Usage
By default, multiple queues are supported.  In order to add a new queue, register it via an event in your `Plugin::init()`:
```php 
yii\base\Event::on(
    Flipbox\Yii2\Queue\Queues\MultipleQueue::class,
    Flipbox\Yii2\Queue\Queues\MultipleQueue::EVENT_REGISTER_QUEUES,
    function(Flipbox\Yii2\Queue\Events\RegisterQueues $event) {
        $event->queues[] = [
            'class' => UrbanIndo\Yii2\Queue\Queues\SqsQueue::class,
            'module' => 'YOUR_PLUGIN_ID',
            'url' => 'https://sqs.us-west-2.amazonaws.com/1234567890/xxxxxx',
            'config' => [
                'region' => 'us-west-2',
                'version' => 'latest'
            ]
        ];
    }
);
```

To post a new job:
```php
$job = new UrbanIndo\Yii2\Queue\Job([
    'route' => function() {
        Craft::info("Hello world.");
    }
]);

Queue::getInstance()->getQueue()->post($job);
```

To run a job:
```php
$job = Queue::getInstance()->getQueue()->fetch();

if ($job) {
    Queue::getInstance()->getQueue()->run($job);
}
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/flipbox/queue/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Flipbox Digital](https://github.com/flipbox)

## License

The MIT License (MIT). Please see [License File](https://github.com/flipbox/queue/blob/master/LICENSE) for more information.
