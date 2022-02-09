<?php

namespace DSG\SquadRCON\Data;

class ServerConnectionInfo {
    const DEFAULT_SOCKET_TIMEOUT_SECONDS = 3;

    /**
     * Host of the Server.
     */
    public string $host;

    /**
     * (RCon) Port of the Server.
     */
    public int $port;

    /**
     * (RCon) Password of the Server.
     */
    public string $password;

    /**
     * Timeout for the RCon connection.
     */
    public int $timeout;

    function __construct(string $host, int $port, string $password, int $timeout = self::DEFAULT_SOCKET_TIMEOUT_SECONDS)
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->password = $password;

        if ($timeout <= 0) {
            throw new \InvalidArgumentException('Timeout must be greater or equal to 1');
        }
        $this->timeout  = $timeout;
    }
}