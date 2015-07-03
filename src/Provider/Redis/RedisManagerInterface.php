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

/**
 * Redis Manager Interface.
 *
 * @author Axel Etcheverry <axel@etcheverry.biz>
 */
interface RedisManagerInterface
{
    /**
     *
     * @param array      $config
     * @param Redis|null $redis
     */
    public function __construct(array $config, Redis $redis = null);

    /**
     * Get Redis Master Discovery
     *
     * @return RedisMasterDiscovery
     */
    public function getMasterDiscovery();

    /**
     * Get Redis instance
     *
     * @return Redis
     */
    public function getRedis();
}
