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

require_once ("./model/schedule.php");
require_once ("./model/player.php");
require_once ("./model/round.php");
require_once ("./util/participation.php");
require_once ("./util/formatter.php");
require_once ("./db/database.php");

class ScheduleController
{

    private $schedule;

    private $formatter;

    private $database;

    public function __construct ()
    {
        $this->schedule = new Schedule();
        $this->formatter = new Formatter();
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function instantiateFromDatabase (int $roundId, int $playerId,
            int $participationStatus): void
    {
        $this->schedule->setRound($this->database->getRoundById($roundId));
        $this->schedule->setPlayer($this->database->getPlayerById($playerId));
        $this->schedule->getParticipation()->setParticipationStatus(
                $participationStatus);
    }

    public function instantiateSkeleton (int $roundId, int $playerId,
            int $participationStatus, int $desiredOpponent = -1,
            int $desiredColor = 0): void
    {
        $this->schedule->getRound()->setId($roundId);
        $this->schedule->getPlayer()->setId($playerId);
        $this->schedule->getParticipation()->setParticipationStatus(
                $participationStatus);
        $this->schedule->getDesiredOpponent()->setId($desiredOpponent);
        $this->schedule->getDesiredColor()->setGameColor($desiredColor);
    }

    // inserts a new Schedule in the database. Returns number of new records
    // created. That should always be one. Will fail in case that a Schedule
    // with Schedule.getId() is already in the database
    // (i.e. no automatic update will be done)
    public function insertSchedule (): int
    {
        if ((! is_null($this->schedule)) && ($this->isScheduleValid())) {
            return $this->database->insertSchedule($this->schedule);
        } else {
            return 0;
        }
    }

    public function getRoundSelection (string $selectName, int $roundId = -1): string
    {
        $activeRounds = $this->database->getAllActiveRounds();
        $roundSelection = $this->formatter->getRoundsForSelection($activeRounds,
                $selectName, $roundId);
        return $roundSelection;
    }

    public function getPlayerSelection (string $selectName, $playerId = - 1): string
    {
        $players = $this->database->getAllActivePlayers();
        $playerSelection = $this->formatter->getPlayersForSelection($players,
                $selectName, $playerId);
        return $playerSelection;
    }

    public function getParticipationSelection (string $selectName,
            int $participationId = -1): string
    {
        return $this->formatter->getParticipationSelection($selectName,
                $participationId);
    }

    public function getSchedule (): Schedule
    {
        return $this->schedule;
    }

    public function setSchedule (Schedule $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function updateSchedule (): int
    {
        if (! is_null($this->schedule) && $this->isScheduleValid()) {
            return $this->database->updateSchedule($this->schedule);
        } else {
            return 0;
        }
    }

    public function getAllSchedules (): array
    {
        return $this->database->getAllSchedules();
    }
    
    public function getSchedulesForCurrentTournament (): array
    {
        return $this->database->getAllActiveSchedules();
    }
    

    public function getAllSchedulesForRound (int $roundId): array
    {
        return $this->database->getAllSchedulesForRound($roundId);
    }

    public function getScheduleById (int $id): Schedule
    {
        return $this->database->getScheduleById($id);
    }

    public function getSchedulesForPlayer (Player $player): array
    {
        return $this->database->getSchedulesForPlayer($player->getId());
    }

    public function deleteScheduleFromDatabase (): int
    {
        return $this->database->deleteSchedule($this->schedule);
    }

    private function referencedObjectsExistInDatabase (): bool
    {
        return $this->database->playerAndRoundExistInDatabase(
                $this->schedule->getPlayer()
                    ->getId(),
                $this->schedule->getRound()
                    ->getId());
    }

    private function isScheduleValid (): bool
    {
        // The schedule is considered valid when the references in the database
        // exist
        // The member variable $this->schedule might returen false on
        // $this->schedule->isScheduleValid()
        // That will happen when just the object id's have been inserted without
        // the other attributes
        // That is a valid staus for update purposes
        return $this->referencedObjectsExistInDatabase();
    }
}

?>