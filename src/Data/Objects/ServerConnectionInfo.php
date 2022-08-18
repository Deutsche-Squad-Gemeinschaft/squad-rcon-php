<?php

namespace DSG\SquadRCON\Data\Objects;

use DSG\SquadRCON\Data\Validators\NumberBetween;
use Spatie\DataTransferObject\DataTransferObject;

class ServerConnectionInfo extends DataTransferObject
{
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
    #[NumberBetween(1, 30)]
    public int $timeout = 3;
}