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

namespace Apisearch\Reconnect;

use Redis;
use RedisCluster;
use RedisException;

/**
 * Class PHPRedisReconnect.
 */
class PHPRedisReconnect extends Reconnect
{
    /**
     * Get exceptions that indicates connection lost.
     *
     * @return string[]
     */
    protected static function getDisconnectedExceptionsClass(): array
    {
        return [
            RedisException::class,
        ];
    }

    /**
     * Reconnect.
     *
     * @param Redis|RedisCluster $object
     * @param array              $parameters
     */
    protected static function reconnect(
        $object,
        array $parameters
    ) {
        $object->close();
        $object->connect(
            $parameters['host'],
            \intval($parameters['port'] ?? 6379),
            \floatval($parameters['timeout'] ?? 0.0)
        );
    }
}
