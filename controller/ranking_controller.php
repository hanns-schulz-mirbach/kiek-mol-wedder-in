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

require_once ("./model/game.php");
require_once ("./model/player.php");
require_once ("./model/tournament.php");
require_once ("./controller/tournament_controller.php");
require_once ("./util/result.php");
require_once ("./util/tournament_ranking.php");
require_once ("./util/formatter.php");

class RankingController
{

    private $ranking;

    private $tournament;

    private $tournamentRanking;

    private $formatter;

    private $database;

    public function __construct ()
    {
        $this->ranking = new Ranking();
        $this->tournamentRanking = new TournamentRanking();
        $this->formatter = new Formatter();
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function instantiateFromDatabase (int $tournamentId, int $playerId,
            int $rank = 0, int $numOfGamesWhite = 0, int $numOfGamesBlack = 0,
            int $numOfDraws = 0, int $numOfLosses = 0, int $numOfWins = 0,
            int $numOfDefaultLosses = 0, int $numOfDefaultWins = 0,
            int $accumulatedDwzOpponents = 0, int $sonnebornBergerScore = 0): void
    {
        $this->ranking->setTournament(
                $this->database->getTournamentById($tournamentId));
        $this->ranking->setPlayer($this->database->getPlayerById($playerId));
        $this->ranking->setRank($rank);
        $this->ranking->setNumOfGamesWhite($numOfGamesWhite);
        $this->ranking->setNumOfGamesBlack($numOfGamesBlack);
        $this->ranking->setNumOfDraws($numOfDraws);
        $this->ranking->setNumOfLosses($numOfLosses);
        $this->ranking->setNumOfWins($numOfWins);
        $this->ranking->setNumOfDefaultLosses($numOfDefaultLosses);
        $this->ranking->setNumOfDefaultWins($numOfDefaultWins);
        $this->ranking->setAccumulatedDwzOpponents($accumulatedDwzOpponents);
        $this->ranking->setSonnebornBergerScore($sonnebornBergerScore);
    }

    public function instantiateSkeleton (int $tournamentId, int $playerId,
            int $rank = 0, int $numOfGamesWhite = 0, int $numOfGamesBlack = 0,
            int $numOfDraws = 0, int $numOfLosses = 0, int $numOfWins = 0,
            int $numOfDefaultLosses = 0, int $numOfDefaultWins = 0,
            int $accumulatedDwzOpponents = 0, int $sonnebornBergerScore = 0): void
    {
        $this->ranking->getTournament()->setId($tournamentId);
        $this->ranking->getPlayer()->setId($playerId);
        $this->ranking->setRank($rank);
        $this->ranking->setNumOfGamesWhite($numOfGamesWhite);
        $this->ranking->setNumOfGamesBlack($numOfGamesBlack);
        $this->ranking->setNumOfDraws($numOfDraws);
        $this->ranking->setNumOfLosses($numOfLosses);
        $this->ranking->setNumOfWins($numOfWins);
        $this->ranking->setNumOfDefaultLosses($numOfDefaultLosses);
        $this->ranking->setNumOfDefaultWins($numOfDefaultWins);
        $this->ranking->setAccumulatedDwzOpponents($accumulatedDwzOpponents);
        $this->ranking->setSonnebornBergerScore($sonnebornBergerScore);
    }

    // inserts a new Ranking in the database. Returns number of new records
    // created. That should always be one. Will fail in case that a Ranking
    // with Ranking.getId() is already in the database
    // (i.e. no automatic update will be done)
    public function insertRanking (): int
    {
        if ((! is_null($this->ranking)) && ($this->isRankingValid())) {
            return $this->database->insertRanking($this->ranking);
        } else {
            return 0;
        }
    }

    public function getRanking (): Ranking
    {
        return $this->ranking;
    }

    public function getRankingForCurrentTournament (): array
    {
        return $this->database->getRankingForCurrentTournament();
    }

    public function getRankingForTournament (Tournament $tournament): array
    {
        return $this->database->getRankingForTournament($tournament->getId());
    }

    public function setRanking (Ranking $ranking): void
    {
        $this->ranking = $ranking;
    }

    public function getTournament (): Tournament
    {
        return $this->tournament;
    }

    public function getTournamentRanking (): TournamentRanking
    {
        return $this->tournamentRanking;
    }

    public function setTournament (Tournament $tournament): void
    {
        $this->tournament = $tournament;
    }

    public function setTournamentRanking (TournamentRanking $tournamentRanking): void
    {
        $this->tournamentRanking = $tournamentRanking;
    }

    public function updateRanking (): int
    {
        if (! is_null($this->ranking) && $this->isRankingValid()) {
            return $this->database->updateRanking($this->ranking);
        } else {
            return 0;
        }
    }

    public function getAllRankings (): array
    {
        return $this->database->getAllRankings();
    }

    public function getRankingById (int $id): Ranking
    {
        return $this->database->getRankingById($id);
    }

    public function deleteAllRankings (): int
    {
        return $this->database->deleteAllRankings();
    }

    public function deleteRankingsForActiveTournaments (): int
    {
        $tournamentController = new TournamentController();
        $activeTournaments = $tournamentController->getAllActiveTournaments();
        
        $numOfDeletedRankings = 0;
        
        foreach ($activeTournaments as $tournament) {
            $numOfDeletedRankings += $this->deleteRankingsForTournament(
                    $tournament);
        }
        
        return $numOfDeletedRankings;
    }

    public function deleteRankingsForTournament (Tournament $tournament): int
    {
        $numOfDeletedRankings = 0;
        
        $numOfDeletedRankings += $this->database->deleteAllRankingsForTournament(
                $tournament->getId());
        
        return $numOfDeletedRankings;
    }

    public function createRankingForTournament (Tournament $tournament): void
    {
        $tournamentGames = $this->database->getFinishedGamesForTournament(
                $tournament->getId());
        
        if (! empty($tournamentGames)) {
            
            $this->tournamentRanking->setTournament($tournament);
            
            // add all games to tournament ranking
            foreach ($tournamentGames as $game) {
                $this->tournamentRanking->addGameToTournamentRanking($game);
            }
            
            // calculate Sonneborn Berger scores. That has as a prerequiste that
            // all
            // games have been added to the tournament
            foreach ($tournamentGames as $game) {
                $this->tournamentRanking->addSonnebornBergerScoreForGame($game);
            }
            
            // create ranks
            $this->tournamentRanking->createRanks();
            
            // persist ranking in database
            $rankingArray = $this->tournamentRanking->getArrayOfRankings();
            foreach ($rankingArray as $ranking) {
                $this->database->insertRanking($ranking);
            }
        }
    }

    public function createRankingsForActiveTournaments (): void
    {
        $tournamentController = new TournamentController();
        $activeTournaments = $tournamentController->getAllActiveTournaments();
        
        foreach ($activeTournaments as $tournament) {
            $this->createRankingForTournament($tournament);
        }
    }

    private function referencedObjectsExistInDatabase (): bool
    {
        return $this->database->playerAndTournamentExistInDatabase(
                $this->ranking->getPlayer()
                    ->getId(),
                $this->ranking->getTournament()
                    ->getId());
    }

    private function isRankingValid (): bool
    {
        // The ranking is considered valid when the references in the database
        // exist
        // The member variable $this->ranking might returen false on
        // $this->ranking->isRankingValid()
        // That will happen when just the object id's have been inserted in the
        // ranking without
        // the other attributes
        // That is valid for update purposes
        return $this->referencedObjectsExistInDatabase();
    }
}

?>