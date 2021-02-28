<?php

namespace DSG\SquadRCON\Tests\Runners;

use DSG\SquadRCON\Contracts\ServerCommandRunner;
use DSG\SquadRCON\Tests\Runners\Responses\ListPlayersResponse;
use DSG\SquadRCON\Tests\Runners\Responses\ListSquadsResponse;
use DSG\SquadRCON\Tests\Runners\Responses\ShowCurrentMapResponse;
use DSG\SquadRCON\Tests\Runners\Responses\ShowNextMapResponse;

class TestingCommandRunner implements ServerCommandRunner {
    /**
     * @inheritDoc
     */
    public function listSquads() : string
    {
        return ListSquadsResponse::get();
    }

    /**
     * @inheritDoc
     */
    public function listPlayers() : string
    {
        return ListPlayersResponse::get();
    }

    /**
     * @inheritDoc
     */
    public function listDisconnectedPlayers() : string
    {
        return ListPlayersResponse::get();
    }

    /**
     * @inheritDoc
     */
    function adminKick(string $nameOrSteamId, string $reason = '') : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminKickById(int $id, string $reason = '') : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminBan(string $nameOrSteamId, string $duration = '1d', string $reason = '') : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminBanById(int $id, string $duration = '1d', string $reason = '') : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function showCurrentMap() : string
    {
        return ShowCurrentMapResponse::get();
    }

    /**
     * @inheritDoc
     */
    public function showNextMap() : string
    {
        return ShowNextMapResponse::get();
    }

    /**
     * @inheritDoc
     */
    public function adminBroadcast(string $msg) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    function adminRestartMatch() : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    function adminEndMatch() : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    function adminSetMaxNumPlayers(int $slots) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    function adminSetServerPassword(string $password) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminChangeLevel(string $level) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminSetNextLevel(string $level) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminChangeLayer(string $layer) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminSetNextLayer(string $layer) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminVoteLevel(string $levels) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminVoteLayer(string $layers) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminVoteNextLevel(string $levels) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminVoteNextLayer(string $layer) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminVote(string $name, string $choices) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminSlomo(float $timeDilation = 1.0) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminForceTeamChange(string $nameOrSteamId) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminForceTeamChangeById(int $playerId) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    //public function adminDemoteCommander(string $nameOrSteamId) : bool
    //{
    //    return true;
    //}

    /**
     * @inheritDoc
     */
    //public function adminDemoteCommanderById(int $playerId) : bool
    //{
    //    return true;
    //}

    /**
     * @inheritDoc
     */
    public function adminDisbandSquad(int $teamId, int $squadId) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminRemovePlayerFromSquad(string $playerName) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminRemovePlayerFromSquadById(int $playerId) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminWarn(string $nameOrSteamId, string $warnReason) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function adminWarnById(int $playerId, string $warnReason) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    function disconnect() : void
    {
        return;
    }
}