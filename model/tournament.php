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

class Tournament
{

    private $id;

    private $tournamentTitle;

    private $startDate;

    private $startDateString;

    private $endDate;

    private $endDateString;

    private $isFinalized;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        // default tournament title containing the current year
        $this->tournamentTitle = "Kiek mol wedder in ";
        
        $this->isFinalized = 0;
        
        $this->setStartDate("01.01.9999");
        $this->setEndDate("31.12.9999");
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getTournamentTitle (): string
    {
        return $this->tournamentTitle;
    }

    public function getStartDate (): string
    {
        return $this->startDateString;
    }

    public function getStartDateForDB (): string
    {
        return $this->startDate->format('Y-m-d');
    }

    public function getEndDate (): string
    {
        return $this->endDateString;
    }

    public function getEndDateForDB (): string
    {
        return $this->endDate->format('Y-m-d');
    }

    public function getIsFinalized (): int
    {
        return $this->isFinalized;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setTournamentTitle (string $tournamentTitle): void
    {
        $this->tournamentTitle = $tournamentTitle;
    }

    public function setStartDate (string $startDate): void
    {
        $this->startDate = DateTime::createFromFormat("d.m.Y", $startDate);
        $this->startDateString = $startDate;
    }

    public function setEndDate (string $endDate): void
    {
        $this->endDate = DateTime::createFromFormat("d.m.Y", $endDate);
        $this->endDateString = $endDate;
    }

    public function setIsFinalized (int $isFinalized): void
    {
        $this->isFinalized = $isFinalized;
    }

    public function __toString (): string
    {
        $tournamentDataAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Turniernummer:</td><td>' .
                 "$this->id" . '</td></tr><tr><td>Titel:</td><td>' .
                 "$this->tournamentTitle" .
                 '</td></tr><tr><td>Startdatum:</td><td>' .
                 "$this->startDateString" .
                 '</td></tr><tr><td>Enddatum:</td><td>' . "$this->endDateString" .
                 '</td></tr><tr><td>Turnier abgeschlossen:</td><td>' .
                 "$this->isFinalized" . '</td></tr></table>';
        
        return $tournamentDataAsHTMLTable;
    }

    public function getUpdateSQL (string $tablename): string
    {
        $updateSQL = 'UPDATE ' . $tablename . ' SET ' . "tournament_title = ' " .
                 "$this->tournamentTitle" . "', " . "start_date = ' " .
                 $this->getStartDateForDB() . "', " . "end_date = ' " .
                 $this->getEndDateForDB() . "', " . "is_finalized = ' " .
                 "$this->isFinalized" . "' " . " WHERE id = '" . $this->id . "'";
        
        return $updateSQL;
    }

    public function isTournamentValid (): bool
    {
        return ($this->idIsValid() && $this->startDateIsValid() &&
                 $this->endDateIsValid() && $this->isFinalizedIsValid());
    }

    public function getTournamentAsTableRow (): string
    {
        return "<tr>" . "<td>" . "$this->id" . "</td>" . "<td>" .
                 "$this->tournamentTitle" . "</td>" . "<td>" .
                 "$this->startDateString" . "</td>" . "<td>" .
                 "$this->endDateString" . "</td>" . "<td>" . "$this->isFinalized" .
                 "</td></tr>";
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function tournamentTitleIsValid (): bool
    {
        return (isset($this->tournamentTitle) &&
                 is_string($this->tournamentTitle));
    }

    private function startDateIsValid (): bool
    {
        if (isset($this->startDate)) {
            return (($this->startDate instanceof DateTime));
        } else {
            return true;
        }
    }

    private function endDateIsValid (): bool
    {
        if (isset($this->endDate) && isset($this->startDate)) {
            return (($this->endDate instanceof DateTime) &&
                     ($this->startDate instanceof DateTime) &&
                     ($this->endDate > $this->startDate));
        } elseif (isset($this->endDate)) {
            return (($this->endDate instanceof DateTime));
        } else {
            return true;
        }
    }

    private function isFinalizedIsValid (): bool
    {
        if (isset($this->isFinalized)) {
            return true;
        } else {
            return true;
        }
    }
}
?>
