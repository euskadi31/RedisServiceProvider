# Silex Redis Service Provider

[![Build Status](https://travis-ci.org/euskadi31/RedisServiceProvider.svg?branch=master)](https://travis-ci.org/euskadi31/RedisServiceProvider)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2180e654-edaa-474c-95fe-9239d0cc7b00/mini.png)](https://insight.sensiolabs.com/projects/2180e654-edaa-474c-95fe-9239d0cc7b00)


## Install

Add `euskadi31/redis-service-provider` to your `composer.json`:

    % php composer.phar require euskadi31/redis-service-provider:~1.0

## Usage

### Configuration

```php
<?php

$app = new Silex\Application;

$app->register(new \Euskadi31\Silex\Provider\RedisServiceProvider);
```

## License

RedisServiceProvider is licensed under [the MIT license](LICENSE.md).
