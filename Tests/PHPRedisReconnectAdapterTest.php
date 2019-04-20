<?php

/*
 * This file is part of the Apisearch PHP Reconnect.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Reconnect\Tests;

use Apisearch\Reconnect\PHPRedisReconnect;
use Apisearch\Reconnect\Reconnect;
use Redis;

/**
 * Class PHPRedisReconnectAdapterTest.
 */
class PHPRedisReconnectAdapterTest extends ReconnectAdapterTest
{
    /**
     * Make connection.
     *
     * @return mixed
     */
    protected function makeConnection()
    {
        $redis = new Redis();
        $redis->pconnect('localhost', 6379, 1.0);

        return $redis;
    }

    /**
     * Make simple action.
     *
     * @param Redis $connection
     *
     * @return bool
     */
    protected function makeSimpleAction($connection): bool
    {
        $connection->set('test_incr', 0);
        $connection->lPush('test_pop', 'x');
        sleep(2);

        return '1' === $connection->get('test_incr');
    }

    /**
     * Get Reconnect instance.
     *
     * @return Reconnect
     */
    protected function getReconnectInstance(): Reconnect
    {
        return new PHPRedisReconnect();
    }

    /**
     * Force connection drop.
     *
     * @param Redis $object
     */
    protected function forceConnectionDrop($object)
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);

        do {
            usleep(100000);
            $openedClientsList = $redis->client('list');
            foreach ($openedClientsList as $openedClient) {
                $redis->client('kill', $openedClient['addr']);
            }
        } while (empty($openedClientsList));
    }

    /**
     * Get connection parameters.
     *
     * @return array
     */
    protected function getConnectionParameters(): array
    {
        return [
            'host' => '127.0.0.1',
            'port' => 6379,
        ];
    }
}
