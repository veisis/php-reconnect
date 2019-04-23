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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\DriverException;

/**
 * Class DBALReconnect.
 */
class DBALReconnect extends Reconnect
{
    /**
     * Get exceptions that indicates connection lost.
     *
     * @return string[]
     */
    protected static function getDisconnectedExceptionsClass(): array
    {
        return [
            DBALException::class,
            DriverException::class,
            PDOException::class,
        ];
    }

    /**
     * Reconnect.
     *
     * @param Connection $object
     * @param array      $parameters
     */
    protected static function reconnect(
        $object,
        array $parameters
    ) {
        $object->close();
        $object->connect();
    }
}
