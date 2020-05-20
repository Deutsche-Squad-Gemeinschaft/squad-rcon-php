<div align="center">
    <h1>Squad RCON PHP</h1>
    <b>RCON PHP wrapper for Squad server management</b>
    <hr>
    <a href="https://dsg-gaming.de">
        <img alt="Deutsche Squad Gemeinschaft" src="https://raw.githubusercontent.com/Deutsche-Squad-Gemeinschaft/battlemetrics-php/master/dsg-badge.svg">
    </a>
    <a href="https://github.com/Deutsche-Squad-Gemeinschaft/squad-rcon-php/actions">
        <img alt="Deutsche Squad Gemeinschaft" src="https://github.com/Deutsche-Squad-Gemeinschaft/squad-rcon-php/workflows/CI/badge.svg">
    </a>
    <a href="https://codecov.io/gh/Deutsche-Squad-Gemeinschaft/squad-rcon-php">
        <img alt="Deutsche Squad Gemeinschaft" src="https://codecov.io/gh/Deutsche-Squad-Gemeinschaft/squad-rcon-php/branch/master/graph/badge.svg">
    </a>
    <a href="https://packagist.org/packages/dsg/squad-rcon-php">
        <img alt="Total Downloads" src="https://poser.pugx.org/dsg/squad-rcon-php/downloads.png">
    </a>
    <a href="https://packagist.org/packages/dsg/squad-rcon-php">
        <img alt="Latest Stable Version" src="https://poser.pugx.org/dsg/squad-rcon-php/v/stable">
    </a>
    <a href="https://packagist.org/packages/dsg/squad-rcon-php">
        <img alt="Latest Unstable Version" src="https://poser.pugx.org/dsg/squad-rcon-php/v/unstable">
    </a>
    <a href="https://packagist.org/packages/dsg/squad-rcon-php">
        <img alt="License" src="https://poser.pugx.org/dsg/squad-rcon-php/license">
    </a>
</div>

## Installation

You can install this package by using composer and the following command:
```
composer require dsg/squad-rcon-php
```

The code will then be available under the `DSG\SquadRCON` namespace.

## Commands

* [x] ListPlayers
* [x] ListSquads
* [x] AdminListDisconnectedPlayers
* [x] ShowNextMap
* [x] AdminKick "\<NameOrSteamId\>" \<KickReason\>
* [x] AdminKickById \<PlayerId\> \<KickReason\>
* [x] AdminBan "\<NameOrSteamId\>" "\<BanLength\>" \<BanReason\>
* [x] AdminBanById \<PlayerId\> "\<BanLength\>" \<BanReason\>
* [x] AdminBroadcast \<Message\>
* [x] AdminRestartMatch
* [x] AdminEndMatch
* [x] AdminChangeMap \<MapName\>
* [x] AdminSetNextMap \<MapName\>
* [x] AdminSetMaxNumPlayers \<NumPlayers\>
* [x] AdminSetServerPassword \<Password\>

## USAGE

### Create an instance
```php
$server = new SquadServer(new ServerConnectionInfo('127.0.0.1', 21114, 'YourRconPassword'));
```

### Get current server population (Teams, Squads, Players)
```php
$players = $server->serverPopulation();
```

### Get currently active players
```php
$players = $server->listPlayers();
```

### Get disconnected players
```php
$players = $server->listDisconnectedPlayers();
```

### Get currently active squads (and teams)
```php
$players = $server->listSquads();
```

### Kick a player
```php
$success = $server->kick('76561197960287930', '1h', 'Reason');
$success = $server->kickById($player->getId()', '1h', 'Reason');
```

### Ban a player
```php
$success = $server->ban('76561197960287930', '1h', 'Reason');
$success = $server->banById($player->getId()', '1h', 'Reason');
```

### Get the current map
```php
$map = $server->currentMap();
```

### Get the next map
```php
$map = $server->nextMap();
```

### Restart the current match
```php
$success = $server->restartMatch();
```

### End the current match
```php
$success = $server->endMatch();
```

### Broadcast message to all players on the server
```php
$success = $server->broadcastMessage('Hello from the other side');
```

### Change map (end current game)
```php
$success = $server->changeMap('Sumari AAS v1');
```

### Set next map
```php
$success = $server->nextMap('Sumari AAS v1');
```

### Set the maximum amount of players / slots
```php
$success = $server->setSlots(80);
```

### Set the server password
```php
$success = $server->setPassword('secret');
```