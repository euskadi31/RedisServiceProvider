<?php
/**
 * @package     Redis
 * @author      Axel Etcheverry <axel@etcheverry.biz>
 * @copyright   Copyright (c) 2014 Axel Etcheverry (https://twitter.com/euskadi31)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @namespace
 */
namespace Redis\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Redis;

class RedisServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app["redis"] = $app->share(function() use ($app) {

            $host       = "127.0.0.1";
            $port       = 6379;
            $timeout    = 0;
            $persistent = false;
            $auth       = null;
            $select     = null;

            if (isset($app["redis.host"]) && !empty($app["redis.host"])) {
                $host = $app["redis.host"];
            }

            if (
                isset($app["redis.port"]) &&
                !empty($app["redis.host"]) &&
                is_int($app["redis.port"])
            ) {
                $port = $app["redis.port"];
            }

            if (
                isset($app["redis.timeout"]) &&
                !empty($app["redis.host"]) &&
                is_int($app["redis.timeout"])
            ) {
                $timeout = $app["redis.timeout"];
            }

            if (isset($app["redis.persistent"])) {
                $persistent = (bool)$app["redis.persistent"];
            }

            if (isset($app["redis.auth"]) && !empty($app["redis.auth"])) {
                $auth = $app["redis.auth"];
            }

            if (
                isset($app["redis.select"]) &&
                !empty($app["redis.select"]) &&
                is_int($app["redis.select"])
            ) {
                $select = $app["redis.select"];
            }

            $redis = new Redis();

             if ($persistent) {
                $redis->pconnect($host, $port, $timeout);
            } else {
                $redis->connect($host, $port, $timeout);
            }

            if (!empty($auth)) {
                $redis->auth($auth);
            }

            if (!is_null($select)) {
                $redis->select($select);
            }

            return $redis;
        });

    }

    public function boot(Application $app)
    {
        $app->finish(function() use ($app) {

            if (isset($app["redis.persistent"])) {
                $persistent = (bool)$app["redis.persistent"];
            }

            if (!$persistent) {
                $app["redis"]->close();
            }
        });
    }
}
