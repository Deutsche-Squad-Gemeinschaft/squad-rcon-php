<?php

namespace DSG\SquadRCON\Data\Objects;

use Spatie\DataTransferObject\DataTransferObject;

class Player extends DataTransferObject
{
    public int $id;

    public string $steamId;

    public string $name;

    public bool $leader = false;
    
    public ?string $role = null;

    public ?int $disconnectedSince = null;
}
