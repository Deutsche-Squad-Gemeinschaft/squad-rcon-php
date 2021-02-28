<?php

namespace DSG\SquadRCON\Runners;

use DSG\SquadRCON\Contracts\ServerCommandRunner;
use DSG\SquadRCON\Data\ServerConnectionInfo;
use xPaw\SourceQuery\SourceQuery;

class SquadRconRunner implements ServerCommandRunner {
    private SourceQuery $sourceQuery;

    /**
     * SquadServer constructor.
     * @param ServerConnectionInfo $info
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function __construct(ServerConnectionInfo $info)
    {
        /* Initialize the Query class */
        $this->sourceQuery = new SourceQuery();

        /* Connect to the server */
        $this->sourceQuery->Connect($info->host, $info->port, $info->timeout, SourceQuery::SQUAD);

        /* Authenticated with rcon password */
        $this->sourceQuery->SetRconPassword($info->password);
    }
    
    /**
     * @inheritDoc
     */
    public function listSquads() : string
    {
        return $this->sourceQuery->Rcon('ListSquads');
    }

    /**
     * @inheritDoc
     */
    public function listPlayers() : string
    {
        /* Execute the ListPlayers command and get the response */
        return $this->sourceQuery->Rcon('ListPlayers');
    }

    /**
     * @inheritDoc
     */
    public function listDisconnectedPlayers() : string
    {
        return $this->sourceQuery->Rcon('AdminListDisconnectedPlayers');
    }

    /**
     * @inheritDoc
     */
    public function adminKick(string $nameOrSteamId, string $reason = '') : bool
    {
        return $this->_consoleCommand('AdminKick', $nameOrSteamId . ' ' . $reason, 'Kicked player ');
    }

    /**
     * @inheritDoc
     */
    public function adminKickById(int $id, string $reason = '') : bool
    {
        return $this->_consoleCommand('AdminKickById', $id . ' ' . $reason, 'Kicked player ');
    }

    /**
     * @inheritDoc
     */
    public function adminBan(string $nameOrSteamId, string $duration = '1d', string $reason = '') : bool
    {
        return $this->_consoleCommand('AdminBan', $nameOrSteamId . ' ' . $duration . ' ' . $reason, 'Banned player ');
    }

    /**
     * @inheritDoc
     */
    public function adminBanById(int $id, string $duration = '1d', string $reason = '') : bool
    {
        return $this->_consoleCommand('AdminBanById', $id . ' ' . $duration . ' ' . $reason, 'Banned player ');
    }

    /**
     * @inheritDoc
     */
    public function showCurrentMap() : string
    {
        return $this->sourceQuery->Rcon('ShowCurrentMap');
    }

    /**
     * @inheritDoc
     */
    public function showNextMap() : string
    {
        return $this->sourceQuery->Rcon('ShowNextMap');
    }

    /**
     * @inheritDoc
     */
    public function adminBroadcast(string $msg) : bool
    {
        return $this->_consoleCommand('AdminBroadcast', $msg, 'Message broadcasted');
    }

    /**
     * @inheritDoc
     */
    public function adminRestartMatch() : bool
    {
        return $this->_consoleCommand('AdminRestartMatch', '', 'Game restarted');
    }

    /**
     * @inheritDoc
     */
    public function adminEndMatch() : bool
    {
        return $this->_consoleCommand('AdminEndMatch', '', 'Match ended');
    }

    /**
     * @inheritDoc
     */
    public function adminSetMaxNumPlayers(int $slots) : bool
    {
        return $this->_consoleCommand('AdminSetMaxNumPlayers', $slots, 'Set MaxNumPlayers to ' . $slots);
    }

    /**
     * @inheritDoc
     */
    public function adminSetServerPassword(string $password) : bool
    {
        return $this->_consoleCommand('AdminSetServerPassword', $password, 'Set server password to ' . $password);
    }

    /**
     * @inheritDoc
     */
    public function adminChangeLevel(string $level) : bool
    {
        return $this->_consoleCommand('AdminChangeLayer', $level, 'Changed level to');
    }

    /**
     * @inheritDoc
     */
    public function adminSetNextLevel(string $level) : bool
    {
        return $this->_consoleCommand('AdminSetNextLayer', $level, 'Set next level to');
    }

    /**
     * @inheritDoc
     */
    public function adminChangeLayer(string $layer) : bool
    {
        return $this->_consoleCommand('AdminChangeLayer', $layer, 'Changed layer to');
    }

    /**
     * @inheritDoc
     */
    public function adminSetNextLayer(string $layer) : bool
    {
        return $this->_consoleCommand('AdminSetNextLayer', $layer, 'Set next layer to');
    }

    /**
     * @inheritDoc
     */
    public function adminVoteLevel(string $levels) : bool
    {
        return $this->_consoleCommand('AdminVoteLevel', $levels, 'TODO');
    }

    /**
     * @inheritDoc
     */
    public function adminVoteLayer(string $layers) : bool
    {
        return $this->_consoleCommand('AdminVoteLayer', $layers, 'TODO');
    }

    /**
     * @inheritDoc
     */
    public function adminVoteNextLevel(string $levels) : bool
    {
        return $this->_consoleCommand('AdminVoteNextLevel', $levels, 'TODO');
    }

    /**
     * @inheritDoc
     */
    public function adminVoteNextLayer(string $layer) : bool
    {
        return $this->_consoleCommand('AdminVoteNextLayer', $layer, 'TODO');
    }

    /**
     * @inheritDoc
     */
    public function adminVote(string $name, string $choices) : bool
    {
        return $this->_consoleCommand('AdminVote', $name + ' ' + $choices, 'TODO');
    }

    /**
     * @inheritDoc
     */
    public function adminSlomo(float $timeDilation = 1.0) : bool
    {
        return $this->_consoleCommand('AdminSlomo', $timeDilation);
    }

    /**
     * @inheritDoc
     */
    public function adminForceTeamChange(string $nameOrSteamId) : bool
    {
        return $this->_consoleCommand('AdminForceTeamChange', $nameOrSteamId, 'Forced team change for player ');
    }

    /**
     * @inheritDoc
     */
    public function adminForceTeamChangeById(int $playerId) : bool
    {
        return $this->_consoleCommand('AdminForceTeamChangeById', $playerId, 'Forced team change for player ');
    }

    /**
     * @inheritDoc
     */
    //public function adminDemoteCommander(string $nameOrSteamId) : bool
    //{
    //    return $this->_consoleCommand('AdminDemoteCommander', $nameOrSteamId, 'Forced team change for player ');
    //}

    /**
     * @inheritDoc
     */
    //public function adminDemoteCommanderById(int $playerId) : bool
    //{
    //    return $this->_consoleCommand('AdminDemoteCommanderById', $playerId, 'Forced team change for player ');
    //}

    /**
     * @inheritDoc
     */
    public function adminDisbandSquad(int $teamId, int $squadId) : bool
    {
        return $this->_consoleCommand('AdminDisbandSquad', $teamId . ' ' . $squadId, 'Remote admin disbanded squad ' . $squadId . ' on team ' . $teamId . ', named "');
    }

    /**
     * @inheritDoc
     */
    public function adminRemovePlayerFromSquad(string $playerName) : bool
    {
        return $this->_consoleCommand('AdminRemovePlayerFromSquad', $playerName, 'Player ', ' was removed from squad');
    }

    /**
     * @inheritDoc
     */
    public function adminRemovePlayerFromSquadById(int $playerId) : bool
    {
        return $this->_consoleCommand('AdminRemovePlayerFromSquadById', $playerId, 'Player ', ' was removed from squad');
    }

    /**
     * @inheritDoc
     */
    public function adminWarn(string $nameOrSteamId, string $warnReason) : bool
    {
        return $this->_consoleCommand('AdminWarn', $nameOrSteamId . ' ' . $warnReason, 'Remote admin has warned player ');
    }

    /**
     * @inheritDoc
     */
    public function adminWarnById(int $playerId, string $warnReason) : bool
    {
        return $this->_consoleCommand('AdminWarnById', $playerId . ' ' . $warnReason, 'Remote admin has warned player ');
    }

    /**
     * Helper method to run Console commands with an expected response.
     * 
     * @param string $cmd
     * @param string $param
     * @param string $expected
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    private function _consoleCommand(string $cmd, string $param, ?string $expectedStart = null, ?string $expectedEnd = null) : bool
    {
        /* Execute the RCON command */
        $response = $this->sourceQuery->Rcon($cmd . ' ' . $param);

        /* Clean NULL characters from the response */
        $cleaned = str_replace("\0", '', $response);

        /* Validate */
        if (is_null($expectedStart) && is_null($expectedEnd)) {
            return !mb_strlen($cleaned);
        } else {
            $status = true;

            if (!is_null($expectedStart)) {
                $status &= substr($cleaned, 0, strlen($expectedStart)) == $expectedStart;
            }
            
            if (!is_null($expectedEnd)) {
                $status &= substr($cleaned, -strlen($expectedEnd)) == $expectedEnd;
            }

            return $status;
        }
    }

    /**
     * @inheritDoc
     */
    public function disconnect() : void
    {
        if ($this->sourceQuery) {
            $this->sourceQuery->disconnect();
        }
    }
}