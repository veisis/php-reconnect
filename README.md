# Apisearch - PHP Reconnect

Use persistent connections in your ReactPHP server or in your workers, and
forget about "what to do when connection fails".

[![CircleCI](https://circleci.com/gh/apisearch-io/php-reconnect.svg?style=svg)](https://circleci.com/gh/apisearch-io/php-reconnect)

You can easily install PHP Reconnect by adding the package name in your project
*composer.json* file.

> Even this package offers tools related to some external dependencies, like
> Doctrine DBAL or RabbitMQ, you can check in our composer.json that we don't
> really need them when installing the package as a requirements. If you want to
> use any of the adapters, you'll be responsible to have the required packages
> in your project. So the number of extra packages installed when adding this
> one as a dependency is 0.

```json
{
    "require": {
        "apisearch-io/php-reconnect": "*"
    }
}
```

## Doctrine DBAL Adapter

> You need the doctrine/dbal required package in your project to use this
> adapter.

You can use your DBAL Connection in a very safe way by using this adapter. If
your worker or server suffers any kind of timeout or the connection breaks, the
Reconnect class will be able to recreate it transparently, allowing you to
**NOT** lose anything because of that.

```php
use Apisearch\Reconnect\DBALReconnect;
use Doctrine\DBAL\Connection;

$connection = //;
$result = DBALReconnect::tryOrReconnect(
    function (Connection $connection) {
        return $connection->doWhatever();
    },
    $connection,
    [], // Unused parameters
    3, // Number of retries before throwing Exception. By default -1 / Infinite
    1, // Seconds between each try. By default 0
    new \Exception // Exception to throw after N tries. By default, last one
);
```

Most of the times, the syntax will be much sorter than that.

```php
use Apisearch\Reconnect\DBALReconnect;
use Doctrine\DBAL\Connection;

$connection = //;
$result = DBALReconnect::tryOrReconnect(
    function (Connection $connection) {
        return $connection->doWhatever();
    },
    $connection
);
```

## AMQP / RabbitMQ Adapter

> You need the php-amqplib/php-amqplib required package in your project to use
> this adapter.

You can use any implementation of the AMQP protocol by using this adapter. This
adapter is very useful for workers, constantly listening to a queue, and being
susceptible of a network problem, or a connection break.

```php
use Apisearch\Reconnect\AMQPReconnect;
use PhpAmqpLib\Connection\AbstractConnection;

$connection = //;
$result = AMQPReconnect::tryOrReconnect(
    function (AbstractConnection $connection) {
        return $connection->doWhatever();
    },
    $connection,
    [], // Unused parameters
    3, // Number of retries before throwing Exception. By default -1 / Infinite
    1, // Seconds between each try. By default 0
    new \Exception // Exception to throw after N tries. By default, last one
);
```

Most of the times, the syntax will be much sorter than that. Specially in
workers, make sure that you don't limit the number of retries. In the case you 
want to set a number of seconds between retries, then set to `-1` the number of
retries. Means infinite.

```php
use Apisearch\Reconnect\AMQPReconnect;
use PhpAmqpLib\Connection\AbstractConnection;

$connection = //;
$result = AMQPReconnect::tryOrReconnect(
    function (AbstractConnection $connection) {
        return $connection->doWhatever();
    },
    $connection
);
```

## PHPRedis Adapter

> You need the phpredis extension installed. Make sure you have it in your
> environment.

When using redis as a simple storage service or as a subscriber / consumer
worker, you might have networking problems, being this last example the clear
example of an exception in your worker. This adapter allow you to reconnect to
Redis in a transparent way

> You can use it both on Redis and RedisCluster classes

```php
use Apisearch\Reconnect\PHPRedisReconnect;
use Redis;

$redis = //;
$result = PHPRedisReconnect::tryOrReconnect(
    function (Redis $redis) {
        return $redis->doWhatever();
    },
    $redis,
    [
        'host' => '127.0.0.1', // Host
        'port' => 6379, // Port, by default 6379
        'timeout' => 1.0 // Timeout, by default 0.0
    ], // Connection parameters
    3, // Number of retries before throwing Exception. By default -1 / Infinite
    1, // Seconds between each try. By default 0
    new \Exception // Exception to throw after N tries. By default, last one
);
```

Most of the times, the syntax will be much sorter than that. Specially in
workers, make sure that you don't limit the number of retries. In the case you 
want to set a number of seconds between retries, then set to `-1` the number of
retries. Means infinite.

```php
use Apisearch\Reconnect\PHPRedisReconnect;
use Redis;

$redis = //;
$result = PHPRedisReconnect::tryOrReconnect(
    function (Redis $redis) {
        return $redis->doWhatever();
    },
    $redis
);
```