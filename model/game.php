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

require_once ("./model/round.php");
require_once ("./model/player.php");
require_once ("./util/result.php");

class Game
{

    private $id;

    private $round;

    private $playerWhite;

    private $playerBlack;

    private $dateOfGame;

    private $result;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->round = new Round();
        $this->playerWhite = new Player('', '', '', '', - 1, - 1, '', '', '', '');
        $this->playerBlack = new Player('', '', '', '', - 1, - 1, '', '', '', '');
        $this->dateOfGame = new DateTime();
        $this->result = new Result();
    }

    public function __toString (): string
    {
        $gameDataAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' . "$this->id" . '</td></tr>
<tr><td>Runde:</td><td>' . "<a href='round_update.php?id=" .
                 $this->round->getId() . "'>" . $this->round->getId() . '</a></td></tr>
<tr><td>Spieler Weiß:</td><td>' . "<a href='player_update.php?id=" .
                 $this->playerWhite->getId() . "'>" . $this->playerWhite->getId() . '</a></td></tr>
<tr><td>Spieler Schwarz:</td><td>' . "<a href='player_update.php?id=" .
                 $this->playerBlack->getId() . "'>" . $this->playerBlack->getId() . '</a></td></tr>
<tr><td>Partiedatum:</td><td>' . $this->dateOfGame->format("d.m.Y") . '</td></tr>
<tr><td>Partieresultat:</td><td>' . "$this->result" . '</td></tr>

</table>';
        
        return $gameDataAsHTMLTable;
    }

    public function getUpdateSQL (string $tablename): string
    {
        $updateSQL = 'UPDATE ' . $tablename . ' SET ' . "round = " .
                 $this->round->getId() . ", " . "player_white =  " .
                 $this->playerWhite->getId() . ", " . "player_black =  " .
                 $this->playerBlack->getId() . ", " . "date_of_game =  '" .
                 $this->dateOfGame->format("Y-m-d") . "', " . "result =  " .
                 $this->result->getGameResult() . " WHERE id = " . $this->id;
        
        return $updateSQL;
    }

    public function isGameValid (): bool
    {
        return ($this->idIsValid() && $this->playersAreValid() &&
                 $this->roundIsValid());
    }

    public function getGameAsTableRow (): string
    {
        $tableRow = "<tr><td>" . $this->getId() . "</td><td>" .
                 $this->getRound()->getRoundDescription() . ", " .
                 $this->getDateOfGameForDisplay() . "</td><td>" .
                 $this->getPlayerWhite()->getLastname() . ", " .
                 $this->getPlayerWhite()->getLastname() . "</td><td>" .
                 $this->getPlayerBlack()->getLastname() . ", " .
                 $this->getPlayerBlack()->getFirstname() . ", " .
                 $this->getResult() . "</td></tr>";
        
        return $tableRow;
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function playersAreValid (): bool
    {
        if (isset($this->playerWhite) && isset($this->playerBlack)) {
            return ($this->playerWhite->isPlayerValid() &&
                     $this->playerBlack->isPlayerValid());
        } else {
            return false;
        }
    }

    private function roundIsValid (): bool
    {
        if (isset($this->round)) {
            return (($this->round->isRoundValid));
        } else {
            return false;
        }
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getRound (): Round
    {
        return $this->round;
    }

    public function getPlayerWhite (): Player
    {
        return $this->playerWhite;
    }

    public function getPlayerBlack (): Player
    {
        return $this->playerBlack;
    }

    public function getDateOfGame (): DateTime
    {
        return $this->dateOfGame;
    }

    public function getDateOfGameForDB (): string
    {
        return $this->dateOfGame->format("Y-m-d");
    }

    public function getDateOfGameForDisplay (): string
    {
        return $this->dateOfGame->format("d.m.Y");
    }

    public function getResult (): Result
    {
        return $this->result;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setRound (Round $round): void
    {
        $this->round = $round;
    }

    public function setPlayerWhite (Player $player): void
    {
        $this->playerWhite = $player;
    }

    public function setPlayerBlack (Player $player): void
    {
        $this->playerBlack = $player;
    }

    public function setGameDateString (string $gameDate,
            string $dateformat = "d.m.Y"): void
    {
        $this->dateOfGame = DateTime::createFromFormat($dateformat, $gameDate);
    }

    public function setGameDate (DateTime $gameDate): void
    {
        $this->dateOfGame = $gameDate;
    }

    public function setGameResult (Result $result): void
    {
        $this->result = $result;
    }
}
?>
