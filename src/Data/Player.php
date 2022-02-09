<?php

namespace DSG\SquadRCON\Data;

class Player
{
    private int $id;

    private string $steamId;

    private string $name;

    private ?Team $team = null;

    private ?Squad $squad = null;

    private bool $leader = false;
    
    private ?string $role = null;

    /**
     * @var int|null
     */
    private ?int $disconnectedSince = null;

    function __construct(int $id, string $steamId, string $name, bool $leader = false, ?string $role = null)
    {
        $this->id       = $id;
        $this->steamId  = $steamId;
        $this->name     = $name;
        $this->leader   = $leader;
        $this->role     = $role;
    }

    /**
     * Get the ID of this Player instance.
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the SteamId of this Player instance.
     */
    public function getSteamId() : string
    {
        return $this->steamId;
    }

    /**
     * Get the name of this Player instance.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the name of the Role the Player has currently selected.
     */
    public function getRole(): ?string
    {
        return $this->kit;
    }

    /**
     * Get the Team this player instance is assigned to.
     */
    public function getTeam() : ?Team
    {
        return $this->team;
    }

    /**
     * Sets the Team of this Player instance
     */
    public function setTeam(Team $team) : void
    {
        $this->team = $team;
    }

    /**
     * Get the Squad this Player instance is assigned to.
     */
    public function getSquad() : ?Squad
    {
        return $this->squad;
    }

    /**
     * Sets the Squad of this Player instance
     */
    public function setSquad(Squad $squad) : void
    {
        $this->squad = $squad;
    }

    /**
     * Determines if this Player is the leader of the Squad he is currently part of.
     */
    public function isLeader(): bool
    {
        return $this->leader;
    }

    /**
     * Gets the disconnected since attribute of this Player instance.
     */
    public function getDisconnectedSince() : ?int
    {
        return $this->disconnectedSince;
    }

    /**
     * Sets the disconnected since attribute of this Player instance.
     *
     * @param int $disconnectedSince Seconds since disconnect
     */
    public function setDisconnectedSince(int $disconnectedSince) : void
    {
        $this->disconnectedSince = $disconnectedSince;
    }
}
