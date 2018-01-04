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

require_once ("./util/user_role.php");

class Player
{

    private $id;

    private $firstname;

    private $lastname;

    private $mail;

    private $club;

    private $dwz;

    private $elo;

    private $telephone;

    private $cellphone;

    private $role;

    private $password;

    private $is_active;

    public function getClub (): string
    {
        return $this->club;
    }

    public function getDwz (): int
    {
        return $this->dwz;
    }

    public function getElo (): int
    {
        return $this->elo;
    }

    public function getTelephone (): string
    {
        return $this->telephone;
    }

    public function getCellphone (): string
    {
        return $this->cellphone;
    }

    public function getPassword (): string
    {
        return $this->password;
    }

    public function setFirstname (string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function setLastname (string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function setMail (string $mail): void
    {
        $this->mail = $mail;
    }

    public function setClub (string $club): void
    {
        $this->club = $club;
    }

    public function setDwz (int $dwz): void
    {
        $this->dwz = $dwz;
    }

    public function setElo (int $elo): void
    {
        $this->elo = $elo;
    }

    public function setTelephone (string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setCellphone (string $cellphone): void
    {
        $this->cellphone = $cellphone;
    }

    public function setRole (UserRole $role): void
    {
        $this->role = $role;
    }

    public function setPassword (string $password): void
    {
        $this->password = $password;
    }

    public function getMail (): string
    {
        return $this->mail;
    }

    public function getFirstname (): string
    {
        return $this->firstname;
    }

    public function getLastname (): string
    {
        return $this->lastname;
    }

    public function getIsActive (): int
    {
        return $this->is_active;
    }

    function __construct (string $firstname, string $lastname, string $mail,
            string $club, int $dwz, int $elo, string $telephone,
            string $cellphone, string $role, string $password, int $isActive = 1)
    {
        $this->role = new UserRole();
        $this->role->setUserRoleDescription(trim($role));
        
        $this->firstname = trim($firstname);
        $this->lastname = trim($lastname);
        $this->mail = trim($mail);
        $this->club = trim($club);
        $this->dwz = $dwz;
        $this->elo = $elo;
        $this->telephone = trim($telephone);
        $this->cellphone = trim($cellphone);
        $this->password = trim($password);
        $this->is_active = $isActive;
        $this->id = - 1; // the final id is generated later automatically by the
                         // database
        
        if ($this->lastname == "") {
            $this->lastname = "Unbekannt";
        }
    }

    public function __toString (): string
    {
        $playerDataAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Spielernummer:</td><td>' .
                 "$this->id" . '</td></tr><tr><td>Vorname:</td><td>' .
                 "$this->firstname" . '</td></tr><tr><td>Nachname:</td><td>' .
                 "$this->lastname" . '</td></tr><tr><td>E-mail:</td><td>' .
                 "$this->mail" . '</td></tr><tr><td>Verein:</td><td>' .
                 "$this->club" . '</td></tr><tr><td>DWZ:</td><td>' . "$this->dwz" .
                 '</td></tr><tr><td>ELO:</td><td>' . "$this->elo" .
                 '</td></tr><tr><td>Festnetznummer:</td><td>' .
                 "$this->telephone" . '</td></tr><tr><td>Mobilnummer:</td><td>' .
                 "$this->cellphone" . '</td></tr><tr><td>Nimmt am aktuellen Turnier tei:</td><td>' .
                 "$this->is_active" . '</td></tr><tr><td>Rolle:</td><td>' .
                 "$this->role" . '</td></tr></table>';
        
        return $playerDataAsHTMLTable;
    }

    public function getInsertSQL (string $tablename): string
    {
        // the player id will be generated by the database and is not part of
        // the SQL statement
        $insertSQL = 'INSERT INTO ' . $tablename .
                 " (first_name, last_name, e_mail, club, dwz, elo, phone, mobile, role, is_active, passwd) " .
                 " values (' " . "$this->firstname" . " ' , ' " .
                 "$this->lastname" . " ' , ' " . "$this->mail" . " ' , ' " .
                 "$this->club" . " ' , ' " . "$this->dwz" . " ' , ' " .
                 "$this->elo" . " ' , ' " . "$this->telephone" . " ' , ' " .
                 "$this->cellphone" . " ' , ' " . "$this->role" . " ' , ' " .
                 "$this->is_active" . " ' , ' " . "$this->password" . " ' )";
        
        return $insertSQL;
    }

    public function getUpdateSQL (string $tablename): string
    {
        $updateSQL = 'UPDATE ' . $tablename . ' SET ' . "first_name = ' " .
                 "$this->firstname" . "', " . "last_name = ' " .
                 "$this->lastname" . "', " . "e_mail = ' " . "$this->mail" .
                 "', " . "club = ' " . "$this->club" . "', " . "dwz = ' " .
                 "$this->dwz" . "', " . "elo = ' " . "$this->elo" . "', " .
                 "phone = ' " . "$this->telephone" . "', " . "mobile = ' " .
                 "$this->cellphone" . "', " . "role = ' " . "$this->role" . "', " .
                 "is_active = ' " . "$this->is_active" . "', " . "passwd = ' " .
                 "$this->password" . "' " . " WHERE id = '" . $this->id . "'";
        
        return $updateSQL;
    }

    public function isPlayerValid (): bool
    {
        return ($this->lastnameIsValid() && $this->mailIsValid() &&
                 $this->dwzIsValid() && $this->eloIsValid() &&
                 $this->roleIsValid() && $this->isActiveIsValid());
    }

    public function getRole (): UserRole
    {
        return $this->role;
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function setId (int $a_id)
    {
        $this->id = $a_id;
    }

    public function setIsActive (int $isActive)
    {
        $this->is_active = $isActive;
    }

    public function getPlayerAsTableRow (): string
    {
        return "<tr><td><a href='player_update.php?id=" . $this->id . "'>" .
                 $this->id . "</a></td><td>" . "$this->lastname" . "</td><td>" .
                 "$this->firstname" . "</td>" . "<td>" . "$this->mail" .
                 "</td><td>" . "$this->telephone" . "</td><td>" .
                 "$this->cellphone" . "</td><td>" . "$this->club" . "</td><td>" .
                 "$this->dwz" . "</td><td>" . "$this->elo" . "</td><td>" .
                 "$this->role" . "</td></tr>";
    }

    private function lastnameIsValid (): bool
    {
        return (isset($this->lastname) && is_string($this->lastname));
    }

    private function mailIsValid (): bool
    {
        return (isset($this->mail) && is_string($this->mail));
    }

    private function dwzIsValid (): bool
    {
        return (($this->dwz >= 0) && ($this->dwz < 2900));
    }

    private function eloIsValid (): bool
    {
        return (($this->elo >= 0) && ($this->elo < 2900));
    }

    private function isActiveIsValid (): bool
    {
        return (($this->is_active == 0) || ($this->is_active == 1));
    }

    private function roleIsValid (): bool
    {
        return (($this->role->isRoleValid()));
    }
}
?>
