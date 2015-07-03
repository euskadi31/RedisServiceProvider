<?php
/*
 * This file is part of the RedisServiceProvider.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Euskadi31\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

/**
 * Redis integration for Silex.
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class RedisServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Container $app)
    {
        $app['redis.options'] = [
            'type'      => 'redis',
            'server'    => [
                'host' => '127.0.0.1',
                'port' => 6379
            ],
            'sentinels' => [],
            'client'    => [
                'redis'     => [
                    'retry'     => 0,
                    'interval'  => 1000,
                    'auth'      => null,
                    'namespace' => null,
                    'db'        => 0,
                    'timeout'   => 1
                ],
                'sentinel'  => [
                    'auth'      => null,
                    'namespace' => null,
                    'db'        => 0,
                    'timeout'   => 0.5
                ]
            ]
        ];

        $app['redis.manager.factory'] = $app->protect(function($options) use ($app) {
            return function() use ($options, $app) {
                $manager = new Redis\RedisManager($options);

                if (isset($app['logger']) && !empty($app['logger'])) {
                    $manager->setLogger($app['logger']);
                }

                return $manager;
            };
        });

        $app['redis'] = function($app) {
            $redis = $app['redis.manager.factory']($app['redis.options']);

            return $redis()->getRedis();
        };
    }
}
