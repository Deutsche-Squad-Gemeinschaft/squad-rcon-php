<?php

namespace DSG\SquadRCON;

use DSG\SquadRCON\Contracts\ServerCommandRunner;
use DSG\SquadRCON\Data\Team;
use DSG\SquadRCON\Data\Squad;
use DSG\SquadRCON\Data\Player;
use DSG\SquadRCON\Data\Population;
use DSG\SquadRCON\Data\ServerConnectionInfo;
use DSG\SquadRCON\Runners\SquadRconRunner;

class SquadServer
{
    const SQUAD_SOCKET_TIMEOUT_SECONDS = 0.5;

    /** @var ServerCommandRunner */
    private ServerCommandRunner $runner;

    /**
     * SquadServer constructor.
     * @param $host
     * @param $port
     * @param $password
     * @param float $timeout
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function __construct(ServerConnectionInfo $serverConnectionInfo, ServerCommandRunner $runner = null)
    {
        /* Initialize the default Runner if none is specified */
        if (!$runner) {
            $runner = new SquadRconRunner($serverConnectionInfo);
        }

        $this->runner = $runner;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function disconnect() : void
    {
        $this->runner->disconnect();
    }

    /**
     * @return Team[]
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function serverPopulation() : Population
    {
        /* Get the current Teams and their Squads */
        $population = new Population($this->listSquads());

        /* Get the currently connected players, feed listSquads output to reference Teams/Squads */
        $this->listPlayers($population);

        return $population;
    }

    /**
     * ListSquads command. Returns an array
     * of Teams containing Squads. The output
     * can be given to the listPlayers method
     * to add and reference the Player instances.
     *
     * @return Team[]
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function listSquads() : array
    {
        /** @var Team[] $teams */
        $teams = [];

        /** @var Squad[] $squads */
        $squads = [];

        /* Get the SquadList from the Server */
        $response = $this->runner->listSquads();

        /** @var Team The current team */
        $currentTeam = null;
        foreach (explode("\n", $response) as $lineSquad) {
            $matches = [];
            if (preg_match('/^Team ID: ([1|2]) \((.*)\)/', $lineSquad, $matches) > 0) {
                /* Initialize a new Team */
                $team = new Team(intval($matches[1]), $matches[2]);

                /* Add to the lookup */
                $teams[$team->getId()] = $team;
                
                /* Initialize squad lookup array */
                $squads[$team->getId()] = [];

                /* Set as current team */
                $currentTeam = $team;
            } else if (preg_match('/^ID: (\d{1,}) \| Name: (.*?) \| Size: (\d) \| Locked: (True|False)/', $lineSquad, $matches) > 0) {
                /* Initialize a new Squad */
                $squad = new Squad(intval($matches[1]), $matches[2], intval($matches[3]), $matches[4] === 'True', $currentTeam);
                
                /* Reference Team */
                $currentTeam->addSquad($squad);

                /* Add to the squads lookup */
                $squads[$currentTeam->getId()][$squad->getId()] = $squad;
            }
        }

        return $teams;
    }

    /**
     * ListPlayers command, returns an array
     * of Player instances. The output of
     * ListSquads can be piped into it to
     * assign the Players to their Team/Squad.
     *
     * @param array $teams
     * @return Player[]
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function listPlayers(Population &$population = null) : array
    {
        /* Initialize an empty output array */
        $players = [];

        /* Execute the ListPlayers command and get the response */
        $response = $this->runner->listPlayers();

        /* Process each individual line */
        foreach (explode("\n", $response) as $line) {
            /* Initialize an empty array and try to get info form line */
            $matches = [];
            if (preg_match('/^ID: (\d{1,}) \| SteamID: (\d{17}) \| Name: (.*?) \| Team ID: (1|2|N\/A) \| Squad ID: (\d{1,}|N\/A)/', $line, $matches)) {
                /* Initialize new Player instance */
                $player = new Player(intval($matches[1]), $matches[2], $matches[3]);

                /* Set Team and Squad references if ListSquads output is provided */
                if ($population && $population->hasTeams() && $matches[4] !== 'N/A' && $population->getTeam($matches[4])) {
                    /* Get the Team */
                    $player->setTeam($population->getTeam($matches[4]));

                    if (count($player->getTeam()->getSquads()) && $matches[5] !== 'N/A' && array_key_exists($matches[5], $player->getTeam()->getSquads())) {
                        /* Get the Squad */
                        $squad = $player->getTeam()->getSquads()[$matches[5]];

                        /* Add the Player to the Squad */
                        $squad->addPlayer($player);
                    } else {
                        /* Add as unassigned Player to the Team instance */
                        $player->getTeam()->addPlayer($player);
                    }
                }

                /* Add to the output */
                $players[] = $player;
            } else if (preg_match('/^-{5} Recently Disconnected Players \[Max of 15\] -{5}/', $line)) {
                /* Notihing of interest, break the loop */
                break;
            }
        }

        return $players;
    }

    /**
     * ListDisconnectedPlayers command, returns an array
     * of disconnected Player instances.
     *
     * @return Player[]
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function listDisconnectedPlayers() : array
    {
        /* Initialize an empty output array */
        $players = [];

        /* Execute the ListPlayers command and get the response */
        $response = $this->runner->listPlayers();

        /* Process each individual line */
        foreach (explode("\n", $response) as $line) {
            /* Initialize an empty array and try to get info form line */
            $matches = [];
            if (preg_match('/^ID: (\d{1,}) \| SteamID: (\d{17}) \| Since Disconnect: (\d{2,})m.(\d{2})s \| Name: (.*?)$/', $line, $matches)) {
                /* Initialize new Player instance */
                $player = new Player(intval($matches[1]), $matches[2], $matches[5]);

                /* Set the disconnected since time */
                $player->setDisconnectedSince(intval($matches[3]) * 60 + intval($matches[4]));

                /* Add to the output */
                $players[] = $player;
            }
        }

        return $players;
    }

    /**
     * AdminBroadcast command.
     * Broadcasts the given message on the server.
     * 
     * @param string $msg
     * @param string $reason
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminKick(string $nameOrSteamId, string $reason = '') : bool
    {
        return $this->runner->adminKick($nameOrSteamId, $reason);
    }

    /**
     * AdminKickById command.
     * Broadcasts the given message on the server.
     * 
     * @param int $id
     * @param string $reason
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminKickById(int $id, string $reason = '') : bool
    {
        return $this->runner->adminKickById($id, $reason);
    }

    /**
     * AdminBan command.
     * Bans the given Player from the Server.
     * 
     * @param string $msg
     * @param string $reason
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminBan(string $nameOrSteamId, string $duration = '1d', string $reason = '') : bool
    {
        return $this->runner->adminBan($nameOrSteamId, $duration, $reason);
    }

    /**
     * AdminBanById command.
     * Bans the given Player from the Server.
     * 
     * @param int $id
     * @param string $reason
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminBanById(int $id, string $duration = '1d', string $reason = '') : bool
    {
        return $this->runner->adminBanById($id, $duration, $reason);
    }

    /**
     * Gets the current map using the ShowNextMap command.
     * 
     * @return string
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function showCurrentMap() : array
    {
        /* Initialize the output */
        $maps = [
            'level' => null,
            'layer' => null
        ];

        /* Run the ShowNextMap Command and get response */
        $response = $this->runner->showCurrentMap();

        /* Parse response */
        $arr = explode(', layer is ', $response);
        if (count($arr) > 1) {
            $maps['level'] = substr($arr[0], strlen('Current level is '));
            $maps['layer'] = trim($arr[1]);
        }

        return $maps;
    }

    /**
     * Gets the current next map using the ShowNextMap command.
     * 
     * @return string
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function showNextMap() : array
    {
        /* Initialize the output */
        $maps = [
            'current' => null,
            'next' => null
        ];

        /* Run the ShowNextMap Command and get response */
        $response = $this->runner->showNextMap();

        /* Parse response */
        $arr = explode(', layer is ', $response);
        if (count($arr) > 1) {
            $maps['level'] = substr($arr[0], strlen('Next level is '));
            $maps['layer'] = trim($arr[1]);
        }

        return $maps;
    }

    /**
     * AdminBroadcast command.
     * Broadcasts the given message on the server.
     * 
     * @param string $msg
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminBroadcast(string $msg) : bool
    {
        return $this->runner->adminBroadcast($msg);
    }

    /**
     * AdminRestartMatch command.
     * Restarts the current match.
     *
     * @return boolean
     */
    public function adminRestartMatch() : bool
    {
        return $this->runner->adminRestartMatch();
    }

    /**
     * AdminEndMatch command.
     * Ends the current Match.
     *
     * @return boolean
     */
    public function adminEndMatch() : bool
    {
        return $this->runner->adminEndMatch();
    }

    /**
     * AdminSetMaxNumPlayers command.
     * Sets the max amount of players (public).
     *
     * @param int $slots How many public slots ther should be.
     * @return boolean
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetMaxNumPlayers(int $slots = 78) : bool
    {
        return $this->runner->adminSetMaxNumPlayers($slots);
    }

    /**
     * AdminSetServerPassword command.
     * Sets the password of the server.
     *
     * @param string $password
     * @return boolean
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetServerPassword(string $password) : bool
    {
        return $this->runner->adminSetServerPassword($password);
    }

    /**
     * AdminChangeLevel command.
     * Change the level ( and pick a random layer on it) and travel to it immediately,
     * 
     * @param string $level The level to change to.
     * @return array
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminChangeLevel(string $level) : bool
    {
        return $this->runner->adminChangeLevel($level);
    }

    /**
     * AdminSetNextLevel command.
     * Set the next Level ( and pick a random layer on it) to travel to after this match ends.
     * 
     * @param string $level The level to be set as next level.
     * @return array
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetNextLevel(string $level) : bool
    {
        return $this->runner->adminSetNextLevel($level);
    }

    /**
     * AdminChangeLayer command.
     * Change the layer and travel to it immediately.
     * 
     * @param string $layer The layer to change to.
     * @return array
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminChangeLayer(string $layer) : bool
    {
        return $this->runner->adminChangeLayer($layer);
    }

    /**
     * AdminSetNextLayer command.
     * Set the next layer to travel to after this match ends.
     * 
     * @param string $layer The layer to be set as next layer.
     * @return array
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetNextLayer(string $layer) : bool
    {
        return $this->runner->adminSetNextLayer($layer);
    }

    /**
     * AdminVoteLevel command.
     * Trigger a Level Vote for immediate change.
     *
     * @param string $level
     * @return boolean
     */
    public function adminVoteLevel(string $levels) : bool
    {
        return $this->runner->adminVoteLevel($levels);
    }

    /**
     * AdminVoteLayer command.
     * Trigger a Layer Vote for immediate change.
     *
     * @param string $layer
     * @return boolean
     */
    public function adminVoteLayer(string $layers) : bool
    {
        return $this->runner->adminVoteLayer($layers);
    }

    /**
     * AdminVoteNextLevel command.
     * Trigger a Level Vote for next match.
     *
     * @param string $level
     * @return boolean
     */
    public function adminVoteNextLevel(string $levels) : bool
    {
        return $this->runner->adminVoteNextLayer($levels);
    }

    /**
     * AdminVoteNextLayer command.
     * Trigger a Layer Vote for next match.
     *
     * @param string $layer
     * @return boolean
     */
    public function adminVoteNextLayer(string $layer) : bool
    {
        return $this->runner->adminVoteNextLayer($layer);
    }

    /**
     * AdminVote command.
     * Ingame text vote.
     *
     * @param string $name
     * @param string $choices
     * @return boolean
     */
    public function adminVote(string $name, string $choices) : bool
    {
        return $this->runner->adminVote($name, $choices);
    }

    /**
     * AdminSlomo command.
     * Sets the game speed with the AdminSlomo
     * command. Providing no parameter will set
     * the speed to default.
     *
     * @param float $timeDilation
     * @return boolean
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSlomo(float $timeDilation = 1.0) : bool
    {
        return $this->runner->adminSlomo($timeDilation);
    }

    /**
     * AdminForceTeamChange command.
     * Forces a player to the opposite team
     * by providing the name or steamid.
     *
     * @param string $nameOrSteamId
     * @return boolean
     */
    public function adminForceTeamChange(string $nameOrSteamId) : bool
    {
        return $this->runner->adminForceTeamChange($nameOrSteamId);
    }

    /**
     * AdminForceTeamChangeById command.
     * Forces a player to the opposite team
     * by providing the ingame Player id.
     *
     * @param integer $playerId
     * @return boolean
     */
    public function adminForceTeamChangeById(int $playerId) : bool
    {
        return $this->runner->adminForceTeamChangeById($playerId);
    }

    /**
     * AdminDemoteCommander command.
     * Demotes a player from the commander slot
     * by providing the name or steamid.
     *
     * @param string $nameOrSteamId
     * @return boolean
     */
    //public function adminDemoteCommander(string $nameOrSteamId) : bool
    //{
    //    return $this->runner->adminDemoteCommander($nameOrSteamId);
    //}

    /**
     * AdminDemoteCommanderById command.
     * Demotes a player from the commander slot
     * by providing the ingame Player id.
     *
     * @param integer $playerId
     * @return boolean
     */
    //public function adminDemoteCommanderById(int $playerId) : bool
    //{
    //    return $this->runner->adminDemoteCommanderById($playerId);
    //}

    /**
     * AdminDisbandSquad command.
     * Disbands a Squad by providing the Team id  / index & Squad id / index.
     *
     * @param integer $teamId
     * @param integer $squadId
     * @return boolean
     */
    public function adminDisbandSquad(int $teamId, int $squadId) : bool
    {
        return $this->runner->adminDisbandSquad($teamId, $squadId);
    }

    /**
     * AdminRemovePlayerFromSquad command.
     * Removes a Player from his Squad by providing
     * the Player name.
     *
     * @param string $playerName
     * @return boolean
     */
    public function adminRemovePlayerFromSquad(string $playerName) : bool
    {
        return $this->runner->adminRemovePlayerFromSquad($playerName);
    }

    /**
     * AdminRemovePlayerFromSquadById command.
     * Removes a player from his Squad by providing
     * the ingame Player id.
     *
     * @param integer $playerId
     * @return boolean
     */
    public function adminRemovePlayerFromSquadById(int $playerId) : bool
    {
        return $this->runner->adminRemovePlayerFromSquadById($playerId);
    }

    /**
     * AdminWarn command.
     * Warns a Player by providing his name / steamid
     * and a message.
     *
     * @param string $nameOrSteamId
     * @param string $warnReason
     * @return boolean
     */
    public function adminWarn(string $nameOrSteamId, string $warnReason) : bool
    {
        return $this->runner->adminWarn($nameOrSteamId, $warnReason);
    }

    /**
     * AdminWarnById command.
     * Warns a Player by providing his ingame Player id
     * and a message.
     *
     * @param integer $playerId
     * @param string $warnReason
     * @return boolean
     */
    public function adminWarnById(int $playerId, string $warnReason) : bool
    {
        return $this->runner->adminWarnById($playerId, $warnReason);
    }
}