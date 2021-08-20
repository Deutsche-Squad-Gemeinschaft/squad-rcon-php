<?php

namespace DSG\SquadRCON\Data;

class Squad
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var int
     */
    private int $size;

    /**
     * @var bool
     */
    private bool $locked;

    /**
     * @var Team
     */
    private Team $team;
    
    /**
     * @var string
     */
    private string $creator;

    /**
     * @var Player[]
     */
    private array $players = [];

    function __construct(int $id, string $name, int $size, bool $locked, Team $team, string $creator)
    {
        $this->id      = $id;
        $this->name    = $name;
        $this->size    = $size;
        $this->locked  = $locked;
        $this->team    = $team;
        $this->creator = $creator;
    }

    /**
     * Get the ID of this Squad instance.
     * 
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the name of this Squad instance.
     * 
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the Size of this Squad instance.
     * 
     * @return int
     */
    public function getSize() : int
    {
        return $this->size;
    }

    /**
     * Get the Lock status of this Squad instance.
     * 
     * @return bool
     */
    public function isLocked() : bool
    {
        return $this->locked;
    }

    /**
     * Get the Team of this Squad instance.
     * 
     * @return Team
     */
    public function getTeam() : Team
    {
        return $this->team;
    }

    /**
     * Get the Players of this Squad instance.
     * 
     * @return Player[]
     */
    public function getPlayers() : array
    {
        return $this->players;
    }
    
    /**
     * Get the Player that created this Squad instance.
     * 
     * @return Player
     * @throws \Exception If the creator can not be found
     */
    public function getCreator() : Player
    {
        foreach ($this->players as $player) {
            if ($player->getSteamId() === $this->creator) {
                return $player;
            }
        }
        
        throw new \Exception('Squad creator could not be found! Squad ID: ' . $this->getId() . ', Creator ID: ' . $this->creator);
    }

    /**
     * Adds an Player to this Squad instance.
     * Also References the Squad on the Player.
     *
     * @param Player $player
     * @return void
     */
    public function addPlayer(Player $player) : void
    {
        $this->players[] = $player;
        $player->setSquad($this);
    }
}
