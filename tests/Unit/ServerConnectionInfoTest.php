<?php

namespace DSG\SquadRCON\Tests\Unit;

use DSG\SquadRCON\Data\Objects\ServerConnectionInfo;

class ServerConnectionInfoTest extends \DSG\SquadRCON\Tests\TestCase {
    /**
     * Validates the ServerConnectionInfo can be initialized
     * 
     * @return void
     */
    public function test_server_connection_info_can_be_initialized()
    {
        $info = new ServerConnectionInfo(
            host: 'localhost', 
            port: 12345, 
            password: 'secret'
        );
        
        $this->assertTrue((bool)$info);
    }

    /**
     * Validates that the provided timeout is validated.
     * 
     * @return void
     */
    public function test_server_connection_info_timeout_being_validated()
    {
        $this->expectException(\Spatie\DataTransferObject\Exceptions\ValidationException::class);
        
        new ServerConnectionInfo(
            host: 'localhost', 
            port: 12345, 
            password: 'secret', 
            timeout: 0
        );
    }
}