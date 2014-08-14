RedisServiceProvider for Silex
==============================

This is the [Redis](https://github.com/nicolasff/phpredis) service provider for the Silex microframework.

Installation
------------

[Composer](https://packagist.org/packages/euskadi31/redis-service-provider) is the easiest way to get the Service provider installed and running. Just add your composer.json the following:

~~~
"euskadi31/redis-service-provider": "dev-master"
~~~

Using Redis Service
-------------------

~~~php
require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Redis\Provider\RedisServiceProvider(), array(
    "redis.host"        => "127.0.0.1",
    "redis.port"        => 6379,
    "redis.timeout"     => 5,
    "redis.persistent"  => true,
    "redis.auth"        => "my password",
    "redis.select"      => 2
));

$app["redis"]->set("foo", "bar");
~~~

License
-------

RedisServiceProvider is licensed under [the MIT license](LICENSE.md).
