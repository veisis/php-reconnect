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

$connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
    'localhost',
    '5672',
    'guest',
    'guest',
    '/'
);
$channel = $connection->channel();
$channel->queue_declare('test_1', false, false, false, false);
$channel->queue_declare('test_2', false, false, false, false);
$channel->basic_qos(0, 1, false);

echo 'Connected to Rabbitmq'.PHP_EOL;
\Apisearch\Reconnect\AMQPReconnect::tryOrReconnect(
    function (\PhpAmqpLib\Connection\AMQPStreamConnection $connection) {
        try {
            $channel = $connection->channel();
            $channel->basic_consume('test_1', '', false, true, false, false, function (\PhpAmqpLib\Message\AMQPMessage $message) use ($channel) {
                echo 'Read from `test_1`'.PHP_EOL;
                $channel->basic_publish(new \PhpAmqpLib\Message\AMQPMessage('1', ['delivery_mode' => 2]), '', 'test_2');
                echo 'Write at `test_2`'.PHP_EOL;
            });

            while (count($channel->callbacks)) {
                $channel->wait();
            }
        } catch (\Exception $exception) {
            echo 'Suffered exception'.PHP_EOL;
            throw $exception;
        }
    },
    $connection
);
