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

use Redis;
use RedisMasterDiscovery;
use RedisSentinel;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

/**
 * Redis Manager.
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
class RedisManager implements RedisManagerInterface, LoggerAwareInterface
{
    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @var Discovery
     */
    protected $discovery;

    use LogAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $config, Redis $redis = null)
    {
        if (is_null($redis)) {
            $redis = new Redis();
        }

        $this->redis = $redis;

        if ($redis->isConnected()) {
            throw new RedisManagerException('Redis is already connected');
        }

        $this->processConfig($config);
    }

    /**
     * Process redis config
     *
     * @param  array $config
     * @return void
     */
    public function processConfig(array $config)
    {
        if (isset($config['type']) && $config['type'] == 'redis') {
            $this->processRedisConfig($config);
        } else if (isset($config['type']) && $config['type'] == 'sentinel') {
            $this->processSentinelConfig($config);
        } else {
            throw new RedisManagerException('Bad type config');
        }
    }

    /**
     * @param  array $config
     * @return void
     */
    public function processRedisConfig(array $config)
    {
        if (!isset($config['server'])) {
            throw new RedisManagerException('The "server" property is required.');
        }

        $conf = $config['client']['redis'];
        $conf['host'] = $config['server']['host'];

        if (isset($config['server']['port'])) {
            $conf['port'] = $config['server']['port'];
        }

        $this->connect($conf);
    }

    /**
     *
     * @param  array $config
     * @return void
     */
    public function connect(array $config)
    {
        $status = false;

        if ($config['host'][0] == '/') {
            $status = $this->redis->connect($config['host']);
        } else {
            $retry = $config['retry'];

            do {
                if ($this->redis->connect(
                    $config['host'],
                    $config['port'],
                    $config['timeout']
                )) {
                    $status = true;
                    break;
                }

                $retry--;
                usleep($config['interval'] * 1000);
            } while ($retry > 0);
        }

        if ($status) {
            if (isset($config['auth']) && !empty($config['auth'])) {
                $this->redis->auth($config['auth']);
            }

            if (isset($config['db']) && !is_null($config['db'])) {
                $this->redis->select((int) $config['db']);
            }

            if (isset($config['namespace']) && !empty($config['namespace'])) {
                $this->redis->setOption(
                    Redis::OPT_PREFIX,
                    rtrim($config['namespace'], ':') . ':'
                );
            }
        } else {
            throw new RedisManagerException('Connexion failed.');
        }
    }

    /**
     *
     * @param  array $config
     * @return void
     */
    public function processSentinelConfig(array $config)
    {
        if (!isset($config['sentinels'])) {
            throw new RedisManagerException('The "sentinels" property is required.');
        }

        $this->discovery = new RedisMasterDiscovery();

        foreach ($config['sentinels'] as $sentinel) {
            $this->discovery->addSentinel(new RedisSentinel(
                $sentinel['host'],
                $sentinel['port'],
                $config['client']['sentinel']['timeout']
            ));
        }

        $master = $this->discovery->getMasterAddrByName($config['client']['sentinel']['master']);

        $conf = $config['client']['redis'];
        $conf['host'] = $master[0];
        $conf['port'] = $master[1];

        $this->connect($conf);
    }

    /**
     * {@inheritDoc}
     */
    public function getMasterDiscovery()
    {
        return $this->discovery;
    }

    /**
     * {@inheritDoc}
     */
    public function getRedis()
    {
        return $this->redis;
    }
}
