<?php
declare(strict_types = 1);

/*
 * Copyright (C) 2018 Hanns Schulz-Mirbach, <http://www.schulz-mirbach.com/>
 *
 * This file is part of the KiekMolWedderIn tournament administration program. 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or any later
 * version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/gpl.html/>
 */

require_once ("./model/player.php");
require_once ("./db/database.php");

class PlayerController
{

    private $player;

    private $database;

    public function __construct ()
    {
        $this->database = new Database();
        $this->player = new Player("", "", "", "", - 1, - 1, "", "", "Anonymous",
                "");
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function getPlayer (): Player
    {
        return $this->player;
    }

    public function setPlayer ($aPlayer): void
    {
        $this->player = $aPlayer;
    }

    public function insertPlayer (): int
    {
        if (! is_null($this->player)) {
            return $this->database->insertPlayer($this->player);
        } else {
            return 0;
        }
    }

    public function updatePlayer (): int
    {
        if (! is_null($this->player) && $this->player->isPlayerValid()) {
            return $this->database->updatePlayer($this->player);
        } else {
            return 0;
        }
    }

    public function getPlayerById (int $playerId): Player
    {
        return $this->database->getPlayerById($playerId);
    }

    public function getAllPlayers (): array
    {
        return $this->database->getAllPlayers();
    }
    
    public function getAllActivePlayers (): array
    {
        return $this->database->getAllActivePlayers();
    }
    
    public function getAllActivePlayersWithoutSelf (int $selfId): array
    {
        return $this->database->getAllActivePlayersWithoutSelf($selfId);
    }
    
    
    public function anyPlayerExistsInDatabase (): bool
    {
        return $this->database->anyPlayerExistsInDatabase();
    }
    
    public function deactivateAllPlayers (): int
    {
        return $this->database->deactivateAllPlayers();
    } 
}

?>