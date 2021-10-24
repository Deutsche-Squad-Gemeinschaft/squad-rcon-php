<?php

namespace DSG\SquadRCON\Data;

class Population
{
    /**
     * @var Team[]
     */
    private array $teams;

    function __construct(array $teams)
    {
        $this->teams = $teams;
    }

    /**
     * Get the raw Teams array
     * 
     * @return Team[]
     */
    public function getTeams() : array
    {
        return $this->teams;
    }

    /**
     * Determines if this Population object has any Teams.
     */
    public function hasTeams() : bool
    {
        return !!count($this->teams);
    }

    /**
     * Determines if the given Team exists in this Population
     * instance.
     */
    public function getTeam(int $id) : ?Team
    {
        if (array_key_exists($id, $this->teams)) {
            return $this->teams[$id];
        } else {
            return null;
        }
    }

    /**
     * Flattens the internal teams array and
     * returns ALL the Players in one array.
     *
     * @return Player[]
     */
    public function getPlayers() : array
    {
        /** @var Player[] */
        $players = [];

        foreach ($this->teams as $team) {
            foreach ($team->getSquads() as $squad) {
                $players = array_merge($players, $squad->getPlayers());
            }

            $players = array_merge($players, $team->getPlayers());
        }

        return $players;
    }

    /**
     * Searches and returns the Player with the 
     * given Steam 64 Id. Returns null in case no 
     * Player has been found.
     *
     * @param string $steamId64
     * @return Player|null
     */
    public function getPlayerBySteamId(string $steamId64) : ?Player
    {
        $players = $this->getPlayers();

        /* Serch the players for the given steam id and return the found Player */
        foreach ($players as $player) {
            if ($player->getSteamId() === $steamId64) {
                return $player;
            }
        }

        /* Return null in case nothing has been found */
        return null;
    }
}
