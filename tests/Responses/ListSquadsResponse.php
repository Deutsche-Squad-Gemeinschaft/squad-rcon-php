<?php

namespace DSG\SquadRCON\Tests\Runners\Responses;

class ListSquadsResponse {
    public static function get() {
        return <<<EOT
----- Active Squads -----
Team ID: 1 (United States Army)
ID: 1 | Name: HELI | Size: 1 | Locked: True | Creator Name: [1JGKP]StryexX | Creator Steam ID: 76561198429663037
ID: 2 | Name: HELI | Size: 1 | Locked: True | Creator Name: [BOS]mobb | Creator Steam ID: 76561197990281056
ID: 3 | Name: CMD Squad | Size: 9 | Locked: False | Creator Name: [1JGKP]Bud-Muecke (YT) | Creator Steam ID: 76561198202943394
ID: 4 | Name: MBT | Size: 5 | Locked: True | Creator Name: Flexルーシー | Creator Steam ID: 76561198159379914
ID: 5 | Name: BRADLEY | Size: 2 | Locked: True | Creator Name: Jim2509 | Creator Steam ID: 76561198102527401
ID: 6 | Name: STRYKER | Size: 2 | Locked: False | Creator Name: Jannik | Creator Steam ID: 76561198068361421
ID: 7 | Name: BOS SACHEN MACHEN | Size: 9 | Locked: False | Creator Name: Borg | Creator Steam ID: 76561198349811676
ID: 8 | Name: RUNNING SQUAD | Size: 8 | Locked: False | Creator Name: HugoBadAss92_DEU | Creator Steam ID: 76561198450388317
Team ID: 2 (Russian Ground Forces)
ID: 1 | Name: STURMTRUPP | Size: 6 | Locked: True | Creator Name: [66th] Devilukedude | Creator Steam ID: 76561198420739023
ID: 2 | Name: CMD Squad | Size: 9 | Locked: False | Creator Name: Bene o_O | Creator Steam ID: 76561198056033287
ID: 3 | Name: LOGI GER | Size: 1 | Locked: True | Creator Name: *Ragnar_Lotbrock82* | Creator Steam ID: 76561198054760135
ID: 4 | Name: BMP GER | Size: 2 | Locked: True | Creator Name: Ltd_Dan_FirstPlatoon | Creator Steam ID: 76561198047078003
ID: 5 | Name: GER MIC | Size: 8 | Locked: False | Creator Name: [GER] BlueBoxT2 | Creator Steam ID: 76561198039847055
ID: 6 | Name: (DE) HELI 1 | Size: 1 | Locked: True | Creator Name: Sykkel | Creator Steam ID: 76561198237497942
ID: 7 | Name: MBT | Size: 2 | Locked: True | Creator Name: [TTW-Gen]RaiderRange | Creator Steam ID: 76561198141660689
ID: 8 | Name: CHOPPA | Size: 3 | Locked: True | Creator Name: BreiterBart | Creator Steam ID: 76561198110126580
ID: 9 | Name: SCOUT CAR | Size: 1 | Locked: True | Creator Name: =EBS= FuriousBaco | Creator Steam ID: 76561198042102731
ID: 10 | Name: GER INF | Size: 6 | Locked: False | Creator Name: Aegmar | Creator Steam ID: 76561197963356626
EOT;
    }
}
