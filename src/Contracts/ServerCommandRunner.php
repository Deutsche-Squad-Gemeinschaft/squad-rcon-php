<?php

namespace DSG\SquadRCON\Contracts;

interface ServerCommandRunner {
    /**
     * ListSquads command. Returns an array
     * of Teams containing Squads. The output
     * can be given to the listPlayers method
     * to add and reference the Player instances.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function listSquads() : string;

    /**
     * ListPlayers command, returns an array
     * of Player instances. The output of
     * ListSquads can be piped into it to
     * assign the Players to their Team/Squad.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function listPlayers() : string;

    /**
     * ListDisconnectedPlayers command, returns an array
     * of disconnected Player instances.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function listDisconnectedPlayers() : string;

    /**
     * AdmiNkick command.
     * Kick a Player by Name or Steam64ID
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminKick(string $nameOrSteamId, string $reason = '') : bool;

    /**
     * AdminKickById command.
     * Broadcasts the given message on the server.
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminKickById(int $id, string $reason = '') : bool;

    /**
     * AdminBan command.
     * Bans the given Player from the Server.
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminBan(string $nameOrSteamId, string $duration = '1d', string $reason = '') : bool;

    /**
     * AdminBanById command.
     * Bans the given Player from the Server.
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminBanById(int $id, string $duration = '1d', string $reason = '') : bool;

    /**
     * ShowNextMap command.
     * Gets the current and next map.
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function showCurrentMap() : string;

    /**
     * ShowNextMap command.
     * Gets the current and next map.
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function showNextMap() : string;

    /**
     * AdminBroadcast command.
     * Broadcasts the given message on the server.
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminBroadcast(string $msg) : bool;

    /**
     * AdminRestartMatch command.
     * Broadcasts the given message on the server.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminRestartMatch() : bool;

    /**
     * AdminRestartMatch command.
     * Broadcasts the given message on the server.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminEndMatch() : bool;

    /**
     * AdminSetMaxNumPlayers command.
     * Sets the max amount of players (public).
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetMaxNumPlayers(int $slots) : bool;

    /**
     * AdminSetMaxNumPlayers command.
     * Sets the max amount of players (public).
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetServerPassword(string $password) : bool;

    /**
     * AdminChangeLevel command.
     * Change the level ( and pick a random layer on it) and travel to it immediately,
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminChangeLevel(string $level) : bool;

    /**
     * AdminSetNextLevel command.
     * Set the next Level ( and pick a random layer on it) to travel to after this match ends.
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetNextLevel(string $level) : bool;

    /**
     * AdminChangeLayer command.
     * Change the layer and travel to it immediately.
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminChangeLayer(string $layer) : bool;

    /**
     * AdminSetNextLayer command.
     * Set the next layer to travel to after this match ends.
     * 
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetNextLayer(string $layers) : bool;

    /**
     * AdminVoteLevel command.
     * Trigger a Level Vote for immediate change.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminVoteLevel(string $levels) : bool;

    /**
     * AdminVoteLayer command.
     * Trigger a Layer Vote for immediate change.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminVoteLayer(string $layers) : bool;

    /**
     * AdminVoteNextLevel command.
     * Trigger a Level Vote for next match.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminVoteNextLevel(string $levels) : bool;

    /**
     * AdminVoteNextLayer command.
     * Trigger a Layer Vote for next match.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminVoteNextLayer(string $layer) : bool;

    /**
     * AdminVote command.
     * Ingame text vote.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminVote(string $name, string $choices) : bool;

    /**
     * AdminSlomo command.
     * Sets the game speed with the AdminSlomo
     * command. Providing no parameter will set
     * the speed to default.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSlomo(float $timeDilation = 1.0) : bool;

    /**
     * AdminForceTeamChange command.
     * Forces a player to the opposite team
     * by providing the name or steamid.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminForceTeamChange(string $nameOrSteamId) : bool;

    /**
     * AdminForceTeamChangeById command.
     * Forces a player to the opposite team
     * by providing the ingame Player id.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminForceTeamChangeById(int $playerId) : bool;

    /**
     * AdminDemoteCommander command.
     * Demotes a player from the commander slot
     * by providing the name or steamid.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    //public function adminDemoteCommander(string $nameOrSteamId) : bool;

    /**
     * AdminDemoteCommanderById command.
     * Demotes a player from the commander slot
     * by providing the ingame Player id.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    //public function adminDemoteCommanderById(int $playerId) : bool;

    /**
     * AdminDisbandSquad command.
     * Disbands a Squad by providing the Team id  / index & Squad id / index.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminDisbandSquad(int $teamId, int $squadId) : bool;

    /**
     * AdminRemovePlayerFromSquad command.
     * Removes a Player from his Squad by providing
     * the Player name.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminRemovePlayerFromSquad(string $playerName) : bool;

    /**
     * AdminRemovePlayerFromSquadById command.
     * Removes a player from his Squad by providing
     * the ingame Player id.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminRemovePlayerFromSquadById(int $playerId) : bool;

    /**
     * AdminWarn command.
     * Warns a Player by providing his name / steamid
     * and a message.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminWarn(string $nameOrSteamId, string $warnReason) : bool;

    /**
     * AdminWarnById command.
     * Warns a Player by providing his ingame Player id
     * and a message.
     *
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminWarnById(int $playerId, string $warnReason) : bool;

    /**
     * Disconnects the runner from any squad server instance.
     *
     * @return void
     */
    public function disconnect() : void;
}