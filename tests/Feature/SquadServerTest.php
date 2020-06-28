<?php

namespace DSG\SquadRCON\Tests\Feature;

use DSG\SquadRCON\Data\ServerConnectionInfo;
use DSG\SquadRCON\SquadServer;
use DSG\SquadRCON\Tests\Runners\TestingCommandRunner;

class SquadServerTest extends \DSG\SquadRCON\Tests\TestCase {
    private SquadServer $squadServer;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->squadServer = new SquadServer(new ServerConnectionInfo('', 0, ''), new TestingCommandRunner());
    }

    /**
     * Verifies the currentMap can properly be retrieved.
     *
     * @return void
     */
    public function test_current_map()
    {
        $this->assertSame('Al Basrah AAS v1', $this->squadServer->currentMap());
    }

    /**
     * Verifies the nextMap can properly be retrieved.
     *
     * @return void
     */
    public function test_next_map()
    {
        $this->assertSame('Belaya AAS v1', $this->squadServer->nextMap());
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
            if ($player->getId() === 88) {
                $this->assertSame(195, $player->getDisconnectedSince());
            } else if ($player->getId() === 84) {
                $this->assertSame(108, $player->getDisconnectedSince());
            } else if ($player->getId() === 42) {
                $this->assertSame(3, $player->getDisconnectedSince());
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
            if ($team->getId() === 1) {
                $this->assertSame('United States Army', $team->getName());
                $this->assertCount(8, $team->getSquads());

                foreach ($team->getSquads() as $squad) {
                    if ($squad->getId() === 1) {
                        $this->assertSame('HELI', $squad->getName());
                    } else if ($squad->getId() === 2) {
                        $this->assertSame('HELI', $squad->getName());
                    } else if ($squad->getId() === 3) {
                        $this->assertSame('CMD Squad', $squad->getName());
                    } else if ($squad->getId() === 4) {
                        $this->assertSame('MBT', $squad->getName());
                    } else if ($squad->getId() === 5) {
                        $this->assertSame('BRADLEY', $squad->getName());
                    } else if ($squad->getId() === 6) {
                        $this->assertSame('STRYKER', $squad->getName());
                    } else if ($squad->getId() === 7) {
                        $this->assertSame('BOS SACHEN MACHEN', $squad->getName());
                    } else if ($squad->getId() === 8) {
                        $this->assertSame('RUNNING SQUAD', $squad->getName());
                    }
                }
            } else {
                $this->assertSame('Russian Ground Forces', $team->getName());
                $this->assertCount(10, $team->getSquads());
            }

            $squadCount += count($team->getSquads());
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
        $teams = $this->squadServer->serverPopulation();

        $squadCount = 0;
        $playerCount = 0;
        foreach ($teams as $team) {
            $squadCount += count($team->getSquads());
            $teamPlayerCount = count($team->getPlayers());
            foreach ($team->getSquads() as $squad) {
                $teamPlayerCount += count($squad->getPlayers());
            }
            $playerCount += $teamPlayerCount;

            if ($team->getId() === 1) {
                $this->assertSame('United States Army', $team->getName());
                $this->assertCount(8, $team->getSquads());
                $this->assertSame(38, $teamPlayerCount);

                foreach ($team->getSquads() as $squad) {
                    if ($squad->getId() === 3) {
                        $this->assertSame('CMD Squad', $squad->getName());
                        $this->assertSame(9, $squad->getSize());
                        $this->assertFalse(false, $squad->isLocked());
                        $this->assertSame($team->getId(), $squad->getTeam()->getId());

                        $p = null;
                        /** @var \DSG\SquadRCON\Data\Player $player */
                        foreach ($squad->getPlayers() as $player) {
                            if ($player->getId() === 53) {
                                $this->assertSame('76561198202943394', $player->getSteamId());
                                $this->assertSame('[1JGKP]Bud-Muecke (YT)', $player->getName());
                                $this->assertSame($squad->getId(), $player->getSquad()->getId());
                                $p = $player;
                            }
                        }

                        $this->assertNotNull($p);
                    }
                }
            } else {
                $this->assertSame('Russian Ground Forces', $team->getName());
                $this->assertCount(10, $team->getSquads());
                $this->assertSame(39, $teamPlayerCount);
            }
        }
        
        $this->assertSame(18, $squadCount);
        $this->assertSame(77, $playerCount);
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
        $this->assertTrue($this->squadServer->adminChangeMap('Al Basrah AAS v1'));
    }

    /**
     * Verifies the set next map command does work properly
     * 
     * @return void
     */
    public function test_admin_set_next_map()
    {
        $this->assertTrue($this->squadServer->adminSetNextMap('Al Basrah AAS v1'));
    }

    /**
     * Verifies the restart match command does work properly
     * 
     * @return void
     */
    public function test_admin_restart_match()
    {
        $this->assertTrue($this->squadServer->restartMatch());
    }

    /**
     * Verifies the end match command does work properly
     * 
     * @return void
     */
    public function test_admin_end_match()
    {
        $this->assertTrue($this->squadServer->endMatch());
    }

    /**
     * Verifies the admin set max num players command does work properly
     * 
     * @return void
     */
    public function test_admin_set_max_num_players()
    {
        $this->assertTrue($this->squadServer->setSlots(78));
    }

    /**
     * Verifies the admin set max num players command does work properly
     * 
     * @return void
     */
    public function test_admin_set_password()
    {
        $this->assertTrue($this->squadServer->setPassword('secret'));
    }

    /**
     * Verifies the kick command does work properly
     * 
     * @return void
     */
    public function test_admin_kick()
    {
        $this->assertTrue($this->squadServer->kick('Marcel', 'Test'));
    }

    /**
     * Verifies the kick by id command does work properly
     * 
     * @return void
     */
    public function test_admin_kick_by_id()
    {
        $this->assertTrue($this->squadServer->kickById(1, 'Test'));
    }

    /**
     * Verifies the ban command does work properly
     * 
     * @return void
     */
    public function test_admin_ban()
    {
        $this->assertTrue($this->squadServer->ban('Marcel', '1h', 'Test'));
    }

    /**
     * Verifies the ban by id command does work properly
     * 
     * @return void
     */
    public function test_admin_ban_by_id()
    {
        $this->assertTrue($this->squadServer->banById(1, '1h', 'Test'));
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