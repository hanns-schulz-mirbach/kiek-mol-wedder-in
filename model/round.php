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

class Round
{

    private $id;

    private $roundDescription;

    private $roundDate;

    private $roundDateString;

    private $tournament;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        $this->tournament = new Tournament();
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getRoundDescription (): string
    {
        return $this->roundDescription;
    }

    public function getRoundDate (): string
    {
        return $this->roundDateString;
    }

    public function getRoundDateForDB (): string
    {
        return $this->roundDate->format('Y-m-d');
    }

    public function getTournament (): Tournament
    {
        return $this->tournament;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setRoundDescription (string $roundDescription): void
    {
        $this->roundDescription = $roundDescription;
    }

    public function setRoundDate (string $roundDate, string $dateformat = "d.m.Y"): void
    {
        $this->roundDate = DateTime::createFromFormat($dateformat, $roundDate);
        $this->roundDateString = $this->roundDate->format("d.m.Y");
    }

    public function setTournament (Tournament $aTournament): void
    {
        $this->tournament = $aTournament;
    }

    public function __toString (): string
    {
        $roundDataAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Rundennummer:</td><td>' .
                 "$this->id" . '</td></tr><tr><td>Beschreibung:</td><td>' .
                 "$this->roundDescription" .
                 '</td></tr><tr><td>Rundendatum:</td><td>' .
                 "$this->roundDateString" . '</td></tr><tr><td>Turnier:</td><td>' .
                 "<a href='tournament_update.php?id=" .
                 $this->tournament->getId() . "'>" . $this->tournament->getId() .
                 '</a></td></tr></table>';
        
        return $roundDataAsHTMLTable;
    }

    public function getUpdateSQL (string $tablename): string
    {
        $updateSQL = 'UPDATE ' . $tablename . ' SET ' . "description = ' " .
                 "$this->roundDescription" . "', " . "date = ' " .
                 $this->getRoundDateForDB() . "', " . "tournament = " .
                 $this->tournament->getId() . " WHERE id = " . $this->id;
        
        return $updateSQL;
    }

    public function isRoundValid (): bool
    {
        return ($this->idIsValid() && $this->roundDateIsValid() &&
                 $this->roundDescriptionIsValid() &&
                 $this->tournament->isTournamentValid());
    }

    public function getRoundAsTableRow (): string
    {
        return "<tr>" . "<td>" . "$this->id" . "</td>" . "<td>" .
                 "$this->roundDescription" . "</td>" . "<td>" .
                 "$this->roundDateString" . "</td>" . "<td>" .
                 $this->tournament->getTournamentTitle() . "</td>" . "</tr>";
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function roundDescriptionIsValid (): bool
    {
        return (isset($this->roundDescription) &&
                 is_string($this->roundDescription));
    }

    private function roundDateIsValid (): bool
    {
        if (isset($this->roundDate)) {
            return (($this->roundDate instanceof DateTime));
        } else {
            return true;
        }
    }
}
?>
