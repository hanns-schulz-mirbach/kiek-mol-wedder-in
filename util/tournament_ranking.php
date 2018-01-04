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
require_once ("./model/ranking.php");
require_once ("./model/game.php");
require_once ("./model/player.php");
require_once ("./util/result.php");

class TournamentRanking
{

    private $tournament;

    private $arrayOfRankings;

    function __construct ()
    {
        $this->tournament = new Tournament();
        $this->arrayOfRankings = [];
    }

    public function getTournament (): Tournament
    {
        return $this->tournament;
    }

    public function getArrayOfRankings (): array
    {
        return $this->arrayOfRankings;
    }

    public function setTournament (Tournament $aTournament): void
    {
        $this->tournament = $aTournament;
    }

    public function __toString (): string
    {
        $tournamentRankingAsHTMLTable = '<table><tr><td></td></tr></table>';
        
        return $tournamentRankingAsHTMLTable;
    }

    public function isTournamnentRankingValid (): bool
    {
        return ($this->tournament->isTournamentValid());
    }

    public function addGameToTournamentRanking (Game $game): int
    {
        $affectedRankings = 0;
        $playerWhiteId = $game->getPlayerWhite()->getId();
        $playerBlackId = $game->getPlayerBlack()->getId();
        
        if (isset($this->arrayOfRankings[$playerWhiteId])) {
            $rankingWhite = $this->arrayOfRankings[$playerWhiteId];
        } else {
            $rankingWhite = new Ranking();
            $rankingWhite->setTournament($this->tournament);
            $rankingWhite->setPlayer($game->getPlayerWhite());
            $this->arrayOfRankings[$playerWhiteId] = $rankingWhite;
        }
        
        if (isset($this->arrayOfRankings[$playerBlackId])) {
            $rankingBlack = $this->arrayOfRankings[$playerBlackId];
        } else {
            $rankingBlack = new Ranking();
            $rankingBlack->setTournament($this->tournament);
            $rankingBlack->setPlayer($game->getPlayerBlack());
            $this->arrayOfRankings[$playerBlackId] = $rankingBlack;
        }
        
        $rankingWhite->addGameWhite();
        $rankingBlack->addGameBlack();
        $rankingWhite->addDwzOpponent(
                $game->getPlayerBlack()
                    ->getDwz());
        $rankingBlack->addDwzOpponent(
                $game->getPlayerWhite()
                    ->getDwz());
        
        if ($game->getResult()->isBlackWin()) {
            $rankingWhite->addLoss();
            $rankingBlack->addWin();
            $affectedRankings += 2;
        }
        
        if ($game->getResult()->isBlackWinByDefault()) {
            $rankingWhite->addDefaultLoss();
            $rankingBlack->addDefaultWin();
            $affectedRankings += 2;
        }
        
        if ($game->getResult()->isDraw()) {
            $rankingWhite->addDraw();
            $rankingBlack->addDraw();
            $affectedRankings += 2;
        }
        
        if ($game->getResult()->isWhiteWin()) {
            $rankingWhite->addWin();
            $rankingBlack->addLoss();
            $affectedRankings += 2;
        }
        
        if ($game->getResult()->isWhiteWinByDefault()) {
            $rankingWhite->addDefaultWin();
            $rankingBlack->addDefaultLoss();
            $affectedRankings += 2;
        }
        
        return $affectedRankings;
    }

    public function getRankingForPlayer (Player $player): Ranking
    {
        if (isset($this->arrayOfRankings[$player->getId()])) {
            return $this->arrayOfRankings[$player->getId()];
        } else {
            $ranking = new Ranking();
            $ranking->setPlayer($player);
            return $ranking;
        }
    }

    public function addSonnebornBergerScoreForGame (Game $game): void
    {
        if ($this->playerIsInRanking($game->getPlayerWhite())) {
            $rankingWhite = $this->arrayOfRankings[$game->getPlayerWhite()->getId()];
        } else {
            return;
        }
        if ($this->playerIsInRanking($game->getPlayerBlack())) {
            $rankingBlack = $this->arrayOfRankings[$game->getPlayerBlack()->getId()];
        } else {
            return;
        }
        
        if ($game->getResult()->isBlackWin() ||
                 $game->getResult()->isBlackWinByDefault()) {
            $rankingBlack->addSonnebornBergerScore($rankingWhite->getScore());
        }
        
        if ($game->getResult()->isDraw()) {
            $rankingWhite->addSonnebornBergerScore(
                    intval($rankingBlack->getScore() / 2));
            $rankingBlack->addSonnebornBergerScore(
                    intval($rankingWhite->getScore() / 2));
        }
        
        if ($game->getResult()->isWhiteWin() ||
                 $game->getResult()->isWhiteWinByDefault()) {
            $rankingWhite->addSonnebornBergerScore($rankingBlack->getScore());
        }
    }

    public function createRanks (): void
    {
        usort($this->arrayOfRankings,
                array(
                        $this,
                        "compareRankings"
                ));
        
        $rank = 1;
        foreach ($this->arrayOfRankings as $ranking) {
            if ($ranking instanceof Ranking) {
                $ranking->setRank($rank);
                $rank ++;
            }
        }
    }

    public static function compareRankings (Ranking $rankingLeft,
            Ranking $rankingRight): int
    {
        if ((! isset($rankingLeft)) || (! isset($rankingRight))) {
            return 0;
        }
        
        if ($rankingLeft->getScore() < $rankingRight->getScore()) {
            return - 1;
        }
        
        if ($rankingLeft->getScore() > $rankingRight->getScore()) {
            return 1;
        }
        
        if ($rankingLeft->getSonnebornBergerScore() <
                 $rankingRight->getSonnebornBergerScore()) {
            return - 1;
        }
        
        if ($rankingLeft->getSonnebornBergerScore() >
                 $rankingRight->getSonnebornBergerScore()) {
            return 1;
        }
        
        return 0;
    }

    private function playerIsInRanking (Player $player): bool
    {
        return isset($this->arrayOfRankings[$player->getId()]);
    }
}
?>
