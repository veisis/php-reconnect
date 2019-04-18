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

use Apisearch\Reconnect\DBALReconnect;
use Apisearch\Reconnect\Reconnect;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

/**
 * Class DBALReconnectAdapterTest.
 */
class DBALReconnectAdapterTest extends ReconnectAdapterTest
{
    /**
     * Make connection.
     *
     * @return Connection
     */
    protected function makeConnection()
    {
        $config = new Configuration();
        $connectionParams = array(
            'user' => 'root',
            'password' => 'root',
            'host' => '127.0.0.1',
            'port' => '3306',
            'driver' => 'pdo_mysql',
        );

        return DriverManager::getConnection($connectionParams, $config);
    }

    /**
     * Make simple action.
     *
     * @param Connection $connection
     *
     * @return bool
     */
    protected function makeSimpleAction($connection): bool
    {
        $connection->executeQuery('show databases');

        return true;
    }

    /**
     * Get Reconnect instance.
     *
     * @return Reconnect
     */
    protected function getReconnectInstance(): Reconnect
    {
        return new DBALReconnect();
    }

    /**
     * Force connection drop.
     *
     * @param Connection $object
     */
    protected function forceConnectionDrop($object)
    {
        $global1 = $object->executeQuery('SELECT @@GLOBAL.connect_timeout')->fetchColumn();
        $global2 = $object->executeQuery('SELECT @@GLOBAL.wait_timeout')->fetchColumn();
        $global3 = $object->executeQuery('SELECT @@GLOBAL.interactive_timeout')->fetchColumn();

        $object->executeQuery('SET GLOBAL connect_timeout=1');
        $object->executeQuery('SET GLOBAL wait_timeout=1');
        $object->executeQuery('SET GLOBAL interactive_timeout=1');

        $object->close();
        $object->connect();
        sleep(2);

        $newConnection = $this->makeConnection();
        $newConnection->executeQuery("SET GLOBAL connect_timeout=$global1");
        $newConnection->executeQuery("SET GLOBAL wait_timeout=$global2");
        $newConnection->executeQuery("SET GLOBAL interactive_timeout=$global3");
        $newConnection->close();
    }
}
