<?php

namespace DSG\SquadRCON\Data\Objects;

use Spatie\DataTransferObject\DataTransferObject;

class Population extends DataTransferObject
{
    /**
     * @var Team[]
     */
    public array $teams;

    /**
     * Get the Player instances of all the
     * teams of the Population instance
     *
     * @return Player[]
     */
    public function players() : array
    {
        /** @var Player[] */
        $players = [];

        foreach ($this->teams as $team) {
            foreach ($team->squads as $squad) {
                $players = array_merge($players, $squad->players);
            }

            $players = array_merge($players, $team->players);
        }

        return $players;
    }

    /**
     * Searches and returns the Player with the 
     * given Steam 64 Id. Returns null in case no 
     * Player has been found.
     */
    public function getPlayerBySteamId(string $steamId64) : ?Player
    {
        $players = $this->players();

        /* Serch the players for the given steam id and return the found Player */
        foreach ($players as $player) {
            if ($player->steamId === $steamId64) {
                return $player;
            }
        }

        /* Return null in case nothing has been found */
        return null;
    }
}
