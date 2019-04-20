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

use Closure;
use Exception;

/**
 * Class Reconnect.
 */
abstract class Reconnect
{
    /**
     * Try action n times before throwing exception.
     *
     * @param Closure   $callback
     * @param mixed     $connector
     * @param array     $parameters
     * @param int       $times
     * @param int       $secondsBetweenTries
     * @param Exception $exceptionToThrow
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function tryOrReconnect(
        Closure $callback,
        $connector,
        array $parameters = [],
        int $times = -1,
        int $secondsBetweenTries = 1,
        Exception $exceptionToThrow = null
    ) {
        $iterations = $times;
        $exception = new Exception();

        while (true) {
            try {
                return $callback($connector);
            } catch (Exception $exception) {
                if (!in_array(get_class($exception), static::getDisconnectedExceptionsClass())) {
                    throw $exception;
                }
            }

            if ($times >= 0) {
                if (0 === $iterations) {
                    break;
                }

                --$iterations;
            }

            sleep($secondsBetweenTries);
            static::reconnect(
                $connector,
                $parameters
            );
        }

        throw ($exceptionToThrow ?? $exception);
    }

    /**
     * Get exceptions that indicates connection lost.
     *
     * @return string[]
     */
    abstract protected static function getDisconnectedExceptionsClass(): array;

    /**
     * Reconnect.
     *
     * @param $object
     * @param array $parameters
     */
    abstract protected static function reconnect(
        $object,
        array $parameters
    );
}
