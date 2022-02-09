<?php

namespace DSG\SquadRCON\Data;

class Team
{
    private int $id;

    private string $name;

    /**
     * @var Squad[]
     */
    private array $squads = [];

    /**
     * @var Player[]
     */
    private array $players = [];

    function __construct(int $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }

    /**
     * Get the ID of this Team instance.
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the Name of this Team instance.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the Squads of this Team instance.
     * 
     * @return Squad[]
     */
    public function getSquads() : array
    {
        return $this->squads;
    }

    /**
     * Adds an Squad to this Team instance.
     */
    public function addSquad(Squad $squad) : void
    {
        $this->squads[$squad->getId()] = $squad;
    }

    /**
     * Get the Players of this Team instance.
     * 
     * @return Player[]
     */
    public function getPlayers() : array
    {
        return $this->players;
    }

    /**
     * Adds an Player to this Team instance.
     */
    public function addPlayer(Player $player) : void
    {
        $this->players[] = $player;
    }
}