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

require_once ("./model/player.php");
require_once ("./model/tournament.php");
require_once ("./model/round.php");
require_once ("./model/schedule.php");
require_once ("./model/game.php");
require_once ("./model/ranking.php");
require_once ("./model/report.php");
require_once ("./util/participation.php");
require_once ("./util/result.php");

class Database
{

    private $dbHost;

    private $dbName;

    private $dbUser;

    private $dbPassword;

    private $mySQLDatabase;

    private $dbPort;

    public function __construct ()
    {
        /*
         * // Local deployment
         * $this->dbHost = '127.0.0.1';
         * $this->dbName = 'kiekmolwedderin';
         * $this->dbUser = 'hanns';
         * $this->dbPassword = 'hanns';
         * $this->dbPort = 3306;
         */
        
        // AWS deployment
        $this->dbHost = $_SERVER['RDS_HOSTNAME'];
        $this->dbName = $_SERVER['RDS_DB_NAME'];
        $this->dbUser = $_SERVER['RDS_USERNAME'];
        $this->dbPassword = $_SERVER['RDS_PASSWORD'];
        $this->dbPort = $_SERVER['RDS_PORT'];
        
        $this->mySQLDatabase = new mysqli($this->dbHost, $this->dbUser,
                $this->dbPassword, $this->dbName, $this->dbPort);
        if ($this->mySQLDatabase->connect_errno) {
            echo "Keine Verbindung zur MySQL Datenbank: (" .
                     $this->mySQLDatabase->connect_errno . ") " .
                     $this->mySQLDatabase->connect_error;
        }
    }

    public function __destruct ()
    {
        $this->mySQLDatabase = null;
    }

    public function insertPlayer (Player $aPlayer): int
    {
        $tablename = 'player';
        $sqlStatement = $aPlayer->getInsertSQL($tablename);
        
        if (! $this->mySQLDatabase->query($sqlStatement)) {
            echo "Neuanlage der Spielerdaten ist wegen eines Datenbankfehlers fehlgeschlagen: (" .
                     $this->mySQLDatabase->errno . ") " .
                     $this->mySQLDatabase->error;
        }
        return $this->mySQLDatabase->affected_rows;
    }

    public function insertTournament (Tournament $aTournament): int
    {
        $sqlStatement = 'INSERT INTO tournament ' .
                 " (tournament_title, start_date, end_date, is_finalized) " .
                 " VALUES (' " . $aTournament->getTournamentTitle() . " ' , ' " .
                 $aTournament->getStartDateForDB() . " ' , ' " .
                 $aTournament->getEndDateForDB() . " ' ,  " .
                 $aTournament->getIsFinalized() . "  )";
        
        $this->executeSQLStatement($sqlStatement, "Turnierdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function insertRound (Round $aRound): int
    {
        $sqlStatement = 'INSERT INTO round ' .
                 " (description, date, tournament) " . " VALUES (' " .
                 $aRound->getRoundDescription() . " ' , ' " .
                 $aRound->getRoundDateForDB() . " ' , " .
                 $aRound->getTournament()->getId() . "  )";
        
        $this->executeSQLStatement($sqlStatement, "Rundendaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function insertSchedule (Schedule $aSchedule): int
    {
        $sqlStatement = 'INSERT INTO schedule ' .
                 " (player, participation, round, desired_opponent, desired_color) " .
                 " VALUES ( " . $aSchedule->getPlayer()->getId() . "  ,  " .
                 $aSchedule->getParticipation()->getParticipationStatus() .
                 "  , " . $aSchedule->getRound()->getId() . "  , " .
                 $aSchedule->getDesiredOpponent()->getId() . "  , " .
                 $aSchedule->getDesiredColor()->getGameColor() . "  )";
        
        $this->executeSQLStatement($sqlStatement, "Partieplandaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function insertGame (Game $aGame): int
    {
        $sqlStatement = 'INSERT INTO game ' .
                 " (player_white, player_black, result, date_of_game, round) " .
                 " VALUES ( " . $aGame->getPlayerWhite()->getId() . "  ,  " .
                 $aGame->getPlayerBlack()->getId() . "  ,  " .
                 $aGame->getResult()->getGameResult() . "  ,  '" .
                 $aGame->getDateOfGameForDB() . "'  ,  " .
                 $aGame->getRound()->getId() . "  )";
        
        $this->executeSQLStatement($sqlStatement, "Partiedaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function insertReport (Report $aReport): int
    {
        $sqlStatement = 'INSERT INTO report ' .
                 " (report_author, report_title, round, report_text, publication_date, obsolescence_date) " .
                 " VALUES ( " . $aReport->getReportAuthor()->getId() . "  ,  '" .
                 $aReport->getEncodedReportTitle() . "'  , " .
                 $aReport->getRound()->getId() . "  , '" .
                 $aReport->getEncodedReportText() . "'  , '" .
                 $aReport->getPublicationDate()->format('Y-m-d') . "'  , '" .
                 $aReport->getObsolescenceDate()->format('Y-m-d') . "'  )";
        
        $this->executeSQLStatement($sqlStatement, "Reportdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function insertRanking (Ranking $aRanking): int
    {
        $sqlStatement = 'INSERT INTO ranking ' .
                 " (player, accumulated_dwz_opponents, num_of_games_white, num_of_games_black, num_of_draws, num_of_losses, num_of_wins, tournament, sonneborn_berger_score, num_of_default_losses, num_of_default_wins) " .
                 " VALUES ( " . $aRanking->getPlayer()->getId() . "  ,  " .
                 $aRanking->getAccumulatedDwzOpponents() . "  ,  " .
                 $aRanking->getNumOfGamesWhite() . "  ,  " .
                 $aRanking->getNumOfGamesBlack() . "  ,  " .
                 $aRanking->getNumOfDraws() . "  ,  " .
                 $aRanking->getNumOfLosses() . "  ,  " .
                 $aRanking->getNumOfWins() . "  ,  " .
                 $aRanking->getTournament()->getId() . "  ,  " .
                 $aRanking->getSonnebornBergerScore() . "  ,  " .
                 $aRanking->getNumOfDefaultLosses() . "  ,  " .
                 $aRanking->getNumOfDefaultWins() . "  )";
        
        $this->executeSQLStatement($sqlStatement, "Ranglistendaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updatePlayer (Player $aPlayer): int
    {
        $tablename = 'player';
        $sqlStatement = $aPlayer->getUpdateSQL($tablename);
        
        $this->executeSQLStatement($sqlStatement, "Spielerdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deactivateAllPlayers (): int
    {
        $tablename = 'player';
        $sqlStatement = "UPDATE player SET is_active = 0";
        
        $this->executeSQLStatement($sqlStatement, "Spielerdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function playerExistsInDatabase (int $playerId): bool
    {
        $sqlStatement = 'select count(*) as objectcount from player p where p.id=' .
                 $playerId;
        $objectCount = $this->getObjectCountFromDatabase($sqlStatement);
        if ($objectCount == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function anyPlayerExistsInDatabase (): bool
    {
        $sqlStatement = 'select count(*) as objectcount from player';
        $objectCount = $this->getObjectCountFromDatabase($sqlStatement);
        if ($objectCount != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function playerAndRoundExistInDatabase (int $playerId, int $roundId): bool
    {
        $sqlStatement = 'select count(*) as objectcount from player p where (p.id=' .
                 $playerId . ' ) and exists (select * from round r where r.id=' .
                 $roundId . ')';
        $objectCount = $this->getObjectCountFromDatabase($sqlStatement);
        if ($objectCount == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function playersAndRoundExistInDatabase (int $playerWhiteId,
            int $playerBlackId, int $roundId): bool
    {
        $sqlStatement = 'select count(*) as objectcount from player p where ((p.id=' .
                 $playerWhiteId . ' ) or (p.id=' . $playerBlackId .
                 ')) and exists (select * from round r where r.id=' . $roundId .
                 ')';
        $objectCount = $this->getObjectCountFromDatabase($sqlStatement);
        if ($objectCount == 2) {
            return true;
        } else {
            return false;
        }
    }

    public function updateTournament (Tournament $aTournament): int
    {
        $tablename = 'tournament';
        $sqlStatement = $aTournament->getUpdateSQL($tablename);
        
        $this->executeSQLStatement($sqlStatement, "Turnierdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateRound (Round $aRound): int
    {
        $tablename = 'round';
        $sqlStatement = $aRound->getUpdateSQL($tablename);
        
        $this->executeSQLStatement($sqlStatement, "Rundendaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateSchedule (Schedule $aSchedule): int
    {
        $tablename = 'schedule';
        $sqlStatement = $aSchedule->getUpdateSQL($tablename);
        
        $this->executeSQLStatement($sqlStatement, "Rundenteilnahmedaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateReport (Report $aReport): int
    {
        $tablename = 'report';
        $sqlStatement = $aReport->getUpdateSQL($tablename);
        
        $this->executeSQLStatement($sqlStatement, "Reportdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateGame (Game $aGame): int
    {
        $tablename = 'game';
        $sqlStatement = $aGame->getUpdateSQL($tablename);
        
        $this->executeSQLStatement($sqlStatement, "Partiedaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function updateRanking (Ranking $aRanking): int
    {
        $sqlStatement = 'UPDATE ranking SET ' . "player = " .
                 $aRanking->getPlayer()->getId() . "  ,  " .
                 "accumulated_dwz_opponents = " .
                 $aRanking->getAccumulatedDwzOpponents() . "  ,  " .
                 "num_of_games_white = " . $aRanking->getNumOfGamesWhite() .
                 "  ,  " . "num_of_games_black = " .
                 $aRanking->getNumOfGamesBlack() . "  ,  " . "num_of_draws = " .
                 $aRanking->getNumOfDraws() . "  ,  " . "num_of_losses = " .
                 $aRanking->getNumOfLosses() . "  ,  " . "num_of_wins = " .
                 $aRanking->getNumOfDefaultWins() . "  ,  " . "tournament = " .
                 $aRanking->getTournament()->getId() . "  ,  " .
                 "sonneborn_berger_score = " .
                 $aRanking->getSonnebornBergerScore() . "  ,  " .
                 "num_of_default_losses = " . $aRanking->getNumOfDefaultLosses() .
                 "  ,  " . "num_of_default_wins = " .
                 $aRanking->getNumOfDefaultWins() . "  )" . " WHERE id = " .
                 $aRanking->getId();
        
        $this->executeSQLStatement($sqlStatement, "Partiedaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteSchedule (Schedule $aSchedule): int
    {
        $sqlStatement = 'DELETE FROM schedule where id = ' . $aSchedule->getId();
        
        $this->executeSQLStatement($sqlStatement, "Turnierrundenteilnahmedaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteReport (Report $aReport): int
    {
        $sqlStatement = 'DELETE FROM report where id = ' . $aReport->getId();
        
        $this->executeSQLStatement($sqlStatement, "Reportdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteReportsOlderAs (DateTime $obsolescenceDate): int
    {
        $obsolescenceDateString = $obsolescenceDate->format('Y-m-d');
        $sqlStatement = "DELETE FROM report where obsolescence_date < '" .
                 "$obsolescenceDateString" . "'";
        
        $this->executeSQLStatement($sqlStatement, "Reportdaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteGame (Game $aGame): int
    {
        $sqlStatement = 'DELETE FROM game where id = ' . $aGame->getId();
        
        $this->executeSQLStatement($sqlStatement, "Partiedaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteAllRankings (): int
    {
        $sqlStatement = 'delete from ranking';
        
        $this->executeSQLStatement($sqlStatement, "Ranglistendaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function deleteAllRankingsForTournament (int $tournamentId): int
    {
        $sqlStatement = 'delete from ranking where tournament = ' .
                 "$tournamentId";
        
        $this->executeSQLStatement($sqlStatement, "Ranglistendaten");
        
        return $this->mySQLDatabase->affected_rows;
    }

    public function getPlayerByEmailAndPassword (string $email, string $passwd): Player
    {
        $selectSQL = "SELECT * from player WHERE e_mail = ' " . "$email" . " ' " .
                 " AND passwd = ' " . "$passwd" . " ' ";
        
        return $this->getPlayerFromDatabase($selectSQL);
    }

    public function getPlayerById (int $aPlayerId): Player
    {
        $selectSQL = "SELECT * from player WHERE id = " . $aPlayerId;
        
        return $this->getPlayerFromDatabase($selectSQL);
    }

    public function getTournamentById (int $aTournamentId): Tournament
    {
        $selectSQL = "SELECT * from tournament WHERE id = " . $aTournamentId;
        
        return $this->getTournamentFromDatabase($selectSQL);
    }

    public function getRoundById (int $aRoundId): Round
    {
        $selectSQL = "SELECT * from round WHERE id = " . $aRoundId;
        
        return $this->getRoundFromDatabase($selectSQL);
    }

    public function getScheduleById (int $aScheduleId): Schedule
    {
        $selectSQL = $selectSQL = "SELECT s.id as s_id, s.player as p_id, s.desired_opponent as o_id, s.round as r_id, s.participation as par, s.desired_color as col, r.date as r_date, r.description as r_desc, p.first_name as p_fn, p.last_name as p_ln, o.first_name as o_fn, o.last_name as o_ln, t.id as t_id, t.tournament_title as t_t from schedule s join round r on r.id = s.round join tournament t on t.id = r.tournament join player p on p.id = s.player left join player o on o.id = s.desired_opponent" .
                 ' WHERE s.id = ' . "$aScheduleId";
        
        return $this->getScheduleFromDatabase($selectSQL);
    }

    public function getReportById (int $aReportId): Report
    {
        $selectSQL = $selectSQL = "SELECT r.id as r_id, r.report_author as r_a, r.round as r_r, r.publication_date as r_pd, r.obsolescence_date as r_od, r.report_title as r_ti, r.report_text as r_t, ro.date as ro_date, ro.description as ro_desc, p.first_name as a_fn, p.last_name as a_ln, t.id as t_id, t.tournament_title as t_t from report r join player p on p.id = r.report_author left join round ro on ro.id = r.round left join tournament t on t.id = ro.tournament " .
                 ' WHERE r.id = ' . "$aReportId";
        
        return $this->getReportFromDatabase($selectSQL);
    }

    public function getGameById (int $aGameId): Game
    {
        $selectSQL = $selectSQL = "SELECT g.id as g_id, g.date_of_game as g_date, g.result as g_res, pw.id as pw_id, pw.first_name as pw_fn, pw.last_name as pw_ln, pw.dwz as pw_dwz, pb.id as pb_id, pb.first_name as pb_fn, pb.last_name as pb_ln, pb.dwz as pb_dwz, r.id as r_id, r.date as r_date, r.description as r_desc, t.tournament_title as t_t from game g join round r on r.id = g.round join tournament t on t.id = r.tournament join player pw on pw.id = g.player_white join player pb on pb.id = g.player_black WHERE g.id = " .
                 "$aGameId" . ' ORDER BY r.date';
        
        return $this->getGameFromDatabase($selectSQL);
    }

    public function getAllPlayers (): array
    {
        $selectSQL = "SELECT * from player ORDER BY last_name ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $playerResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $player = $this->extractPlayerFromResultSet($resultRow);
            $playerResultArray[$i] = $player;
            $i ++;
        }
        $resultSet->free();
        return $playerResultArray;
    }

    public function getAllActivePlayers (): array
    {
        $selectSQL = "SELECT * from player WHERE is_active = 1 ORDER BY last_name ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $playerResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $player = $this->extractPlayerFromResultSet($resultRow);
            $playerResultArray[$i] = $player;
            $i ++;
        }
        $resultSet->free();
        return $playerResultArray;
    }

    public function getAllActivePlayersWithoutSelf (int $selfId): array
    {
        $selectSQL = "SELECT * from player WHERE is_active = 1 AND id != " .
                 $selfId . " ORDER BY last_name ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $playerResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $player = $this->extractPlayerFromResultSet($resultRow);
            $playerResultArray[$i] = $player;
            $i ++;
        }
        $resultSet->free();
        return $playerResultArray;
    }

    public function getAllTournaments (): array
    {
        $selectSQL = "SELECT * from tournament ORDER BY start_date DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $tournamentResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $tournament = $this->extractTournamentFromResultSet($resultRow);
            $tournamentResultArray[$i] = $tournament;
            $i ++;
        }
        $resultSet->free();
        return $tournamentResultArray;
    }

    public function getAllActiveTournaments (): array
    {
        $selectSQL = "SELECT * from tournament t WHERE is_finalized = 0 ORDER BY t.start_date DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $tournamentResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $tournament = $this->extractTournamentFromResultSet($resultRow);
            $tournamentResultArray[$i] = $tournament;
            $i ++;
        }
        $resultSet->free();
        return $tournamentResultArray;
    }

    public function getAllFinalizedTournaments (): array
    {
        $selectSQL = "SELECT * from tournament t WHERE is_finalized = 1 ORDER BY t.start_date DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $tournamentResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $tournament = $this->extractTournamentFromResultSet($resultRow);
            $tournamentResultArray[$i] = $tournament;
            $i ++;
        }
        $resultSet->free();
        return $tournamentResultArray;
    }

    public function getAllRounds (): array
    {
        $selectSQL = "SELECT r.id, r.date, r.description, r.tournament, t.tournament_title from round r join tournament t on t.id = r.tournament  ORDER BY r.date DESC ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $roundResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $round = $this->extractRoundFromResultSet($resultRow);
            $roundResultArray[$i] = $round;
            $i ++;
        }
        $resultSet->free();
        return $roundResultArray;
    }

    public function getAllRoundsForTournament (int $tournamentId): array
    {
        $selectSQL = 'SELECT r.id, r.date, r.description, r.tournament, t.tournament_title from round r join tournament t on t.id = r.tournament  where t.id = ' .
                 "$tournamentId" . ' ORDER BY r.date DESC ';
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $roundResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $round = $this->extractRoundFromResultSet($resultRow);
            $roundResultArray[$i] = $round;
            $i ++;
        }
        $resultSet->free();
        return $roundResultArray;
    }

    public function getAllActiveRounds (): array
    {
        $selectSQL = "SELECT r.id, r.date, r.description, r.tournament, t.tournament_title from round r join tournament t on t.id = r.tournament  WHERE t.is_finalized = 0 ORDER BY r.date DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $roundResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $round = $this->extractRoundFromResultSet($resultRow);
            $roundResultArray[$i] = $round;
            $i ++;
        }
        $resultSet->free();
        return $roundResultArray;
    }

    public function getAllSchedules (): array
    {
        $selectSQL = "SELECT s.id as s_id, s.player as p_id, s.desired_opponent as o_id, s.round as r_id, s.participation as par, s.desired_color as col, r.date as r_date, r.description as r_desc, p.first_name as p_fn, p.last_name as p_ln, o.first_name as o_fn, o.last_name as o_ln, t.id as t_id, t.tournament_title as t_t from schedule s join round r on r.id = s.round join tournament t on t.id = r.tournament join player p on p.id = s.player left join player o on o.id = s.desired_opponent ORDER BY t.start_date DESC, r.date DESC , p.last_name ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $scheduleResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $schedule = $this->extractScheduleFromResultSet($resultRow);
            $scheduleResultArray[$i] = $schedule;
            $i ++;
        }
        $resultSet->free();
        return $scheduleResultArray;
    }

    public function getAllActiveSchedules (): array
    {
        $selectSQL = "SELECT s.id as s_id, s.player as p_id, s.desired_opponent as o_id, s.round as r_id, s.participation as par, s.desired_color as col, r.date as r_date, r.description as r_desc, p.first_name as p_fn, p.last_name as p_ln, o.first_name as o_fn, o.last_name as o_ln, t.id as t_id, t.tournament_title as t_t from schedule s join round r on r.id = s.round join tournament t on t.id = r.tournament join player p on p.id = s.player left join player o on o.id = s.desired_opponent WHERE t.is_finalized = 0 ORDER BY t.start_date DESC, r.date DESC, p.last_name ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $scheduleResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $schedule = $this->extractScheduleFromResultSet($resultRow);
            $scheduleResultArray[$i] = $schedule;
            $i ++;
        }
        $resultSet->free();
        return $scheduleResultArray;
    }

    public function getAllReports (): array
    {
        $selectSQL = "SELECT r.id as r_id, r.report_author as r_a, r.round as r_r, r.publication_date as r_pd, r.obsolescence_date as r_od, r.report_title as r_ti, r.report_text as r_t, ro.date as ro_date, ro.description as ro_desc, p.first_name as a_fn, p.last_name as a_ln, t.id as t_id, t.tournament_title as t_t from report r join player p on p.id = r.report_author left join round ro on ro.id = r.round left join tournament t on t.id = ro.tournament ORDER BY r.publication_date DESC, p.last_name ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $reportResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $report = $this->extractReportFromResultSet($resultRow);
            $reportResultArray[$i] = $report;
            $i ++;
        }
        $resultSet->free();
        return $reportResultArray;
    }

    public function getAllActiveReports (): array
    {
        $selectSQL = "SELECT r.id as r_id, r.report_author as r_a, r.round as r_r, r.publication_date as r_pd, r.obsolescence_date as r_od, r.report_title as r_ti, r.report_text as r_t, ro.date as ro_date, ro.description as ro_desc, p.first_name as a_fn, p.last_name as a_ln, t.id as t_id, t.tournament_title as t_t from report r join player p on p.id = r.report_author join round ro on ro.id = r.round join tournament t on t.id = ro.tournament WHERE t.is_finalized = 0 ORDER BY r.publication_date DESC, p.last_name ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $reportResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $report = $this->extractReportFromResultSet($resultRow);
            $reportResultArray[$i] = $report;
            $i ++;
        }
        $resultSet->free();
        return $reportResultArray;
    }

    public function getAllReportsAuthoredBy (int $authorId): array
    {
        $selectSQL = 'SELECT r.id as r_id, r.report_author as r_a, r.round as r_r, r.publication_date as r_pd, r.obsolescence_date as r_od, r.report_title as r_ti, r.report_text as r_t, ro.date as ro_date, ro.description as ro_desc, p.first_name as a_fn, p.last_name as a_ln, t.id as t_id, t.tournament_title as t_t from report r join player p on p.id = r.report_author left join round ro on ro.id = r.round left join tournament t on t.id = ro.tournament WHERE r.report_author = ' .
                 "$authorId" . ' ORDER BY r.publication_date DESC, p.last_name ';
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $reportResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $report = $this->extractReportFromResultSet($resultRow);
            $reportResultArray[$i] = $report;
            $i ++;
        }
        $resultSet->free();
        return $reportResultArray;
    }

    public function getTopNReports (int $n): array
    {
        $selectSQL = "SELECT r.id as r_id, r.report_author as r_a, r.round as r_r, r.publication_date as r_pd, r.obsolescence_date as r_od, r.report_title as r_ti, r.report_text as r_t, ro.date as ro_date, ro.description as ro_desc, p.first_name as a_fn, p.last_name as a_ln, t.id as t_id, t.tournament_title as t_t from report r join player p on p.id = r.report_author left join round ro on ro.id = r.round left join tournament t on t.id = ro.tournament ORDER BY r.publication_date DESC, p.last_name LIMIT " .
                 "$n";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $reportResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $report = $this->extractReportFromResultSet($resultRow);
            $reportResultArray[$i] = $report;
            $i ++;
        }
        $resultSet->free();
        return $reportResultArray;
    }

    public function getAllReportsForRound (int $roundId): array
    {
        $selectSQL = "SELECT r.id as r_id, r.report_author as r_a, r.round as r_r, r.publication_date as r_pd, r.obsolescence_date as r_od, r.report_title as r_ti, r.report_text as r_t, ro.date as ro_date, ro.description as ro_desc, p.first_name as a_fn, p.last_name as a_ln, t.id as t_id, t.tournament_title as t_t from report r join player p on p.id = r.report_author join round ro on ro.id = r.round join tournament t on t.id = ro.tournament WHERE r.round = " .
                 "$roundId" . " ORDER BY r.publication_date DESC, p.last_name ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $reportResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $report = $this->extractReportFromResultSet($resultRow);
            $reportResultArray[$i] = $report;
            $i ++;
        }
        $resultSet->free();
        return $reportResultArray;
    }

    public function getAllReportsPublishedAfter (DateTime $publicationDate): array
    {
        $publicationDateString = "'" . $publicationDate->format('Y-m-d') . "'";
        $selectSQL = "SELECT r.id as r_id, r.report_author as r_a, r.round as r_r, r.publication_date as r_pd, r.obsolescence_date as r_od, r.report_title as r_ti, r.report_text as r_t, ro.date as ro_date, ro.description as ro_desc, p.first_name as a_fn, p.last_name as a_ln, t.id as t_id, t.tournament_title as t_t from report r join player p on p.id = r.report_author left join round ro on ro.id = r.round left join tournament t on t.id = ro.tournament WHERE r.publication_date > " .
                 "$publicationDateString" .
                 " ORDER BY r.publication_date DESC, p.last_name ";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $reportResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $report = $this->extractReportFromResultSet($resultRow);
            $reportResultArray[$i] = $report;
            $i ++;
        }
        $resultSet->free();
        return $reportResultArray;
    }

    public function getAllSchedulesForRound (int $roundId): array
    {
        $selectSQL = 'SELECT s.id as s_id, s.player as p_id, s.desired_opponent as o_id, s.round as r_id, s.participation as par, s.desired_color as col, r.date as r_date, r.description as r_desc, p.first_name as p_fn, p.last_name as p_ln, o.first_name as o_fn, o.last_name as o_ln, t.id as t_id, t.tournament_title as t_t from schedule s join round r on r.id = s.round join tournament t on t.id = r.tournament join player p on p.id = s.player left join player o on o.id = s.desired_opponent WHERE s.round = ' .
                 "$roundId" . ' ORDER BY p.last_name ';
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $scheduleResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $schedule = $this->extractScheduleFromResultSet($resultRow);
            $scheduleResultArray[$i] = $schedule;
            $i ++;
        }
        $resultSet->free();
        return $scheduleResultArray;
    }

    public function getSchedulesForPlayer (int $playerId): array
    {
        $selectSQL = "SELECT s.id as s_id, s.player as p_id, s.desired_opponent as o_id, s.round as r_id, s.participation as par, s.desired_color as col, r.date as r_date, r.description as r_desc, p.first_name as p_fn, p.last_name as p_ln, o.first_name as o_fn, o.last_name as o_ln, t.id as t_id, t.tournament_title as t_t from schedule s join round r on r.id = s.round join tournament t on t.id = r.tournament join player p on p.id = s.player left join player o on o.id = s.desired_opponent WHERE t.is_finalized = 0  AND p.id = " .
                 "$playerId" . " ORDER BY r.date DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $scheduleResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $schedule = $this->extractScheduleFromResultSet($resultRow);
            $scheduleResultArray[$i] = $schedule;
            $i ++;
        }
        $resultSet->free();
        return $scheduleResultArray;
    }

    public function getAllGames (): array
    {
        $selectSQL = "SELECT g.id as g_id, g.date_of_game as g_date, g.result as g_res, pw.id as pw_id, pw.first_name as pw_fn, pw.last_name as pw_ln, pw.dwz as pw_dwz, pb.id as pb_id, pb.first_name as pb_fn, pb.last_name as pb_ln, pb.dwz as pb_dwz, r.id as r_id, r.date as r_date, r.description as r_desc, t.tournament_title as t_t from game g join round r on r.id = g.round join tournament t on t.id = r.tournament join player pw on pw.id = g.player_white join player pb on pb.id = g.player_black ORDER BY t. start_date DESC, r.date DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $gameResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $game = $this->extractGameFromResultSet($resultRow);
            $gameResultArray[$i] = $game;
            $i ++;
        }
        $resultSet->free();
        return $gameResultArray;
    }

    public function getAllActiveGames (): array
    {
        $selectSQL = "SELECT g.id as g_id, g.date_of_game as g_date, g.result as g_res, pw.id as pw_id, pw.first_name as pw_fn, pw.last_name as pw_ln, pw.dwz as pw_dwz, pb.id as pb_id, pb.first_name as pb_fn, pb.last_name as pb_ln, pb.dwz as pb_dwz, r.id as r_id, r.date as r_date, r.description as r_desc, t.tournament_title as t_t from game g join round r on r.id = g.round join tournament t on t.id = r.tournament join player pw on pw.id = g.player_white join player pb on pb.id = g.player_black WHERE t.is_finalized = 0 ORDER BY t. start_date DESC, r.date DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $gameResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $game = $this->extractGameFromResultSet($resultRow);
            $gameResultArray[$i] = $game;
            $i ++;
        }
        $resultSet->free();
        return $gameResultArray;
    }

    public function getAllGamesForRound (int $roundId): array
    {
        $selectSQL = "SELECT g.id as g_id, g.date_of_game as g_date, g.result as g_res, pw.id as pw_id, pw.first_name as pw_fn, pw.last_name as pw_ln, pw.dwz as pw_dwz, pb.id as pb_id, pb.first_name as pb_fn, pb.last_name as pb_ln, pb.dwz as pb_dwz, r.id as r_id, r.date as r_date, r.description as r_desc, t.tournament_title as t_t from game g join round r on r.id = g.round join tournament t on t.id = r.tournament join player pw on pw.id = g.player_white join player pb on pb.id = g.player_black where g.round = " .
                 "$roundId";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $gameResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $game = $this->extractGameFromResultSet($resultRow);
            $gameResultArray[$i] = $game;
            $i ++;
        }
        $resultSet->free();
        return $gameResultArray;
    }

    public function getGamesInCurrentTournamentForPlayer (int $playerId): array
    {
        $selectSQL = "SELECT g.id as g_id, g.date_of_game as g_date, g.result as g_res, pw.id as pw_id, pw.first_name as pw_fn, pw.last_name as pw_ln, pw.dwz as pw_dwz, pb.id as pb_id, pb.first_name as pb_fn, pb.last_name as pb_ln, pb.dwz as pb_dwz, r.id as r_id, r.date as r_date, r.description as r_desc, t.tournament_title as t_t from game g join round r on r.id = g.round join tournament t on t.id = r.tournament join player pw on pw.id = g.player_white join player pb on pb.id = g.player_black where t.is_finalized = 0 AND ((g.player_white = " .
                 $playerId . ") OR (g.player_black = " . $playerId .
                 ")) ORDER BY g.date_of_game DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $gameResultArray = [];
        $i = 0;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $game = $this->extractGameFromResultSet($resultRow);
            $gameResultArray[$i] = $game;
            $i ++;
        }
        $resultSet->free();
        return $gameResultArray;
    }

    public function getFinishedGamesForTournament (int $tournamentId = -1): array
    {
        if ($tournamentId == - 1) {
            // get all finished games for currently open tournament
            $selectSQL = "SELECT g.id as g_id, g.date_of_game as g_date, g.result as g_res, pw.id as pw_id, pw.first_name as pw_fn, pw.last_name as pw_ln, pw.dwz as pw_dwz, pb.id as pb_id, pb.first_name as pb_fn, pb.last_name as pb_ln, pb.dwz as pb_dwz, r.id as r_id, r.date as r_date, r.description as r_desc, t.tournament_title as t_t from game g join round r on r.id = g.round join tournament t on t.id = r.tournament join player pw on pw.id = g.player_white join player pb on pb.id = g.player_black where t.is_finalized = 0 and g.result != 0";
        } else {
            // get all finished games for specified tournament
            $selectSQL = "SELECT g.id as g_id, g.date_of_game as g_date, g.result as g_res, pw.id as pw_id, pw.first_name as pw_fn, pw.last_name as pw_ln, pw.dwz as pw_dwz, pb.id as pb_id, pb.first_name as pb_fn, pb.last_name as pb_ln, pb.dwz as pb_dwz, r.id as r_id, r.date as r_date, r.description as r_desc, t.tournament_title as t_t from game g join round r on r.id = g.round join tournament t on t.id = r.tournament join player pw on pw.id = g.player_white join player pb on pb.id = g.player_black where g.result != 0 and t.id = " .
                     "$tournamentId";
        }
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $scheduleResultArray = [];
        $i = 0;
        
        $gameResultArray = [];
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $game = $this->extractGameFromResultSet($resultRow);
            $gameResultArray[$i] = $game;
            $i ++;
        }
        $resultSet->free();
        return $gameResultArray;
    }

    public function getAllRankings (): array
    {
        $selectSQL = "SELECT r.id as r_id, r.sonneborn_berger_score as r_sbs, p.id as p_id, p.first_name as p_fn, p.last_name as p_ln, p.dwz as p_dwz, r.num_of_losses as r_losses, r.num_of_draws as r_draws, r.num_of_wins as r_wins, r.num_of_default_wins as r_dwins, r.num_of_default_losses as r_dlosses, r.num_of_games_white as r_gw, r.num_of_games_black as r_gb, r.accumulated_dwz_opponents as r_accdwz, (r.num_of_losses * 1 + r.num_of_draws * 2 + r.num_of_wins * 3 + r.num_of_default_wins * 3) as r_score, r.tournament as t_id, t.tournament_title as t_title, t.is_finalized as t_isfin from ranking r join tournament t on t.id = r.tournament join player p on p.id = r.player ORDER BY r_score, r.sonneborn_berger_score";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $rankingResultArray = [];
        $i = 0;
        $rank = 1;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $ranking = $this->extractRankingFromResultSet($resultRow, $rank);
            $rankingResultArray[$i] = $ranking;
            $i ++;
            $rank ++;
        }
        $resultSet->free();
        return $rankingResultArray;
    }

    public function getRankingForCurrentTournament (): array
    {
        $selectSQL = "SELECT r.id as r_id, r.sonneborn_berger_score as r_sbs, p.id as p_id, p.first_name as p_fn, p.last_name as p_ln, p.dwz as p_dwz, r.num_of_losses as r_losses, r.num_of_draws as r_draws, r.num_of_wins as r_wins, r.num_of_default_wins as r_dwins, r.num_of_default_losses as r_dlosses, r.num_of_games_white as r_gw, r.num_of_games_black as r_gb, r.accumulated_dwz_opponents as r_accdwz, (r.num_of_losses * 1 + r.num_of_draws * 2 + r.num_of_wins * 3 + r.num_of_default_wins * 3) as r_score, r.tournament as t_id, t.tournament_title as t_title, t.is_finalized as t_isfin from ranking r join tournament t on t.id = r.tournament join player p on p.id = r.player where t.is_finalized = 0 ORDER BY r_score DESC, r.sonneborn_berger_score DESC, r.accumulated_dwz_opponents DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $rankingResultArray = [];
        $i = 0;
        $rank = 1;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $ranking = $this->extractRankingFromResultSet($resultRow, $rank);
            $rankingResultArray[$i] = $ranking;
            $i ++;
            $rank ++;
        }
        $resultSet->free();
        return $rankingResultArray;
    }

    public function getRankingForTournament (int $tournament_id): array
    {
        $selectSQL = "SELECT r.id as r_id, r.sonneborn_berger_score as r_sbs, p.id as p_id, p.first_name as p_fn, p.last_name as p_ln, p.dwz as p_dwz, r.num_of_losses as r_losses, r.num_of_draws as r_draws, r.num_of_wins as r_wins, r.num_of_default_wins as r_dwins, r.num_of_default_losses as r_dlosses, r.num_of_games_white as r_gw, r.num_of_games_black as r_gb, r.accumulated_dwz_opponents as r_accdwz, (r.num_of_losses * 1 + r.num_of_draws * 2 + r.num_of_wins * 3 + r.num_of_default_wins * 3) as r_score, r.tournament as t_id, t.tournament_title as t_title, t.is_finalized as t_isfin from ranking r join tournament t on t.id = r.tournament join player p on p.id = r.player where t.id = " .
                 "$tournament_id" .
                 " ORDER BY r_score DESC, r.sonneborn_berger_score DESC, r.accumulated_dwz_opponents DESC";
        
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        $rankingResultArray = [];
        $i = 0;
        $rank = 1;
        
        while ($resultRow = $resultSet->fetch_assoc()) {
            $ranking = $this->extractRankingFromResultSet($resultRow, $rank);
            $rankingResultArray[$i] = $ranking;
            $i ++;
            $rank ++;
        }
        $resultSet->free();
        return $rankingResultArray;
    }

    private function extractScheduleFromResultSet ($resultRow): Schedule
    {
        $schedule = new Schedule();
        $schedule->setId(intval($resultRow["s_id"]));
        $schedule->getRound()->setId(intval($resultRow["r_id"]));
        $schedule->getRound()->setRoundDate($resultRow["r_date"], "Y-m-d");
        $schedule->getRound()->setRoundDescription(trim($resultRow["r_desc"]));
        $schedule->getRound()
            ->getTournament()
            ->setId(intval($resultRow["t_id"]));
        $schedule->getRound()
            ->getTournament()
            ->setTournamentTitle(trim($resultRow["t_t"]));
        $schedule->getPlayer()->setId(intval($resultRow["p_id"]));
        $schedule->getPlayer()->setFirstname(trim($resultRow["p_fn"]));
        $schedule->getPlayer()->setLastname(trim($resultRow["p_ln"]));
        $schedule->getParticipation()->setParticipationStatus(
                intval($resultRow["par"]));
        
        // the database will always enforce $resultRow["o_i"] == -1 in case that
        // no id has been provided for the desired opponent. In this case there
        // will be no matching entry in the player table and the query result
        // will contain NULL values for first name and lasr name
        $schedule->getDesiredOpponent()->setId(intval($resultRow["o_id"]));
        if ($schedule->getDesiredOpponent()->getId() != - 1) {
            $schedule->getDesiredOpponent()->setFirstname(
                    trim($resultRow["o_fn"]));
            $schedule->getDesiredOpponent()->setLastname(
                    trim($resultRow["o_ln"]));
        }
        if (isset($resultRow["col"])) {
            $schedule->getDesiredColor()->setGameColor(
                    intval($resultRow["col"]));
        }
        
        return $schedule;
    }

    private function extractReportFromResultSet ($resultRow): Report
    {
        $report = new Report();
        $report->setId(intval($resultRow["r_id"]));
        $report->getReportAuthor()->setId(intval($resultRow["r_a"]));
        $report->getReportAuthor()->setFirstname(trim($resultRow["a_fn"]));
        $report->getReportAuthor()->setLastname(trim($resultRow["a_ln"]));
        $report->setPublicationDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["r_pd"])));
        $report->setObsolescenceDate(
                DateTime::createFromFormat('Y-m-d', trim($resultRow["r_od"])));
        $report->setReportTitle(trim($resultRow["r_ti"]));
        $report->setReportText(trim($resultRow["r_t"]));
        
        // the database will always enforce $resultRow["r_r"] == -1 in case that
        // no id has been provided for the round. In this case there
        // will be no matching entry in the round table and the query result
        // will contain NULL values for round and tournament data
        
        $report->getRound()->setId(intval($resultRow["r_r"]));
        if ($report->getRound()->getId() != - 1) {
            $report->getRound()->setRoundDate($resultRow["ro_date"], "Y-m-d");
            $report->getRound()->setRoundDescription(
                    trim($resultRow["ro_desc"]));
            $report->getRound()
                ->getTournament()
                ->setId(intval($resultRow["t_id"]));
            $report->getRound()
                ->getTournament()
                ->setTournamentTitle(trim($resultRow["t_t"]));
        }
        
        return $report;
    }

    private function extractGameFromResultSet ($resultRow): Game
    {
        $game = new Game();
        
        $game->setId(intval($resultRow["g_id"]));
        $game->setGameDateString($resultRow["g_date"], "Y-m-d");
        $game->getResult()->setGameResult(intval($resultRow["g_res"]));
        $game->getPlayerWhite()->setId(intval($resultRow["pw_id"]));
        $game->getPlayerWhite()->setFirstname(trim($resultRow["pw_fn"]));
        $game->getPlayerWhite()->setLastname(trim($resultRow["pw_ln"]));
        $game->getPlayerWhite()->setDwz(intval($resultRow["pw_dwz"]));
        $game->getPlayerBlack()->setId(intval($resultRow["pb_id"]));
        $game->getPlayerBlack()->setFirstname(trim($resultRow["pb_fn"]));
        $game->getPlayerBlack()->setLastname(trim($resultRow["pb_ln"]));
        $game->getPlayerBlack()->setDwz(intval($resultRow["pb_dwz"]));
        $game->getRound()->setId(intval($resultRow["r_id"]));
        $game->getRound()->setRoundDate($resultRow["r_date"], "Y-m-d");
        $game->getRound()->setRoundDescription(trim($resultRow["r_desc"]));
        $game->getRound()
            ->getTournament()
            ->setTournamentTitle(trim($resultRow["t_t"]));
        
        return $game;
    }

    private function extractRankingFromResultSet ($resultRow, int $rank): Ranking
    {
        $ranking = new Ranking();
        
        $ranking->setId(intval($resultRow["r_id"]));
        $ranking->setSonnebornBergerScore(intval($resultRow["r_sbs"]));
        $ranking->setNumOfLosses(intval($resultRow["r_losses"]));
        $ranking->setNumOfDraws(intval($resultRow["r_draws"]));
        $ranking->setNumOfWins(intval($resultRow["r_wins"]));
        $ranking->setNumOfDefaultWins(intval($resultRow["r_dwins"]));
        $ranking->setNumOfDefaultLosses(intval($resultRow["r_dlosses"]));
        $ranking->setNumOfGamesWhite(intval($resultRow["r_gw"]));
        $ranking->setNumOfGamesBlack(intval($resultRow["r_gb"]));
        $ranking->setAccumulatedDwzOpponents(intval($resultRow["r_accdwz"]));
        $ranking->setRank($rank);
        $ranking->getPlayer()->setId(intval($resultRow["p_id"]));
        $ranking->getPlayer()->setFirstname($resultRow["p_fn"]);
        $ranking->getPlayer()->setLastname($resultRow["p_ln"]);
        $ranking->getPlayer()->setDwz(intval($resultRow["p_dwz"]));
        $ranking->getTournament()->setId(intval($resultRow["t_id"]));
        $ranking->getTournament()->setTournamentTitle($resultRow["t_title"]);
        $ranking->getTournament()->setIsFinalized(intval($resultRow["t_isfin"]));
        
        return $ranking;
    }

    private function extractPlayerFromResultSet ($resultRow): Player
    {
        $player = new Player(trim($resultRow["first_name"]),
                trim($resultRow["last_name"]), trim($resultRow["e_mail"]),
                trim($resultRow["club"]), intval($resultRow["dwz"]),
                intval($resultRow["elo"]), trim($resultRow["phone"]),
                trim($resultRow["mobile"]), trim($resultRow["role"]),
                trim($resultRow["passwd"]));
        $player->setId(intval(trim($resultRow["id"])));
        $player->setIsActive(intval($resultRow["is_active"]));
        return $player;
    }

    private function extractTournamentFromResultSet ($resultRow): Tournament
    {
        $tournament = new Tournament();
        $tournament->setId(intval($resultRow["id"]));
        if (isset($resultRow["tournament_title"])) {
            $tournament->setTournamentTitle(
                    trim($resultRow["tournament_title"]));
        }
        
        $dateTimeZone = new DateTimeZone("Europe/Berlin");
        if (isset($resultRow["start_date"])) {
            $startDate = new DateTime($resultRow["start_date"], $dateTimeZone);
            $tournament->setStartDate($startDate->format('d.m.Y'));
        }
        if (isset($resultRow["end_date"])) {
            $endDate = new DateTime($resultRow["end_date"], $dateTimeZone);
            $tournament->setEndDate($endDate->format('d.m.Y'));
        }
        
        if (isset($resultRow["is_finalized"])) {
            $tournament->setIsFinalized(intval($resultRow["is_finalized"]));
        }
        
        return $tournament;
    }

    private function extractRoundFromResultSet ($resultRow): Round
    {
        $round = new Round();
        $round->setId(intval($resultRow["id"]));
        if (isset($resultRow["description"])) {
            $round->setRoundDescription(trim($resultRow["description"]));
        }
        
        $dateTimeZone = new DateTimeZone("Europe/Berlin");
        if (isset($resultRow["date"])) {
            $roundDate = new DateTime($resultRow["date"], $dateTimeZone);
            $round->setRoundDate($roundDate->format('d.m.Y'));
        }
        
        if (isset($resultRow["tournament"])) {
            $round->getTournament()->setId(intval($resultRow["tournament"]));
        }
        
        if (isset($resultRow["tournament_title"])) {
            $round->getTournament()->setTournamentTitle(
                    trim($resultRow["tournament_title"]));
        }
        
        return $round;
    }

    private function getPlayerFromDatabase (string $selectSQL): Player
    {
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $player = $this->extractPlayerFromResultSet($resultRow);
            $resultSet->free();
            
            return $player;
        } else {
            // there is either no matching player or the result is ambiguous
            $resultSet->free();
            return $player = new Player('', '', '', '', - 1, - 1, '', '', '', '');
        }
    }

    private function getTournamentFromDatabase (string $selectSQL): Tournament
    {
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $tournament = $this->extractTournamentFromResultSet($resultRow);
            $resultSet->free();
            
            return $tournament;
        } else {
            // there is either no matching tournament or the result is ambiguous
            $resultSet->free();
            return $tournament = new Tournament();
        }
    }

    private function getRoundFromDatabase (string $selectSQL): Round
    {
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $round = $this->extractRoundFromResultSet($resultRow);
            $resultSet->free();
            
            return $round;
        } else {
            // there is either no matching tournament or the result is ambiguous
            $resultSet->free();
            return $round = new Round();
        }
    }

    private function getScheduleFromDatabase (string $selectSQL): Schedule
    {
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $schedule = $this->extractScheduleFromResultSet($resultRow);
            $resultSet->free();
        } else {
            // there is either no matching tournament or the result is ambiguous
            $resultSet->free();
            $schedule = new Schedule();
        }
        
        return $schedule;
    }

    private function getReportFromDatabase (string $selectSQL): Report
    {
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $report = $this->extractReportFromResultSet($resultRow);
            $resultSet->free();
        } else {
            // there is either no matching tournament or the result is ambiguous
            $resultSet->free();
            $report = new Report();
        }
        
        return $report;
    }

    private function getGameFromDatabase (string $selectSQL): Game
    {
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            $game = $this->extractGameFromResultSet($resultRow);
            $resultSet->free();
        } else {
            // there is either no matching tournament or the result is ambiguous
            $resultSet->free();
            $game = new Game();
        }
        
        return $game;
    }

    private function getObjectCountFromDatabase (string $selectSQL): int
    {
        $objectCount = 0;
        $resultSet = $this->mySQLDatabase->query($selectSQL);
        
        if ($resultSet->num_rows == 1) {
            $resultRow = $resultSet->fetch_assoc();
            if (isset($resultRow["objectcount"])) {
                $objectCount = intval($resultRow["objectcount"]);
            }
            $resultSet->free();
        }
        return $objectCount;
    }

    private function executeSQLStatement (string $sqlStatement,
            string $errorMessage): void
    {
        if (! $this->mySQLDatabase->query($sqlStatement)) {
            echo "Änderung der " . $errorMessage .
                     " ist wegen eines Datenbankfehlers fehlgeschlagen: (" .
                     $this->mySQLDatabase->errno . ") " .
                     $this->mySQLDatabase->error;
        }
    }
}

?>