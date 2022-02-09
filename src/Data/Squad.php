<?php

namespace DSG\SquadRCON\Data;

class Squad
{
    private int $id;

    private string $name;

    private int $size;

    private bool $locked;

    private Team $team;
    
    private string $creatorName;
    
    private string $creatorSteamId;

    /**
     * @var Player[]
     */
    private array $players = [];

    function __construct(int $id, string $name, int $size, bool $locked, Team $team, string $creatorName, string $creatorSteamId)
    {
        $this->id              = $id;
        $this->name            = $name;
        $this->size            = $size;
        $this->locked          = $locked;
        $this->team            = $team;
        $this->creatorName     = $creatorName;
        $this->creatorSteamId  = $creatorSteamId;
    }

    /**
     * Get the ID of this Squad instance.
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the name of this Squad instance.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the Size of this Squad instance.
     */
    public function getSize() : int
    {
        return $this->size;
    }

    /**
     * Get the Lock status of this Squad instance.
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
     * Get the name of the player who created this Squad.
     */
    public function getCreatorName() : string
    {
        return $this->creatorName;
    }
    
    /**
     * Get the steamId64 of the player who created this Squad.
     */
    public function getCreatorSteamID() : string
    {
        return $this->creatorSteamId;
    }

    /**
     * Get the Player that is the Leader of this Squad
     */
    public function getLeader() : ?Player
    {
        foreach ($this->players as $player) {
            if ($player->isLeader()) {
                return $player;
            }
        }

        return null;
    }

    /**
     * Adds an Player to this Squad instance.
     * Also References the Squad on the Player.
     */
    public function addPlayer(Player $player) : void
    {
        $this->players[] = $player;
        $player->setSquad($this);
    }
}
