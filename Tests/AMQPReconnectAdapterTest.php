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

use Apisearch\Reconnect\AMQPReconnect;
use Apisearch\Reconnect\Reconnect;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class AMQPReconnectAdapterTest.
 */
class AMQPReconnectAdapterTest extends ReconnectAdapterTest
{
    /**
     * Make connection.
     *
     * @return mixed
     */
    protected function makeConnection()
    {
        return new AMQPStreamConnection(
            'localhost',
            '5672',
            'guest',
            'guest',
            '/'
        );
    }

    /**
     * Make simple action.
     *
     * @param AbstractConnection $connection
     *
     * @return bool
     */
    protected function makeSimpleAction($connection): bool
    {
        $channel = $connection->channel();
        $channel->basic_publish(new AMQPMessage('1', ['delivery_mode' => 2]), '', 'test_1');
        sleep(2);
        $value = $channel->basic_get('test_2');

        return '1' === $value->body;
    }

    /**
     * Get Reconnect instance.
     *
     * @return Reconnect
     */
    protected function getReconnectInstance(): Reconnect
    {
        return new AMQPReconnect();
    }

    /**
     * Force connection drop.
     *
     * @param AMQPChannel $object
     */
    protected function forceConnectionDrop($object)
    {
        $host = sprintf('http://%s:%s@%s:%s/api/',
            'guest',
            'guest',
            'localhost',
            '15672'
        );

        do {
            usleep(100000);
            $connections = file_get_contents($host.'connections');
            $connections = json_decode($connections, true);

            foreach ($connections as $connection) {
                file_get_contents(
                    $host.'connections/'.rawurlencode($connection['name']),
                    false,
                    stream_context_create([
                        'http' => [
                            'method' => 'DELETE',
                        ],
                    ])
                );
            }
        } while (empty($connections));
    }
}
