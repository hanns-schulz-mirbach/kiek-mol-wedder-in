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
require_once ("./util/participation.php");
require_once ("./util/color.php");

class Schedule
{

    private $id;

    private $round;

    private $player;

    private $desired_opponent;

    private $desired_color;

    private $participation;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->round = new Round();
        $this->player = new Player('', '', '', '', - 1, - 1, '', '', '', '');
        $this->desired_opponent = new Player('', '', '', '', - 1, - 1, '', '', '',
                '');
        $this->desired_color = new Color();
        $this->participation = new Participation();
    }

    public function __toString (): string
    {
        $scheduleDataAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' . "$this->id" . '</td></tr>
<tr><td>Runde:</td><td>' .
                 "<a href='round_update.php?id=" . $this->round->getId() . "'>" .
                 $this->round->getId() . '</a></td></tr>
<tr><td>Spieler:</td><td>' .
                 "<a href='player_update.php?id=" . $this->player->getId() . "'>" .
                 $this->player->getId() . '</a></td></tr>
<tr><td>Teilnahme:</td><td>' .
                 "$this->participation" . '</td></tr>
<tr><td>Wunschgegner:</td><td>' .
                 "<a href='player_update.php?id=" .
                 $this->desired_opponent->getId() . "'>" .
                 $this->desired_opponent->getId() . '</a></td></tr>
<tr><td>Wunschfarbe:</td><td>' .
                 "$this->desired_color" . '</td></tr>
</table>';
        
        return $scheduleDataAsHTMLTable;
    }

    public function getUpdateSQL (string $tablename): string
    {
        $updateSQL = 'UPDATE ' . $tablename . ' SET ' . "round = " .
                 $this->round->getId() . ", " . "player =  " .
                 $this->player->getId() . ", " . "participation =  " .
                 $this->participation->getParticipationStatus() . ", " .
                 "desired_opponent =  " . $this->desired_opponent->getId() . ", " .
                 "desired_color =  " . $this->desired_color->getGameColor() .
                 " WHERE id = " . $this->id;
        
        return $updateSQL;
    }

    public function isScheduleValid (): bool
    {
        return ($this->idIsValid() && $this->playerIsValid() &&
                 $this->roundIsValid() && $this->desiredColorIsValid() &&
                 $this->desiredOpponentIsValid());
    }

    public function getScheduleAsTableRow (): string
    {
        return "<tr>" . "<td>" . "$this->id" . "</td><td>" .
                 $this->round->getId() . "</td><td>" . $this->player->getId() .
                 "</td>" . "<td>" .
                 $this->participation->getParticipationStatus() . "</td><td>" .
                 $this->desired_opponent->getId() . "</td><td>" .
                 $this->desired_color . "</td></tr>";
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function playerIsValid (): bool
    {
        if (isset($this->player)) {
            return ($this->player->isPlayerValid());
        } else {
            return false;
        }
    }

    private function desiredOpponentIsValid (): bool
    {
        if (isset($this->desired_opponent)) {
            return ($this->desired_opponent->isPlayerValid());
        } else {
            return false;
        }
    }

    private function desiredColorIsValid (): bool
    {
        if (isset($this->desired_color)) {
            return ($this->desired_color->isColorValid());
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

    public function getPlayer (): Player
    {
        return $this->player;
    }

    public function getDesiredOpponent (): Player
    {
        return $this->desired_opponent;
    }

    public function getDesiredColor (): Color
    {
        return $this->desired_color;
    }

    public function getParticipation (): Participation
    {
        return $this->participation;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setRound (Round $round): void
    {
        $this->round = $round;
    }

    public function setPlayer (Player $player): void
    {
        $this->player = $player;
    }

    public function setDesiredOpponent (Player $desiredOpponent): void
    {
        $this->desired_opponent = $desiredOpponent;
    }

    public function setColor (Color $color): void
    {
        $this->desired_color = $color;
    }

    public function setParticipation (Participation $participation): void
    {
        $this->participation = $participation;
    }
}
?>
