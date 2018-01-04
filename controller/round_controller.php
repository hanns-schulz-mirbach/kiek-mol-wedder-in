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
require_once ("./model/round.php");
require_once ("./util/formatter.php");
require_once ("./db/database.php");

class RoundController
{

    private $formatter;
    
    private $round;

    private $database;

    public function getRound ()
    {
        return $this->round;
    }

    public function setRound ($aRound)
    {
        $this->round = $aRound;
    }

    public function __construct ()
    {
        $this->formatter = new Formatter();
        $this->round = new Round();
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    // inserts a new Round in the database. Returns number of new records
    // created. That should always be one. Will fail in case that a Round
    // with Round.getId() is already in the database
    // (i.e. no automatic update will be done)
    public function insertRound (): int
    {
        if ((! is_null($this->round)) && ($this->round->isRoundValid())) {
            return $this->database->insertRound($this->round);
        } else {
            return 0;
        }
    }

    public function updateRound (): int
    {
        if (! is_null($this->round) && $this->round->isRoundValid()) {
            return $this->database->updateRound($this->round);
        } else {
            return 0;
        }
    }

    public function getAllRounds (): array
    {
        return $this->database->getAllRounds();
    }

    public function getRoundsForCurrentTournament (): array
    {
        return $this->database->getAllActiveRounds();
    }

    public function getAllRoundsForTournament (Tournament $tournament): array
    {
        return $this->database->getAllRoundsForTournament($tournament->getId());
    }

    public function getRoundById (int $id): Round
    {
        return $this->database->getRoundById($id);
    }
    
    public function getRoundSelection (string $selectName, int $roundId = -1): string
    {
        $activeRounds = $this->database->getAllActiveRounds();
        $roundSelection = $this->formatter->getRoundsForSelection($activeRounds,
                $selectName, $roundId);
        return $roundSelection;
    }
    
    public function getAuthorSelection (string $selectName, $playerId = - 1): string
    {
        $players = $this->database->getAllActivePlayers();
        $playerSelection = $this->formatter->getPlayersForSelection($players,
                $selectName, $playerId);
        return $playerSelection;
    }
}

?>