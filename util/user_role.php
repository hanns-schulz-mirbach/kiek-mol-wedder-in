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

class UserRole
{

    private $userRoleDescription;

    public function getUserRoleDescription (): string
    {
        return $this->userRoleDescription;
    }

    public function setUserRoleDescription (string $userRole): void
    {
        $this->userRoleDescription = trim($userRole);
    }

    public function __construct ()
    {
        $this->userRoleDescription = 'Anonymous';
    }

    public function __toString (): string
    {
        return $this->userRoleDescription;
    }

    public function isAnonymous (): bool
    {
        return (($this->userRoleDescription === 'Anonymous') ||
                 ($this->userRoleDescription === 'Gast') ||
                 ($this->userRoleDescription === 'Spieler') ||
                 ($this->userRoleDescription === 'Turnierleiter') ||
                 ($this->userRoleDescription === 'Administrator'));
    }

    public function isGuest (): bool
    {
        return (($this->userRoleDescription === 'Gast') ||
                 ($this->userRoleDescription === 'Spieler') ||
                 ($this->userRoleDescription === 'Turnierleiter') ||
                 ($this->userRoleDescription === 'Administrator'));
    }

    public function isPlayer (): bool
    {
        return (($this->userRoleDescription === 'Spieler') ||
                 ($this->userRoleDescription === 'Turnierleiter') ||
                 ($this->userRoleDescription === 'Administrator'));
    }

    public function isTournamentLeader (): bool
    {
        return (($this->userRoleDescription === 'Turnierleiter') ||
                 ($this->userRoleDescription === 'Administrator'));
    }

    public function isAdmin (): bool
    {
        return (($this->userRoleDescription === 'Administrator'));
    }

    public function isRoleValid (): bool
    {
        return (($this->userRoleDescription === 'Anonymous') ||
                 ($this->userRoleDescription === 'Gast') ||
                 ($this->userRoleDescription === 'Spieler') ||
                 ($this->userRoleDescription === 'Turnierleiter') ||
                 ($this->userRoleDescription === 'Administrator'));
    }
}

?>