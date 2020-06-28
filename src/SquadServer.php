<?php

namespace DSG\SquadRCON;

use DSG\SquadRCON\Contracts\ServerCommandRunner;
use DSG\SquadRCON\Data\Team;
use DSG\SquadRCON\Data\Squad;
use DSG\SquadRCON\Data\Player;
use DSG\SquadRCON\Data\ServerConnectionInfo;
use DSG\SquadRCON\Runners\SquadCommandRunner;
use DSG\SquadRCON\Services\RCon;

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
            $runner = new SquadCommandRunner($serverConnectionInfo);
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
    public function serverPopulation() : array
    {
        /* Get the current Teams and their Squads */
        $teams = $this->listSquads();

        /* Get the currently connected players, feed listSquads output to reference Teams/Squads */
        $this->currentPlayers($teams);

        return $teams;
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
     * @deprecated 0.1.3 Use listPlayers instead
     */
    public function currentPlayers(array &$teams = null) : array
    {
        return $this->listPlayers($teams);
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
    public function listPlayers(array &$teams = null) : array
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
                if ($teams && count($teams) && $matches[4] !== 'N/A' && array_key_exists($matches[4], $teams)) {
                    /* Get the Team */
                    $player->setTeam($teams[$matches[4]]);

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
    public function kick(string $nameOrSteamId, string $reason = '') : bool
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
    public function kickById(int $id, string $reason = '') : bool
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
    public function ban(string $nameOrSteamId, string $duration = '1d', string $reason = '') : bool
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
    public function banById(int $id, string $duration = '1d', string $reason = '') : bool
    {
        return $this->runner->adminBanById($id, $duration, $reason);
    }

    /**
     * Gets the current map using the ShowNextMap command.
     * 
     * @return string
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function currentMap() : string
    {
        return $this->currentMaps()['current'];
    }

    /**
     * Gets the current next map using the ShowNextMap command.
     * 
     * @return string
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function nextMap() : string
    {
        return $this->currentMaps()['next'];
    }

    /**
     * ShowNextMap command.
     * Gets the current and next map.
     * 
     * @return array
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    private function currentMaps() : array
    {
        /* Initialize the output */
        $maps = [
            'current' => null,
            'next' => null
        ];

        /* Run the ShowNextMap Command and get response */
        $response = $this->runner->showNextMap("ShowNextMap");

        /* Parse response */
        $arr = explode(', Next map is ', $response);
        if (count($arr) > 1) {
            $next = trim($arr[1]);
            $curr = substr($arr[0], strlen('Current map is '));
            $maps['current'] = $curr;
            $maps['next'] = $next;
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
     * ChatToAdmin command.
     * Restarts the current match.
     *
     * @return boolean
     */
    public function restartMatch() : bool
    {
        return $this->runner->adminRestartMatch();
    }

    /**
     * AdminEndMatch command.
     * Ends the current Match.
     *
     * @return boolean
     */
    public function endMatch() : bool
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
    public function setSlots(int $slots = 78) : bool
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
    public function setPassword(string $password) : bool
    {
        return $this->runner->adminSetServerPassword($password);
    }

    /**
     * AdminChangeMap command
     * Immediately changes the current map to the given map.
     * @param string $map
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminChangeMap(string $map) : bool
    {
        return $this->runner->adminChangeMap($map);
    }

    /**
     * AdminSetNextMap command.
     * Temporarily overwrites the next map in the
     * MapRotations, effecively changing the next map.
     * 
     * @param string $map
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetNextMap(string $map) : bool
    {
        return $this->runner->adminSetNextMap($map);
    }
}