<?php

namespace DSG\SquadRCON\Data\Objects;

use Spatie\DataTransferObject\DataTransferObject;

class Squad extends DataTransferObject
{
    public int $id;

    public string $name;

    public int $size;

    public bool $locked;
    
    public string $creatorName;
    
    public string $creatorSteamId;

    /**
     * @var Player[]
     */
    public array $players = [];

    /**
     * Get the first Player of this Squad that
     * has the leader property set to true
     */
    public function leader() : ?Player
    {
        foreach ($this->players as $player) {
            if ($player->leader) {
                return $player;
            }
        }

        return null;
    }
}
