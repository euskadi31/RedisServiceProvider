<?php
/*
 * This file is part of the RedisServiceProvider.
 *
 * (c) Axel Etcheverry <axel@etcheverry.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class RedisMasterDiscovery
{
    protected $sentinels = [];

    public function addSentinel(RedisSentinel $sentinel)
    {
        $this->sentinels[] = $sentinel;
    }

    public function getSentinels()
    {
        return $this->sentinels;
    }

    public function getMasterAddrByName($master)
    {
        return ['127.0.0.1', '6379'];
    }
}


class RedisSentinel
{
    public function __construct($host, $port)
    {

    }
}
