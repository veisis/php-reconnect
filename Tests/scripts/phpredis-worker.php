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

set_time_limit(0);

require __DIR__.'/../../vendor/autoload.php';

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
echo 'Connected to Redis'.PHP_EOL;
\Apisearch\Reconnect\PHPRedisReconnect::tryOrReconnect(
    function (Redis $redis) {
        try {
            while (true) {
                $redis->blPop(['test_pop'], 0);
                echo 'Read from `test_pop`'.PHP_EOL;
                $redis->incr('test_incr');
                echo 'Increased `test_incr`'.PHP_EOL;
            }
        } catch (\RedisException $exception) {
            echo 'Suffered exception'.PHP_EOL;
            throw $exception;
        }
    },
    $redis
);
