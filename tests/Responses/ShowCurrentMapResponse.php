<?php

namespace DSG\SquadRCON\Tests\Runners\Responses;

class ShowCurrentMapResponse {
    public static function get() {
        return <<<EOT
Current level is Al Basrah, layer is Belaya AAS v1
EOT;
    }
}