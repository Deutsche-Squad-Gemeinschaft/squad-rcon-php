<?php

namespace DSG\SquadRCON\Data\Objects;

use Spatie\DataTransferObject\DataTransferObject;

class Team extends DataTransferObject
{
    public int $id;

    public string $name;

    /**
     * @var Squad[]
     */
    public array $squads = [];

    /**
     * @var Player[]
     */
    public array $players = [];
}