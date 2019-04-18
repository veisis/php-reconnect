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

use Apisearch\Reconnect\Reconnect;
use PHPUnit\Framework\TestCase;

/**
 * Class ReconnectAdapterTest.
 */
abstract class ReconnectAdapterTest extends TestCase
{
    /**
     * Test simple reconnect adapter.
     */
    public function testExceptionReconnectAdapter()
    {
        $reconnectInstance = $this->getReconnectInstance();
        $connectionObject = $this->makeConnection();

        $this->assertTrue($this->makeSimpleAction($connectionObject));
        $this->forceConnectionDrop($connectionObject);

        $result = $reconnectInstance->tryOrReconnect(
            function ($connectionObject) {
                return $this->makeSimpleAction($connectionObject);
            },
            $connectionObject,
            2
        );
        $this->assertTrue($result);
    }

    /**
     * Make connection.
     *
     * @return mixed
     */
    abstract protected function makeConnection();

    /**
     * Make simple action.
     *
     * @param mixed $object
     *
     * @return bool
     */
    abstract protected function makeSimpleAction($object): bool;

    /**
     * Get Reconnect instance.
     *
     * @return Reconnect
     */
    abstract protected function getReconnectInstance(): Reconnect;

    /**
     * Force connection drop.
     *
     * @param mixed $object
     */
    abstract protected function forceConnectionDrop($object);
}
