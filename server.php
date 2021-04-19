<?php

require "vendor/autoload.php";
require "FriendlyConnectionsPool.php";

$loop = \React\EventLoop\Factory::create();
$socket = new \React\Socket\Server('127.0.0.1:8800', $loop);


# 连接时返回指定内容
// $socket->on('connection', function (React\Socket\ConnectionInterface $connection) {
//     $connection->write('Welcome to connect!');
// });


# 简单的返回自己发送的内容
// $socket->on('connection', function (React\Socket\ConnectionInterface $connection) {
//     $connection->on('data', function ($data) use ($connection) {
//         $connection->write($data);
//     });
// });

# 最简单的聊天室
// $pool = new ConnectionsPool;
// $socket->on('connection', function (React\Socket\ConnectionInterface $connection) use ($pool) {
//     $pool->addConnection($connection);
// });

# 一个体验良好的聊天室
$pool = new FriendlyConnectionsPool;
$socket->on('connection', function (React\Socket\ConnectionInterface $connection) use ($pool) {
    $pool->addConnection($connection);
});

$loop->run();