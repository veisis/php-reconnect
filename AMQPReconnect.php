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

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPProtocolConnectionException;

/**
 * Class AMQPReconnect.
 */
class AMQPReconnect extends Reconnect
{
    /**
     * Get exceptions that indicates connection lost.
     *
     * @return string[]
     */
    protected static function getDisconnectedExceptionsClass(): array
    {
        return [
            AMQPConnectionClosedException::class,
            AMQPIOException::class,
            AMQPProtocolConnectionException::class,
        ];
    }

    /**
     * Reconnect.
     *
     * @param AbstractConnection $object
     * @param array              $parameters
     */
    protected static function reconnect(
        $object,
        array $parameters
    ) {
        $object->close();
        $object->reconnect();
    }
}
