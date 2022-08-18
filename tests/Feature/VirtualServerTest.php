<?php

namespace DSG\SquadRCON\Tests\Feature;

use DSG\SquadRCON\Data\Objects\ServerConnectionInfo;
use DSG\SquadRCON\SquadServer;
use DSG\SquadRCON\Tests\Runners\TestingCommandRunner;

class VirtualServerTest extends \DSG\SquadRCON\Tests\TestCase {
    private SquadServer $squadServer;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->squadServer = new SquadServer(new ServerConnectionInfo(
            host: '', 
            port: 0, 
            password: ''
        ), new TestingCommandRunner());
    }

    /**
     * Verifies the currentMap can properly be retrieved.
     *
     * @return void
     */
    public function test_current_map()
    {
        $this->assertSame([
            'level' => 'Al Basrah',
            'layer' => 'Al Basrah AAS v1'
        ], $this->squadServer->showCurrentMap());
    }

    /**
     * Verifies the nextMap can properly be retrieved.
     *
     * @return void
     */
    public function test_next_map()
    {
        $this->assertSame([
            'level' => 'Al Basrah',
            'layer' => 'Al Basrah AAS v1'
        ], $this->squadServer->showNextMap());
    }

    /**
     * Verifies the player list can properly be retrieved.
     *
     * @return void
     */
    public function test_list_players()
    {
        $players = $this->squadServer->listPlayers();

        $this->assertCount(77, $players);
    }

    /**
     * Verifies the disconnected player list can properly be retrieved.
     *
     * @return void
     */
    public function test_list_disconnected_players()
    {
        $playerList = $this->squadServer->listDisconnectedPlayers();

        $this->assertCount(3, $playerList);

        foreach ($playerList as $player) {
            if ($player->id === 88) {
                $this->assertSame(195, $player->disconnectedSince);
            } else if ($player->id === 84) {
                $this->assertSame(108, $player->disconnectedSince);
            } else if ($player->id === 42) {
                $this->assertSame(3, $player->disconnectedSince);
            }
        }
    }

    /**
     * Verifies the team/squad list can properly be retrieved.
     *
     * @return void
     */
    public function test_list_squads()
    {
        $teams = $this->squadServer->listSquads();

        $squadCount = 0;

        foreach ($teams as $team) {
            if ($team->id === 1) {
                $this->assertSame('United States Army', $team->name);
                $this->assertCount(8, $team->squads);

                foreach ($team->squads as $squad) {
                    if ($squad->id === 1) {
                        $this->assertSame('HELI', $squad->name);
                    } else if ($squad->id === 2) {
                        $this->assertSame('HELI', $squad->name);
                    } else if ($squad->id === 3) {
                        $this->assertSame('CMD Squad', $squad->name);
                    } else if ($squad->id === 4) {
                        $this->assertSame('MBT', $squad->name);
                    } else if ($squad->id === 5) {
                        $this->assertSame('BRADLEY', $squad->name);
                    } else if ($squad->id === 6) {
                        $this->assertSame('STRYKER', $squad->name);
                    } else if ($squad->id === 7) {
                        $this->assertSame('BOS SACHEN MACHEN', $squad->name);
                    } else if ($squad->id === 8) {
                        $this->assertSame('RUNNING SQUAD', $squad->name);
                    }
                }
            } else {
                $this->assertSame('Russian Ground Forces', $team->name);
                $this->assertCount(10, $team->squads);
            }

            $squadCount += count($team->squads);
        }
        
        $this->assertSame(18, $squadCount);
    }

    /**
     * Verifies the server population can properly be retrieved.
     *
     * @return void
     */
    public function test_server_population()
    {
        $population = $this->squadServer->serverPopulation();

        $this->assertTrue(!!count($population->teams));

        $t = reset($population->teams);
        $this->assertNotNull($t);
        $this->assertSame('United States Army', $t->name);

        $this->assertFalse(array_key_exists(3, $population->teams));

        $this->assertSame(77, count($population->players()));

        $squadCount = 0;
        $playerCount = 0;
        foreach ($population->teams as $team) {
            $squadCount += count($team->squads);
            $teamPlayerCount = count($team->players);
            foreach ($team->squads as $squad) {
                $teamPlayerCount += count($squad->players);
            }
            $playerCount += $teamPlayerCount;

            if ($team->id === 1) {
                $this->assertSame('United States Army', $team->name);
                $this->assertCount(8, $team->squads);
                $this->assertSame(38, $teamPlayerCount);

                foreach ($team->squads as $squad) {
                    if ($squad->id === 3) {
                        $this->assertSame('CMD Squad', $squad->name);
                        $this->assertSame(9, $squad->size);
                        $this->assertFalse(false, $squad->locked);
                        $this->assertSame('[1JGKP]Bud-Muecke (YT)', $squad->creatorName);
                        $this->assertSame('76561198202943394', $squad->creatorSteamId);
                        $this->assertSame('76561198202943394', $squad->leader()->steamId);
                        
                        $p = null;
                        /** @var \DSG\SquadRCON\Data\Player $player */
                        foreach ($squad->players as $player) {
                            if ($player->id === 53) {
                                $this->assertSame('76561198202943394', $player->steamId);
                                $this->assertTrue($player->leader);
                                $this->assertSame('[1JGKP]Bud-Muecke (YT)', $player->name);
                                $this->assertSame('USA_Marksman_01', $player->role);
                                $p = $player;
                            }
                        }

                        $this->assertNotNull($p);
                    }
                }
            } else {
                $this->assertSame('Russian Ground Forces', $team->name);
                $this->assertCount(10, $team->squads);
                $this->assertSame(39, $teamPlayerCount);
            }
        }
        
        $this->assertSame(18, $squadCount);
        $this->assertSame(77, $playerCount);

        $player = $population->getPlayerBySteamId('76561198202943394');
        $this->assertNotNull($player);
        $this->assertSame(53, $player->id);
        $this->assertSame('[1JGKP]Bud-Muecke (YT)', $player->name);

        $this->assertNull($population->getPlayerBySteamId('DoesCertainlyNotExist'));
    }

    /**
     * Verifies the broadcast command does work properly
     * 
     * @return void
     */
    public function test_admin_Broadcast()
    {
        $this->assertTrue($this->squadServer->adminBroadcast('Hello World!'));
    }

    /**
     * Verifies the change map command does work properly
     * 
     * @return void
     */
    public function test_admin_change_map()
    {
        $this->assertTrue($this->squadServer->adminChangeLayer('Albasrah_AAS_v1'));
    }

    /**
     * Verifies the set next map command does work properly
     * 
     * @return void
     */
    public function test_admin_set_next_map()
    {
        $this->assertTrue($this->squadServer->adminSetNextLayer('Albasrah_AAS_v1'));
    }

    /**
     * Verifies the restart match command does work properly
     * 
     * @return void
     */
    public function test_admin_restart_match()
    {
        $this->assertTrue($this->squadServer->adminRestartMatch());
    }

    /**
     * Verifies the end match command does work properly
     * 
     * @return void
     */
    public function test_admin_end_match()
    {
        $this->assertTrue($this->squadServer->adminEndMatch());
    }

    /**
     * Verifies the admin set max num players command does work properly
     * 
     * @return void
     */
    public function test_admin_set_max_num_players()
    {
        $this->assertTrue($this->squadServer->adminSetMaxNumPlayers(78));
    }

    /**
     * Verifies the admin set max num players command does work properly
     * 
     * @return void
     */
    public function test_admin_set_password()
    {
        $this->assertTrue($this->squadServer->adminSetServerPassword('secret'));
    }

    /**
     * Verifies the kick command does work properly
     * 
     * @return void
     */
    public function test_admin_kick()
    {
        $this->assertTrue($this->squadServer->adminKick('Marcel', 'Test'));
    }

    /**
     * Verifies the kick by id command does work properly
     * 
     * @return void
     */
    public function test_admin_kick_by_id()
    {
        $this->assertTrue($this->squadServer->adminKickById(1, 'Test'));
    }

    /**
     * Verifies the ban command does work properly
     * 
     * @return void
     */
    public function test_admin_ban()
    {
        $this->assertTrue($this->squadServer->adminBan('Marcel', '1h', 'Test'));
    }

    /**
     * Verifies the ban by id command does work properly
     * 
     * @return void
     */
    public function test_admin_ban_by_id()
    {
        $this->assertTrue($this->squadServer->adminBanById(1, '1h', 'Test'));
    }

    /**
     * Verifies the disconnect method works without any exception
     * 
     * @return void
     */
    public function test_squad_server_admin_slomo()
    {
        $this->assertTrue($this->squadServer->adminSlomo(2));
    }

    /**
     * Verifies the disconnect method works without any exception
     * 
     * @return void
     */
    public function test_squad_server_admin_force_team_change()
    {
        $this->assertTrue($this->squadServer->adminForceTeamChange('Test'));
    }

    /**
     * Verifies the disconnect method works without any exception
     * 
     * @return void
     */
    public function test_squad_server_admin_force_team_change_by_id()
    {
        $this->assertTrue($this->squadServer->adminForceTeamChange(0));
    }

    /**
     * Verifies the disconnect method works without any exception
     * 
     * @return void
     */
    public function test_squad_server_admin_disband_squad()
    {
        $this->assertTrue($this->squadServer->adminForceTeamChange(1, 1));
    }

    /**
     * Verifies the disconnect method works without any exception
     * 
     * @return void
     */
    public function test_squad_server_admin_remove_player_from_squad()
    {
        $this->assertTrue($this->squadServer->adminRemovePlayerFromSquad('Test'));
    }

    /**
     * Verifies the disconnect method works without any exception
     * 
     * @return void
     */
    public function test_squad_server_admin_remove_player_from_squad_by_id()
    {
        $this->assertTrue($this->squadServer->adminRemovePlayerFromSquadById(0));
    }

    /**
     * Verifies the disconnect method works without any exception
     * 
     * @return void
     */
    public function test_squad_server_admin_warn()
    {
        $this->assertTrue($this->squadServer->adminWarn('Test', 'Hello World!'));
    }

    /**
     * Verifies the disconnect method works without any exception
     * 
     * @return void
     */
    public function test_squad_server_admin_warn_by_id()
    {
        $this->assertTrue($this->squadServer->adminWarnById(0, 'Hello World!'));
    }

    /**
     * Verifies the disconnect method works without any exception
     * 
     * @return void
     */
    public function test_squad_server_disconnect()
    {
        $this->assertNull($this->squadServer->disconnect());
    }
}
