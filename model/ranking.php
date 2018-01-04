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
require_once ("./model/player.php");
require_once ("./model/game.php");
require_once ("./util/result.php");

class Ranking
{

    private $id;

    private $tournament;

    private $player;

    private $rank;

    private $numOfGamesWhite;

    private $numOfGamesBlack;

    private $numOfDraws;

    private $numOfLosses;

    private $numOfWins;

    private $numOfDefaultLosses;

    private $numOfDefaultWins;

    private $accumulatedDwzOpponents;

    private $sonnebornBergerScore;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        
        $this->player = new Player('', '', '', '', - 1, - 1, '', '', '', '');
        $this->tournament = new Tournament();
        $this->rank = 0;
        $this->numOfGamesWhite = 0;
        $this->numOfGamesBlack = 0;
        $this->numOfDraws = 0;
        $this->numOfLosses = 0;
        $this->numOfWins = 0;
        $this->numOfDefaultLosses = 0;
        $this->numOfDefaultWins = 0;
        $this->accumulatedDwzOpponents = 0;
        $this->sonnebornBergerScore = 0;
    }

    public function __toString (): string
    {
        $rankingDataAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' . "$this->id" .
                 '</td></tr><tr><td>Spieler:</td><td>' . $this->player->getId() .
                 '</td></tr><tr><td>Rang:</td><td>' . "$this->rank" .
                 '</td></tr><tr><td>Turnier:</td><td>' .
                 $this->tournament->getId() . '</td></tr><tr><td>Platz:</td><td>' .
                 "$this->rank" . '</td></tr><tr><td>#Weißpartien:</td><td>' .
                 "$this->numOfGamesWhite" .
                 '</td></tr><tr><td>#Schwarzpartien:</td><td>' .
                 "$this->numOfGamesBlack" . '</td></tr><tr><td>#Remis:</td><td>' .
                 "$this->numOfDraws" . '</td></tr><tr><td>#Verluste:</td><td>' .
                 "$this->numOfLosses" . '</td></tr><tr><td>#Gewinne:</td><td>' .
                 "$this->numOfWins" .
                 '</td></tr><tr><td>#Verluste kampflos:</td><td>' .
                 "$this->numOfDefaultLosses" .
                 '</td></tr><tr><td>#Gewinne kampflos:</td><td>' .
                 "$this->numOfDefaultWins" .
                 '</td></tr><tr><td>Summe DWZ Gegner:</td><td>' .
                 $this->accumulatedDwzOpponents .
                 '</td></tr><tr><td>Feinwertung:</td><td>' .
                 "$this->sonnebornBergerScore" . '</td></tr></table>';
        
        return $rankingDataAsHTMLTable;
    }

    public function isRankingValid (): bool
    {
        return ($this->player->isPlayerValid() &&
                 $this->tournament->isTournamentValid() &&
                 $this->resultsAndNumberOfGamesAreConsistent() &&
                 $this->allNumbersAreNonNegative());
    }

    public function isHigherRankedThan (Ranking $anotherRank): bool
    {
        return $this->rank < $anotherRank->getRank();
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getTournament (): Tournament
    {
        return $this->tournament;
    }

    public function getPlayer (): Player
    {
        return $this->player;
    }

    public function getRank (): int
    {
        return $this->rank;
    }

    public function getNumOfGamesWhite (): int
    {
        return $this->numOfGamesWhite;
    }

    public function getNumOfGamesBlack (): int
    {
        return $this->numOfGamesBlack;
    }

    public function getNumOfDraws (): int
    {
        return $this->numOfDraws;
    }

    public function getNumOfLosses (): int
    {
        return $this->numOfLosses;
    }

    public function getNumOfWins (): int
    {
        return $this->numOfWins;
    }

    public function getNumOfDefaultLosses (): int
    {
        return $this->numOfDefaultLosses;
    }

    public function getNumOfDefaultWins (): int
    {
        return $this->numOfDefaultWins;
    }

    public function getAccumulatedDwzOpponents (): int
    {
        return $this->accumulatedDwzOpponents;
    }

    public function getSonnebornBergerScore (): int
    {
        return $this->sonnebornBergerScore;
    }

    public function getScore (): int
    {
        // default loss = 0 points, loss = 1 point, draw = 2 points, default win
        // = 3 points, win = 3 points
        return ($this->numOfLosses + 2 * $this->numOfDraws +
                 3 * $this->numOfDefaultWins + 3 * $this->numOfWins);
    }

    public function getAverageDwzOfOpponents (): int
    {
        $numOfGames = $this->numOfGamesWhite + $this->numOfGamesBlack;
        return intval(
                ceil($this->accumulatedDwzOpponents / max(1, $numOfGames)));
    }

    public function getNumberOfGames (): int
    {
        return $this->numOfGamesWhite + $this->numOfGamesBlack;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setTournament (Tournament $tournament): void
    {
        $this->tournament = $tournament;
    }

    public function setPlayer (Player $player): void
    {
        $this->player = $player;
    }

    public function setRank (int $rank): void
    {
        $this->rank = $rank;
    }

    public function setNumOfGamesWhite (int $numOfGamesWhite): void
    {
        $this->numOfGamesWhite = $numOfGamesWhite;
    }

    public function setNumOfGamesBlack (int $numOfGamesBlack): void
    {
        $this->numOfGamesBlack = $numOfGamesBlack;
    }

    public function setNumOfDraws (int $numOfDraws): void
    {
        $this->numOfDraws = $numOfDraws;
    }

    public function setNumOfLosses (int $numOfLosses): void
    {
        $this->numOfLosses = $numOfLosses;
    }

    public function setNumOfWins (int $numOfWins): void
    {
        $this->numOfWins = $numOfWins;
    }

    public function setNumOfDefaultLosses (int $numOfDefaultLosses): void
    {
        $this->numOfDefaultLosses = $numOfDefaultLosses;
    }

    public function setNumOfDefaultWins (int $numOfDefaultWins): void
    {
        $this->numOfDefaultWins = $numOfDefaultWins;
    }

    public function setAccumulatedDwzOpponents (int $accumulatedDwzOpponents): void
    {
        $this->accumulatedDwzOpponents = $accumulatedDwzOpponents;
    }

    public function setSonnebornBergerScore (int $sonnebornBergerScore): void
    {
        $this->sonnebornBergerScore = $sonnebornBergerScore;
    }

    public function addGameBlack (): void
    {
        $this->numOfGamesBlack += 1;
    }

    public function addGameWhite (): void
    {
        $this->numOfGamesWhite += 1;
    }

    public function addDraw (): void
    {
        $this->numOfDraws += 1;
    }

    public function addLoss (): void
    {
        $this->numOfLosses += 1;
    }

    public function addWin (): void
    {
        $this->numOfWins += 1;
    }

    public function addDefaultWin (): void
    {
        $this->numOfDefaultWins += 1;
    }

    public function addDefaultLoss (): void
    {
        $this->numOfDefaultLosses += 1;
    }

    public function addDwzOpponent (int $dwzOpponent): void
    {
        $this->accumulatedDwzOpponents += $dwzOpponent;
    }

    public function addSonnebornBergerScore (int $sonnebornBergerScore): void
    {
        $this->sonnebornBergerScore += $sonnebornBergerScore;
    }

    private function resultsAndNumberOfGamesAreConsistent (): bool
    {
        return (($this->numOfGamesBlack + $this->numOfGamesWhite) == ($this->numOfDefaultLosses +
                 $this->numOfDefaultWins + $this->numOfDraws + $this->numOfLosses +
                 $this->numOfWins));
    }

    private function allNumbersAreNonNegative (): bool
    {
        return (($this->accumulatedDwzOpponents >= 0) && ($this->rank >= 0) &&
                 ($this->numOfDefaultLosses >= 0) &&
                 ($this->numOfDefaultWins >= 0) && ($this->numOfDraws >= 0) &&
                 ($this->numOfGamesBlack >= 0) && ($this->numOfGamesWhite >= 0) &&
                 ($this->numOfLosses >= 0) && ($this->numOfWins >= 0) &&
                 ($this->sonnebornBergerScore >= 0));
    }
}
?>
