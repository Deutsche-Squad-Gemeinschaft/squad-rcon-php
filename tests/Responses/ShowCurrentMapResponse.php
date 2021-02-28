<?php

namespace DSG\SquadRCON\Tests\Runners\Responses;

class ShowCurrentMapResponse {
    public static function get() {
        return <<<EOT
Current level is Al Basrah, layer is Al Basrah AAS v1
EOT;
    }
}