<?php

use React\Socket\ConnectionInterface;

class ConnectionsPool
{
    protected $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage;
    }

    public function addConnection(ConnectionInterface $connection)
    {
        $connection->write('A new user coming!' . PHP_EOL);
        $this->connections->attach($connection);
        
        $connection->on('data', function ($data) use ($connection) {
            foreach ($this->connections as $conn) {
                if ($connection != $conn) {
                    $conn->write($data);
                }
            }
        });

        $connection->on('close', function () use ($connection) {
            $this->connections->detach($connection);
        });
    }
}