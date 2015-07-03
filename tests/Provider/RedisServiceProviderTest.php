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

use Euskadi31\Silex\Provider\RedisServiceProvider;
use Silex\Application;

class RedisServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $app = new Application;

        $loggerMock = $this->getMock('Psr\Log\LoggerInterface');

        $app['logger'] = function() use ($loggerMock) {
            return $loggerMock;
        };

        $app->register(new RedisServiceProvider);

        $this->assertTrue(isset($app['redis.options']));
        $this->assertEquals([
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
        ], $app['redis.options']);

        $manager = $app['redis.manager.factory']($app['redis.options']);

        $this->assertInstanceOf('Euskadi31\Silex\Provider\Redis\RedisManager', $manager());

        $redis = $app['redis'];

        $this->assertEquals($redis, $app['redis']);
    }
}
