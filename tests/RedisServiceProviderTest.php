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
namespace Redis\Provider\Tests;

use Silex\Application;
use Redis\Provider\RedisServiceProvider;

class RedisServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testServiceDeclaration()
    {
        $app = new Application();

        $app->register(new RedisServiceProvider(), array(
            "redis.host" => "127.0.0.1",
            "redis.port" => 6379
        ));

        $this->assertInstanceOf("Redis", $app["redis"]);
    }
}
