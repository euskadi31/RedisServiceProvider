# Silex Redis Service Provider

[![Build Status](https://travis-ci.org/euskadi31/RedisServiceProvider.svg?branch=master)](https://travis-ci.org/euskadi31/RedisServiceProvider)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2180e654-edaa-474c-95fe-9239d0cc7b00/mini.png)](https://insight.sensiolabs.com/projects/2180e654-edaa-474c-95fe-9239d0cc7b00)


## Install

Add `euskadi31/redis-service-provider` to your `composer.json`:

    % php composer.phar require euskadi31/redis-service-provider:~2.0

## Usage

### Configuration

```php
<?php

$app = new Silex\Application;

$app->register(new \Euskadi31\Silex\Provider\RedisServiceProvider(
    [
        'type'      => 'redis',
        'server'    => [
            'host' => '10.0.0.1',
            'port' => 9999
        ],
        'client'    => [
            'redis'     => [
                'auth'      => 'helloimredis',
                'namespace' => 'silextwo',
            ]
        ]
]));
```

## License

RedisServiceProvider is licensed under [the MIT license](LICENSE.md).
