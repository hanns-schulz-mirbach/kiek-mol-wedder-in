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
require_once ("./model/round.php");
require_once ("./util/result.php");
require_once ("./util/formatter.php");

class GameController
{

    private $game;

    private $formatter;

    private $database;

    public function __construct ()
    {
        $this->game = new Game();
        $this->formatter = new Formatter();
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function instantiateFromDatabase (int $roundId, int $playerWhiteId,
            int $playerBlackId, DateTime $gameDate, Result $gameResult): void
    {
        $this->game->setRound($this->database->getRoundById($roundId));
        $this->game->setPlayerWhite(
                $this->database->getPlayerById($playerWhiteId));
        $this->game->setPlayerBlack(
                $this->database->getPlayerById($playerBlackId));
        $this->game->setGameDate($gameDate);
        $this->game->setGameResult($gameResult);
    }

    public function instantiateSkeleton (int $gameId, int $roundId,
            int $playerWhiteId, int $playerBlackId, DateTime $gameDate,
            int $resultValue): void
    {
        $this->game->setId($gameId);
        $this->game->getRound()->setId($roundId);
        $this->game->getPlayerWhite()->setId($playerWhiteId);
        $this->game->getPlayerBlack()->setId($playerBlackId);
        $this->game->setGameDatE($gameDate);
        $this->game->getResult()->setGameResult($resultValue);
    }

    public function mapPlayerAndInstantiateSkeleton (int $gameId, int $roundId,
            int $playerId, int $opponentId, int $colorId, DateTime $gameDate,
            int $resultValue): void
    {
        $this->game->setId($gameId);
        $this->game->getRound()->setId($roundId);
        $this->game->setGameDatE($gameDate);
        $this->game->getResult()->setGameResult($resultValue);
        
        $color = new Color();
        $color->setGameColor($colorId);
        
        if ($color->isBlack()) {
            $playerWhiteId = $opponentId;
            $playerBlackId = $playerId;
        } elseif ($color->isWhite()) {
            $playerWhiteId = $playerId;
            $playerBlackId = $opponentId;
        } else {
            $playerWhiteId = $opponentId;
            $playerBlackId = $playerId;
        }
        
        $this->game->getPlayerWhite()->setId($playerWhiteId);
        $this->game->getPlayerBlack()->setId($playerBlackId);
    }

    // inserts a new Game in the database. Returns number of new records
    // created. That should always be one. Will fail in case that a Game
    // with Game.getId() is already in the database
    // (i.e. no automatic update will be done)
    public function insertGame (): int
    {
        if ((! is_null($this->game)) && ($this->isGameValid())) {
            return $this->database->insertGame($this->game);
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

    public function getPlayerSelection (string $selectName, $playerId): string
    {
        $players = $this->database->getAllActivePlayers();
        $playerSelection = $this->formatter->getPlayersForSelection($players,
                $selectName, $playerId);
        return $playerSelection;
    }

    public function getOpponentSelection (string $selectName, int $playerId,
            int $opponentId = -1): string
    {
        $players = $this->database->getAllActivePlayers();
        $opponentSelection = $this->formatter->getOpponentSelection($players,
                $selectName, $playerId, $opponentId);
        return $opponentSelection;
    }

    public function getResultSelection (string $selectName, int $resultValue): string
    {
        return $this->formatter->getResultSelection($selectName, $resultValue);
    }

    public function getColorSelection (string $selectName, int $colorValue = -1): string
    {
        return $this->formatter->getColorSelection($selectName, $colorValue);
    }

    public function getGame (): Game
    {
        return $this->game;
    }

    public function setGame (Game $game): void
    {
        $this->game = $game;
    }

    public function updateGame (): int
    {
        if (! is_null($this->game) && $this->isGameValid()) {
            return $this->database->updateGame($this->game);
        } else {
            return 0;
        }
    }

    public function getAllGames (): array
    {
        return $this->database->getAllGames();
    }
    
    public function getGamesForCurrentTournament (): array
    {
        return $this->database->getAllActiveGames();
    }
    

    public function getAllGamesForRound (int $roundId): array
    {
        return $this->database->getAllGamesForRound($roundId);
    }

    public function getGamesInCurrentTournamentForPlayer (Player $player): array
    {
        return $this->database->getGamesInCurrentTournamentForPlayer(
                $player->getId());
    }

    public function getGameById (int $id): Game
    {
        return $this->database->getGameById($id);
    }

    public function deleteGameFromDatabase (): int
    {
        return $this->database->deleteGame($this->game);
    }

    private function referencedObjectsExistInDatabase (): bool
    {
        return $this->database->playersAndRoundExistInDatabase(
                $this->game->getPlayerWhite()
                    ->getId(),
                $this->game->getPlayerBlack()
                    ->getId(),
                $this->game->getRound()
                    ->getId());
    }

    private function isGameValid (): bool
    {
        // The game is considered valid when the references in the database
        // exist
        // The member variable $this->game might returen false on
        // $this->game->isGameValid()
        // That will happen when just the object id's have been inserted in the
        // game without
        // the other attributes
        // That is a valid staus for update purposes
        return $this->referencedObjectsExistInDatabase();
    }
}

?>