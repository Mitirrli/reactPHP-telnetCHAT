<?php

use React\Socket\ConnectionInterface;

class FriendlyConnectionsPool
{
    protected $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage;
    }

    public function addConnection(ConnectionInterface $connection)
    {
        $connection->write('Enter your name: ');
        $this->setConnectionName($connection, '');
        
        $connection->on('data', function ($data) use ($connection) {
            $name = $this->getConnectionName($connection);
            if(empty($name)) {
                $name = str_replace(["\n", "\r"], '', $data);
                $this->setConnectionName($connection, $name);
                $this->sendAll("User $name come in" . PHP_EOL, $connection);
                
                return;
            }

            $this->sendAll("$name: $data", $connection);
        });

        $connection->on('close', function () use ($connection) {
            $name = $this->getConnectionName($connection);
            $this->connections->offsetUnset($connection);
            $this->sendAll("User $name come out" . PHP_EOL, $connection);
        });
    }

    private function getConnectionName(ConnectionInterface $connection)
    {
        return $this->connections->offsetGet($connection);
    }

    private function setConnectionName(ConnectionInterface $connection, $name)
    {
        return $this->connections->offsetSet($connection, $name);
    }

    private function sendAll(string $message, $connection) 
    {
        foreach ($this->connections as $conn) {
            if ($connection != $conn) {
                $conn->write($message);
            } 
        } 
    }
}