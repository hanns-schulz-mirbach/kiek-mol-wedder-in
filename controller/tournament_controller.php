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

require_once ("./model/tournament.php");
require_once ("./db/database.php");

class TournamentController
{

    private $tournament;

    private $database;

    public function getTournament ()
    {
        return $this->tournament;
    }

    public function setTournament ($aTournament)
    {
        $this->tournament = $aTournament;
    }

    public function __construct ()
    {
        $this->tournament = new Tournament();
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    // inserts a new Tournament in the database. Returns number of new records
    // created. That should always be one. Will fail in case that a Tournament
    // with Tournament.getId() is already in the database
    // (i.e. no automatic update will be done)
    public function insertTournament (): int
    {
        if ((! is_null($this->tournament)) &&
                 ($this->tournament->isTournamentValid())) {
            return $this->database->insertTournament($this->tournament);
        } else {
            return 0;
        }
    }

    public function updateTournament (): int
    {
        if (! is_null($this->tournament) &&
                 $this->tournament->isTournamentValid()) {
            return $this->database->updateTournament($this->tournament);
        } else {
            return 0;
        }
    }

    public function getAllTournaments (): array
    {
        return $this->database->getAllTournaments();
    }

    public function getAllFinalizedTournaments (): array
    {
        return $this->database->getAllFinalizedTournaments();
    }

    public function getAllActiveTournaments (): array
    {
        return $this->database->getAllActiveTournaments();
    }

    public function getMostRecentActiveTournament (): Tournament
    {
        $activeTournaments = $this->database->getAllActiveTournaments();
        if (sizeof($activeTournaments) == 0) {
            $mostRecentTournament = new Tournament();
        }else {
            $mostRecentTournament = $activeTournaments[0];
        }
        
        return $mostRecentTournament;
    }

    public function getTournamentById (int $id): Tournament
    {
        return $this->database->getTournamentById($id);
    }
}

?>