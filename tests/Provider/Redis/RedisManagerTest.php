<?php
/*
 * This file is part of the RedisServiceProvider.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Euskadi31\Silex\Provider\Redis;

if (!class_exists('RedisMasterDiscovery')) {
    require __DIR__ . '/RedisFallback.php';
}

use Euskadi31\Silex\Provider\Redis\RedisManager;
use RedisException;
use Redis;

class RedisManagerTest extends \PHPUnit_Framework_TestCase
{
    public function getRedisMock()
    {
        $redisMock = $this->getMock('Redis');

        return $redisMock;
    }

    public function testConstructorWithoutRedis()
    {
        $manager = new RedisManager([
            'type' => 'redis',
            'server' => [
                'host' => '127.0.0.1',
                'port' => 6379
            ],
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'retry' => 3,
                    'interval' => 1000
                ]
            ]
        ]);

        $this->assertInstanceOf('Redis', $manager->getRedis());
    }

    /**
     * @expectedException Euskadi31\Silex\Provider\Redis\RedisManagerException
     * @expectedExceptionMessage Redis is already connected
     * @codeCoverageIgnore
     */
    public function testConstructorWithRedisConnected()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->once())
            ->method('isConnected')
            ->will($this->returnValue(true));

        $manager = new RedisManager([
            'type' => 'redis',
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'retry' => 3,
                    'interval' => 1000
                ]
            ]
        ], $redisMock);
    }

    public function testConstructorWithRedisConf()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('127.0.0.1'), $this->equalTo(6379), $this->equalTo(1))
            ->will($this->returnValue(true));

        $manager = new RedisManager([
            'type' => 'redis',
            'server' => [
                'host' => '127.0.0.1',
                'port' => 6379
            ],
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'retry' => 3,
                    'interval' => 1000
                ]
            ]
        ], $redisMock);

        $this->assertInstanceOf('Redis', $manager->getRedis());
        $this->assertEquals($redisMock, $manager->getRedis());
    }

    public function _retry($redisMock)
    {
        $manager = new RedisManager([
            'type' => 'redis',
            'server' => [
                'host' => '127.0.0.1',
                'port' => 6379
            ],
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'retry' => 3,
                    'interval' => 1000
                ]
            ]
        ], $redisMock);

        $this->assertInstanceOf('Redis', $manager->getRedis());
        $this->assertEquals($redisMock, $manager->getRedis());
    }

    public function testConstructorWithRedisConfAndRetry()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->exactly(1))
            ->method('connect')
            ->with($this->equalTo('127.0.0.1'), $this->equalTo(6379), $this->equalTo(1))
            ->will($this->returnValue(true));
        $this->_retry($redisMock);
    }

    /**
     * @expectedException Euskadi31\Silex\Provider\Redis\RedisManagerException
     * @expectedExceptionMessage Connexion failed.
     * @codeCoverageIgnore
     */
    public function testRetryFail()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->exactly(3))
            ->method('connect')
            ->with($this->equalTo('127.0.0.1'), $this->equalTo(6379), $this->equalTo(1))
            ->will($this->returnValue(false));
        $this->_retry($redisMock);
    }

    public function testConstructorWithRedisConfSocket()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('/var/run/redis.sock'))
            ->will($this->returnValue(true));

        $manager = new RedisManager([
            'type' => 'redis',
            'server' => [
                'host' => '/var/run/redis.sock'
            ],
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'retry' => 3,
                    'interval' => 1000
                ]
            ]
        ], $redisMock);
    }

    public function testConstructorWithRedisConfAuth()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('/var/run/redis.sock'))
            ->will($this->returnValue(true));
        $redisMock->expects($this->once())
            ->method('auth')
            ->with($this->equalTo('password'));

        $manager = new RedisManager([
            'type' => 'redis',
            'server' => [
                'host' => '/var/run/redis.sock'
            ],
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'auth' => 'password',
                    'retry' => 3,
                    'interval' => 1000
                ]
            ]
        ], $redisMock);
    }

    public function testConstructorWithRedisConfDb()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('/var/run/redis.sock'))
            ->will($this->returnValue(true));
        $redisMock->expects($this->once())
            ->method('select')
            ->with($this->equalTo(4));

        $manager = new RedisManager([
            'type' => 'redis',
            'server' => [
                'host' => '/var/run/redis.sock'
            ],
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'db' => 4,
                    'retry' => 3,
                    'interval' => 1000
                ]
            ]
        ], $redisMock);
    }

    public function testConstructorWithRedisConfNamespace()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('/var/run/redis.sock'))
            ->will($this->returnValue(true));
        $redisMock->expects($this->once())
            ->method('setOption')
            ->with($this->equalTo(Redis::OPT_PREFIX), $this->equalTo('my:app:'));

        $manager = new RedisManager([
            'type' => 'redis',
            'server' => [
                'host' => '/var/run/redis.sock'
            ],
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'namespace' => 'my:app:',
                    'retry' => 3,
                    'interval' => 1000
                ]
            ]
        ], $redisMock);
    }

    /**
     * @expectedException Euskadi31\Silex\Provider\Redis\RedisManagerException
     * @expectedExceptionMessage The "server" property is required.
     * @codeCoverageIgnore
     */
    public function testConstructorWithBadRedisConf()
    {
        $redisMock = $this->getRedisMock();

        $manager = new RedisManager([
            'type' => 'redis',
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'retry' => 3,
                    'interval' => 1000
                ]
            ]
        ], $redisMock);
    }

    public function testConstructorWithSentinelConf()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('127.0.0.1'), $this->equalTo(6379), $this->equalTo(1))
            ->will($this->returnValue(true));

        $manager = new RedisManager([
            'type' => 'sentinel',
            'sentinels' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 26379
                ]
            ],
            'client' => [
                'redis' => [
                    'timeout' => 1,
                    'retry' => 3,
                    'interval' => 1000
                ],
                'sentinel' => [
                    'master' => 'mymaster',
                    'timeout' => 0.5
                ]
            ]
        ], $redisMock);

        $this->assertInstanceOf('Redis', $manager->getRedis());
        $this->assertEquals($redisMock, $manager->getRedis());

        $this->assertInstanceOf('RedisMasterDiscovery', $manager->getMasterDiscovery());
        $this->assertEquals(1, count($manager->getMasterDiscovery()->getSentinels()));
    }

    public function testConstructorWithSentinelAndRedisConf()
    {
        $redisMock = $this->getRedisMock();
        $redisMock->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('127.0.0.1'), $this->equalTo(6379), $this->equalTo(1))
            ->will($this->returnValue(true));

        $manager = new RedisManager([
            'type' => 'sentinel',
            'sentinels' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 26379
                ]
            ],
            'server' => [
                'host' => '/var/run/redis.sock'
            ],
            'client' => [
                'sentinel' => [
                    'master' => 'mymaster',
                    'timeout' => 0.5
                ],
                'redis' => [
                    'timeout' => 1,
                    'namespace' => 'my:app:',
                    'retry' => 3,
                    'interval' => 1000
                ]
            ],
        ], $redisMock);

        $this->assertInstanceOf('Redis', $manager->getRedis());
        $this->assertEquals($redisMock, $manager->getRedis());

        $this->assertInstanceOf('RedisMasterDiscovery', $manager->getMasterDiscovery());
        $this->assertEquals(1, count($manager->getMasterDiscovery()->getSentinels()));
    }

    /**
     * @expectedException Euskadi31\Silex\Provider\Redis\RedisManagerException
     * @expectedExceptionMessage The "sentinels" property is required.
     * @codeCoverageIgnore
     */
    public function testConstructorWithBadSentinelConf()
    {
        $redisMock = $this->getRedisMock();

        $manager = new RedisManager([
            'type' => 'sentinel',
            'client' => [
                'sentinel' => [
                    'master' => 'mymaster',
                    'timeout' => 0.5
                ]
            ]
        ], $redisMock);
    }

    /**
     * @expectedException Euskadi31\Silex\Provider\Redis\RedisManagerException
     * @expectedExceptionMessage Bad type config
     * @codeCoverageIgnore
     */
    public function testConstructorWithBadConf()
    {
        $redisMock = $this->getRedisMock();

        $manager = new RedisManager([
            'type' => 'bad_value'
        ], $redisMock);

        $manager = new RedisManager([], $redisMock);
    }
}
