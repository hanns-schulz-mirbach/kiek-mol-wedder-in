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

class LoginController
{

    private $e_mail;

    private $passwd;

    private $player;

    private $database;

    public function __construct (string $a_email, string $a_passwd)
    {
        $this->e_mail = $a_email;
        $this->passwd = $a_passwd;
        $this->database = new Database();
        
        // determine player matching the provided e_mail and passwd
        $this->player = $this->database->getPlayerByEmailAndPassword(
                $this->e_mail, $this->passwd);
    }

    public function isPlayerValid (): bool
    {
        if (isset($this->player)) {
            return $this->player->isPlayerValid();
        } else {
            return false;
        }
    }

    public function addPlayerDataToSession (): void
    {
        $_SESSION['user_role'] = $this->player->getRole()->getUserRoleDescription();
        $_SESSION['user_name'] = $this->player->getMail();
        $_SESSION['user_id'] = $this->player->getId();
    }
    
    public function getPlayer ()
    {
        return $this->player;
    }
}

?>