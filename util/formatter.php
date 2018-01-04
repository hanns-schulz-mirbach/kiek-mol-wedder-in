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
require_once ("./model/round.php");
require_once ("./model/player.php");
require_once ("./model/schedule.php");
require_once ("./model/game.php");
require_once ("./model/ranking.php");
require_once ("./model/report.php");
require_once ("./util/participation.php");
require_once ("./util/result.php");
require_once ("./util/user_role.php");
require_once ("./util/color.php");

class Formatter
{

    public function getAllPlayersAsTableForUpdate (string $updateUrl,
            array $allPlayers): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Nachname</th><th>Vorname</th><th>E-mail</th><th>Festnetznummer</th><th>Mobilnummer</th><th>Verein</th><th>DWZ</th><th>ELO</th><th>Rolle</th></th><th>Aktiv</th</tr>";
        
        $tableBody = '';
        
        foreach ($allPlayers as $player) {
            if ($player->getIsActive() == 0) {
                $activityString = "Nein";
            } else {
                $activityString = "Ja";
            }
            $newTableRow = "<tr><td><a href='" . $updateUrl . "?id=" .
                     $player->getId() . "'>" . $player->getId() . "</a></td><td>" .
                     $player->getLastname() . "</td><td>" .
                     $player->getFirstname() . "</td>" . "<td>" .
                     $player->getMail() . "</td><td>" . $player->getTelephone() .
                     "</td><td>" . $player->getCellphone() . "</td><td>" .
                     $player->getClub() . "</td><td>" . $player->getDwz() .
                     "</td><td>" . $player->getElo() . "</td><td>" .
                     $player->getRole() . "</td><td>" . "$activityString" .
                     "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllPlayersAsTable (array $allPlayers): string
    {
        $tableHeader = "<table><tr><th>Nachname</th><th>Vorname</th><th>E-mail</th><th>Festnetznummer</th><th>Mobilnummer</th><th>Verein</th><th>DWZ</th><th>ELO</th></tr>";
        
        $tableBody = '';
        
        foreach ($allPlayers as $player) {
            $newTableRow = "<tr><td>" . $player->getLastname() . "</td><td>" .
                     $player->getFirstname() . "</td>" . "<td>" .
                     $player->getMail() . "</td><td>" . $player->getTelephone() .
                     "</td><td>" . $player->getCellphone() . "</td><td>" .
                     $player->getClub() . "</td><td>" . $player->getDwz() .
                     "</td><td>" . $player->getElo() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllTournamentsAsTableForUpdate (array $allTournaments): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Turniertitel</th><th>Startdatum</th><th>Enddatum</th><th>Turnier abgeschlossen</th></tr>";
        
        $tableBody = '';
        
        foreach ($allTournaments as $tournament) {
            if ($tournament->getIsFinalized() == 0) {
                $finalizationString = "Nein";
            } else {
                $finalizationString = "Ja";
            }
            $newTableRow = "<tr><td>" . "<a href='tournament_update.php?id=" .
                     $tournament->getId() . "'>" . $tournament->getId() .
                     "</a></td><td>" . $tournament->getTournamentTitle() .
                     "</td>" . "<td>" . $tournament->getStartDate() . "</td>" .
                     "<td>" . $tournament->getEndDate() . "</td>" . "<td>" .
                     "$finalizationString" . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllTournamentsAsLinkCollection (string $targetUrl,
            array $allTournaments, string $idString = "?t_id"): string
    {
        $collectionHeader = "<div> ";
        
        $collectionBody = '';
        
        foreach ($allTournaments as $tournament) {
            $newLink = "<a href='" . $targetUrl . "$idString" .
                     $tournament->getId() . "'>" .
                     $tournament->getTournamentTitle() . "</a>" . '&emsp; ';
            $collectionBody = $collectionBody . $newLink;
        }
        
        $collectionFooter = "</div>";
        
        return ($collectionHeader . $collectionBody . $collectionFooter);
    }

    public function getAllRoundsAsTableForUpdate (array $allRounds): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Turnier</th><th>Rundenbeschreibung</th><th>Rundendatum</th></tr>";
        
        $tableBody = '';
        
        foreach ($allRounds as $round) {
            $newTableRow = "<tr><td>" . "<a href='round_update.php?id=" .
                     $round->getId() . "'>" . $round->getId() . "</a></td><td>" .
                     "<a href='tournament_update.php?id=" .
                     $round->getTournament()->getId() . "'>" .
                     $round->getTournament()->getTournamentTitle() .
                     "</a></td><td>" . $round->getRoundDescription() .
                     "</td><td>" . $round->getRoundDate() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllRoundsAsLinkCollection (string $targetUrl,
            array $allRounds, string $idString = "?id="): string
    {
        $collectionHeader = "<div> ";
        
        $collectionBody = '';
        
        foreach ($allRounds as $round) {
            $newLink = "<a href='" . $targetUrl . "$idString" . $round->getId() .
                     "'>" . $round->getRoundDescription() . "</a>" . '&emsp; ';
            $collectionBody = $collectionBody . $newLink;
        }
        
        $collectionFooter = "</div>";
        
        return ($collectionHeader . $collectionBody . $collectionFooter);
    }

    public function getAllRoundsAsTable (array $allRounds): string
    {
        if (sizeof($allRounds) == 0) {
            return "<table><tr><th>Beschreibung</th><th>Rundendatum</th></tr></table>";
        }
        
        $tournamentTitle = $allRounds[0]->getTournament()->getTournamentTitle();
        $tableHeader = "<table><caption>" . "$tournamentTitle" . "</caption>" .
                 "<tr><th>Beschreibung</th><th>Rundendatum</th></tr>";
        
        $tableBody = '';
        
        foreach ($allRounds as $round) {
            
            $newTableRow = "<tr><td>" . $round->getRoundDescription() .
                     "</td><td>" . $round->getRoundDate() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getRoundUpdateForm (Round $round, array $tournaments): string
    {
        $tournamentSelectName = "tournament";
        $tournamentSelection = $this->getTournamentsForSelection($tournaments,
                $tournamentSelectName,
                $round->getTournament()
                    ->getId());
        
        $roundForm = '<table>' . $this->getDataTableHeader() . '<tr><td> 
        <label for="round_description">Rundenbeschreibung:</label> </td><td> <input type="text"
                name="round_description" id="round_description" required ' .
                 'value="' . $round->getRoundDescription() . '"' . '>
                </td></tr> 
                <tr><td>
                <label for="round_date">Rundendatum:</label> </td><td> <input type="text"
                        name="round_date" id="round_date" value =' .
                 '"' . $round->getRoundDate() . '"' .
                 ' placeholder="TT.MM.JJJJ" required
                        pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
                                title="Datumseingabe im Format TT.MM.JJJJ"' . '>
                        </td></tr>
                                <tr><td>
                                <label for="' .
                 "$tournamentSelectName" . '">Turnier:</label> </td><td>' .
                 "$tournamentSelection" . '</td></tr></table>' .
                 $this->getSubmitResetControl();
        
        return $roundForm;
    }

    public function getAllSchedulesAsTable (array $allSchedules,
            $targetUrl = "schedule_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Turnier</th><th>Runde</th><th>Spieler</th><th>Teilnahme</th><th>Wunschgegner</th><th>Wunschfarbe</th></tr>";
        
        $tableBody = '';
        
        foreach ($allSchedules as $schedule) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $schedule->getId() . "'>" . $schedule->getId() .
                     "</a></td><td>" . $schedule->getRound()
                        ->getTournament()
                        ->getTournamentTitle() . "</td><td>" .
                     $schedule->getRound()->getRoundDescription() . ", " .
                     $schedule->getRound()->getRoundDate() . "</td><td>" .
                     $schedule->getPlayer()->getLastName() . "</td><td>" .
                     $schedule->getParticipation() . "</td><td>" .
                     $schedule->getDesiredOpponent()->getLastName() . "</td><td>" .
                     $schedule->getDesiredColor() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllSchedulesAsReadonlyTable (array $allSchedules): string
    {
        $tableHeader = "<table><tr><th>Runde</th><th>Datum</th><th>Teilnahme</th><th>Wunschgegner</th><th>Wunschfarbe</th></tr>";
        
        $tableBody = '';
        
        foreach ($allSchedules as $schedule) {
            
            $newTableRow = "<tr><td>" .
                     $schedule->getRound()->getRoundDescription() . "</td><td>" .
                     $schedule->getRound()->getRoundDate() . "</td><td>" .
                     $schedule->getParticipation() . "</td><td>" .
                     $schedule->getDesiredOpponent()->getLastName() . "</td><td>" .
                     $schedule->getDesiredColor() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllSchedulesWithPlayerDataAsReadonlyTable (
            array $allSchedules): string
    {
        $tableHeader = "<table><tr><th>Runde</th><th>Datum</th><th>Spieler</th><th>Teilnahme</th><th>Wunschgegner</th><th>Wunschfarbe</th></tr>";
        
        $tableBody = '';
        
        foreach ($allSchedules as $schedule) {
            
            $newTableRow = "<tr><td>" .
                     $schedule->getRound()->getRoundDescription() . "</td><td>" .
                     $schedule->getRound()->getRoundDate() . "</td><td>" .
                     $schedule->getPlayer()->getLastname() . ', ' .
                     $schedule->getPlayer()->getFirstname() . "</td><td>" .
                     $schedule->getParticipation() . "</td><td>" .
                     $schedule->getDesiredOpponent()->getLastName() . "</td><td>" .
                     $schedule->getDesiredColor() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllReportsAsTableForUpdate (array $allReports,
            $updateUrl = "report_update.php"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Autor</th><th>Publikationsdatum</th><th>Ablaufdatum</th>Titel<th></th><th>Text</th></tr>";
        
        $tableBody = '';
        
        foreach ($allReports as $report) {
            
            $textLength = strlen($report->getReportText());
            $truncatedReportText = substr($report->getReportText(), 0, 50) .
                     " ... insgesamt " . "$textLength" . " Zeichen";
            $showUrl = "<a href='report_show.php?id=" . $report->getId() . "'>";
            $newTableRow = "<tr><td>" . "<a href='" . "$updateUrl" . "?id=" .
                     $report->getId() . "'>" . $report->getId() . "</a></td><td>" .
                     $report->getReportAuthor()->getFirstname() . " " .
                     $report->getReportAuthor()->getLastname() . "</td><td>" .
                     $report->getPublicationDate()->format('d.m.Y') . "</td><td>" .
                     $report->getObsolescenceDate()->format('d.m.Y') .
                     "</td><td>" . $report->getReportTitle() . "</td><td>" .
                     "$showUrl" . "$truncatedReportText" . "</a>" . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllReportsAsReadonlyTable (array $allReports): string
    {
        $tableHeader = "<table><tr><th>Autor</th><th>Publikationsdatum</th><th>Ablaufdatum</th><th>Titel</th><th>Text</th></tr>";
        
        $tableBody = '';
        
        foreach ($allReports as $report) {
            
            $textLength = strlen($report->getReportText());
            $truncatedReportText = substr($report->getReportText(), 0, 50) .
                     " ... insgesamt " . "$textLength" . " Zeichen";
            $showUrl = "<a href='report_show.php?id=" . $report->getId() . "'>";
            $newTableRow = "<tr><td>" .
                     $report->getReportAuthor()->getFirstname() . " " .
                     $report->getReportAuthor()->getLastname() . "</td><td>" .
                     $report->getPublicationDate()->format('d.m.Y') . "</td><td>" .
                     $report->getObsolescenceDate()->format('d.m.Y') .
                     "</td><td>" . $report->getReportTitle() . "</td><td>" .
                     "$showUrl" . "$truncatedReportText" . "</a>" . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getPlayerSchedulesForm (string $targerURL, array $rounds,
            array $allSchedules, array $allPlayers): string
    {
        $playerSchedulesForm = "<form action='" . $targerURL . "' method='post'>";
        $tableHeader = "<table><tr><th>Runde</th><th>Datum</th><th>Teilnahme</th><th>Wunschgegner</th><th>Wunschfarbe</th></tr>";
        $tableBody = '';
        
        foreach ($rounds as $round) {
            $schedule = $this->getScheduleForRound($allSchedules, $round);
            
            if (isset($schedule) && (! ($schedule->getId() == - 1))) {
                $participationSelectionName = $round->getId() . "-" .
                         $schedule->getId();
                $participationSelection = $this->getParticipationSelection(
                        $participationSelectionName,
                        $schedule->getParticipation()
                            ->getParticipationStatus());
                
                $opponentSelectionName = "o-" . $participationSelectionName;
                $opponentSelection = $this->getPlayersForSelection($allPlayers,
                        $opponentSelectionName,
                        $schedule->getDesiredOpponent()
                            ->getId());
                
                $colorSelectionName = "c-" . $participationSelectionName;
                $colorSelection = $this->getColorSelection($colorSelectionName,
                        $schedule->getDesiredColor()
                            ->getGameColor());
                
                $newTableRow = "<tr><td>" . $round->getRoundDescription() .
                         "</td><td>" . $round->getRoundDate() . "</td><td>" .
                         "$participationSelection" . "</td><td>" .
                         "$opponentSelection" . "</td><td>" . "$colorSelection" .
                         "</td></tr>";
                
                $tableBody = $tableBody . $newTableRow;
            } else {
                $participationSelectionName = $round->getId() . "-" . "-1";
                $participationSelection = $this->getParticipationSelection(
                        $participationSelectionName);
                
                $opponentSelectionName = "o-" . $participationSelectionName;
                $opponentSelection = $this->getPlayersForSelection($allPlayers,
                        $opponentSelectionName);
                
                $colorSelectionName = "c-" . $participationSelectionName;
                $colorSelection = $this->getColorSelection($colorSelectionName);
                
                $newTableRow = "<tr><td>" . $round->getRoundDescription() .
                         "</td><td>" . $round->getRoundDate() . "</td><td>" .
                         "$participationSelection" . "</td><td>" .
                         "$opponentSelection" . "</td><td>" . "$colorSelection" .
                         "</td></tr>";
                
                $tableBody = $tableBody . $newTableRow;
            }
        }
        
        $tableFooter = "</table>";
        
        $submit = '<input name="absenden" type="submit" value="Speichern">';
        
        $playerSchedulesForm = $playerSchedulesForm . $tableHeader . $tableBody .
                 $tableFooter . $submit . "</form>";
        
        return $playerSchedulesForm;
    }

    public function getAllGamesAsTable (array $allGames,
            string $targetUrl = "game_update.php?id"): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Turnier</th><th>Runde</th><th>Partiedatum</th><th>Spieler Weiß</th><th>Spieler Schwarz</th><th>Ergebnis</th></tr>";
        
        $tableBody = '';
        
        foreach ($allGames as $game) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "=" .
                     $game->getId() . "'>" . $game->getId() . "</a></td><td>" . $game->getRound()
                        ->getTournament()
                        ->getTournamentTitle() . "</td><td>" .
                     $game->getRound()->getRoundDescription() . "</td><td>" .
                     $game->getDateOfGameForDisplay() . "</td><td>" .
                     $game->getPlayerWhite()->getLastname() . ", " .
                     $game->getPlayerWhite()->getFirstname() . "</td><td>" .
                     $game->getPlayerBlack()->getLastname() . ", " .
                     $game->getPlayerBlack()->getFirstname() . "</td><td>" .
                     $game->getResult() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllGamesForCurrentTournamentAsTable (string $targetUrl,
            array $allGames): string
    {
        $tableHeader = "<table><tr><th>Id</th><th>Runde</th><th>Partiedatum</th><th>Spieler Weiß</th><th>Spieler Schwarz</th><th>Ergebnis</th></tr>";
        
        $tableBody = '';
        
        foreach ($allGames as $game) {
            
            $newTableRow = "<tr><td>" . "<a href='" . "$targetUrl" . "?id=" .
                     $game->getId() . "'>" . $game->getId() . "</a></td><td>" .
                     $game->getRound()->getRoundDescription() . "</td><td>" .
                     $game->getDateOfGameForDisplay() . "</td><td>" .
                     $game->getPlayerWhite()->getLastname() . ", " .
                     $game->getPlayerWhite()->getFirstname() . "</td><td>" .
                     $game->getPlayerBlack()->getLastname() . ", " .
                     $game->getPlayerBlack()->getFirstname() . "</td><td>" .
                     $game->getResult() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getAllGamesAsReadonlyTable (array $allGames): string
    {
        $tableHeader = "<table><tr><th>Runde</th><th>Partiedatum</th><th>Spieler Weiß</th><th>Spieler Schwarz</th><th>Ergebnis</th></tr>";
        
        $tableBody = '';
        
        foreach ($allGames as $game) {
            
            $newTableRow = "<tr><td>" . $game->getRound()->getRoundDescription() .
                     "</td><td>" . $game->getDateOfGameForDisplay() . "</td><td>" .
                     $game->getPlayerWhite()->getLastname() . ", " .
                     $game->getPlayerWhite()->getFirstname() . "</td><td>" .
                     $game->getPlayerBlack()->getLastname() . ", " .
                     $game->getPlayerBlack()->getFirstname() . "</td><td>" .
                     $game->getResult() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getSubmitResetControl (): string
    {
        $submitSelectControl = '<div class="submit-reset"><input name="absenden" type="submit" value="Speichern"> <input name="reset" type="reset" value="Zurücksetzen"></div>';
        
        return $submitSelectControl;
    }

    public function getSubmitResetDeleteControl (string $deleteURL): string
    {
        $submitSelectDeleteControl = '<div class="submit-reset"><input name="absenden" type="submit" value="Speichern"> <input name="reset" type="reset" value="Zurücksetzen"> ' .
                 "$deleteURL" . '</div>';
        
        return $submitSelectDeleteControl;
    }

    public function getDataTableHeader (): string
    {
        $dataTableHeader = '<tr><th>Attribut</th><th>Wert</th></tr>';
        
        return $dataTableHeader;
    }

    public function getTournamentRankingAsTable (array $tournamentRanking): string
    {
        $tableHeader = "<table><tr><th>Platz</th><th>Nachname</th><th>Vorname</th><th>Punkte</th><th>Feinwertung</th><th>&Oslash; DWZ Gegner</th><th>#Weiß</th><th>#Schwarz</th><th># Gewinne</th><th># Remis</th><th># Verluste</th><th># Gewinne kampflos</th><th># Verluste kampflos</th></tr>";
        
        $tableBody = '';
        
        foreach ($tournamentRanking as $ranking) {
            
            $newTableRow = "<tr><td>" . $ranking->getRank() . "</td><td>" .
                     $ranking->getPlayer()->getLastname() . "</td><td>" .
                     $ranking->getPlayer()->getFirstname() . "</td><td>" .
                     $ranking->getScore() . "</td><td>" .
                     $ranking->getSonnebornBergerScore() . "</td><td>" .
                     $ranking->getAverageDwzOfOpponents() . "</td><td>" .
                     $ranking->getNumOfGamesWhite() . "</td><td>" .
                     $ranking->getNumOfGamesBlack() . "</td><td>" .
                     $ranking->getNumOfWins() . "</td><td>" .
                     $ranking->getNumOfDraws() . "</td><td>" .
                     $ranking->getNumOfLosses() . "</td><td>" .
                     $ranking->getNumOfDefaultWins() . "</td><td>" .
                     $ranking->getNumOfDefaultLosses() . "</td></tr>";
            $tableBody = $tableBody . $newTableRow;
        }
        
        $tableFooter = "</table>";
        
        return ($tableHeader . $tableBody . $tableFooter);
    }

    public function getTournamentsForSelection (array $tournaments, $selectName,
            int $tournamentId = -1): string
    {
        $selectHead = "<select name='" . "$selectName" . "'>";
        
        $selectBody = '';
        
        $rowNumber = 0;
        
        foreach ($tournaments as $tournament) {
            if ((($rowNumber == 0) && ($tournamentId == - 1)) ||
                     ($tournament->getId() == $tournamentId)) {
                $newSelectRow = "<option selected value = '" .
                 $tournament->getId() . "' >" . $tournament->getTournamentTitle() .
                 "</option>";
    } else {
        $newSelectRow = "<option value = '" . $tournament->getId() . "' >" .
                 $tournament->getTournamentTitle() . "</option>";
    }
    
    $selectBody = $selectBody . $newSelectRow;
    $rowNumber ++;
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getRoundsForSelection (array $rounds, string $selectName,
    int $roundId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

$rowNumber = 0;

foreach ($rounds as $round) {
    if ((($rowNumber == 0) && ($roundId == - 1)) || ($round->getId() == $roundId)) {
        $newSelectRow = "<option selected value = '" . $round->getId() . "' >" .
                 $round->getRoundDescription() . ", " . $round->getRoundDate() .
                 "</option>";
    } else {
        $newSelectRow = "<option value = '" . $round->getId() . "' >" .
                 $round->getRoundDescription() . ", " . $round->getRoundDate() .
                 "</option>";
    }
    
    $selectBody = $selectBody . $newSelectRow;
    $rowNumber ++;
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getRoundsForSelectionWithUnknownRound (array $rounds,
    string $selectName, int $roundId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

$rowNumber = 0;

foreach ($rounds as $round) {
    if ($round->getId() == $roundId) {
        $newSelectRow = "<option selected value = '" . $round->getId() . "' >" .
                 $round->getRoundDescription() . ", " . $round->getRoundDate() .
                 "</option>";
    } else {
        $newSelectRow = "<option value = '" . $round->getId() . "' >" .
                 $round->getRoundDescription() . ", " . $round->getRoundDate() .
                 "</option>";
    }
    
    $selectBody = $selectBody . $newSelectRow;
    $rowNumber ++;
}

if ($roundId == - 1) {
    $unknownRound = "<option selected value = '-1' > Unbekannt </option>";
} else {
    $unknownRound = "<option value = '-1' > Unbekannt </option>";
}

$selectBody = $selectBody . $unknownRound;

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getPlayersForSelection (array $players, $selectName,
    int $playerId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

foreach ($players as $player) {
    if ($player->getId() == $playerId) {
        $newSelectRow = "<option selected value = '" . $player->getId() . "' >" .
                 $player->getLastname() . ", " . $player->getFirstname() .
                 "</option>";
    } else {
        $newSelectRow = "<option value = '" . $player->getId() . "' >" .
                 $player->getLastname() . ", " . $player->getFirstname() .
                 "</option>";
    }
    
    $selectBody = $selectBody . $newSelectRow;
}

if ($playerId == - 1) {
    $unknownPlayer = "<option selected value = '-1' > Unbekannt </option>";
} else {
    $unknownPlayer = "<option value = '-1' > Unbekannt </option>";
}

$selectBody = $selectBody . $unknownPlayer;

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getPlayersForSelectionWithoutUnknown (array $players, $selectName,
    int $playerId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

foreach ($players as $player) {
    if ($player->getId() == $playerId) {
        $newSelectRow = "<option selected value = '" . $player->getId() . "' >" .
                 $player->getLastname() . ", " . $player->getFirstname() .
                 "</option>";
    } else {
        $newSelectRow = "<option value = '" . $player->getId() . "' >" .
                 $player->getLastname() . ", " . $player->getFirstname() .
                 "</option>";
    }
    
    $selectBody = $selectBody . $newSelectRow;
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getOpponentSelection (array $players, $selectName, int $playerId,
    int $opponentId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

foreach ($players as $player) {
    if (($player->getId() != $playerId) && ($player->getId() != $opponentId)) {
        $newSelectRow = "<option value = '" . $player->getId() . "' >" .
                 $player->getLastname() . ", " . $player->getFirstname() .
                 "</option>";
        $selectBody = $selectBody . $newSelectRow;
    } elseif (($player->getId() != $playerId) && ($player->getId() == $opponentId)) {
        $newSelectRow = "<option selected value = '" . $player->getId() . "' >" .
                 $player->getLastname() . ", " . $player->getFirstname() .
                 "</option>";
        $selectBody = $selectBody . $newSelectRow;
    }
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getParticipationSelection (string $selectName,
    int $participationId = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

for ($i = 0; $i < 3; $i ++) {
    $participation = new Participation();
    $participation->setParticipationStatus($i);
    if (($i == $participationId) || (($participationId == - 1) && ($i == 2))) {
        $selectBody = $selectBody . "<option value=" . "$i" . ' selected>' .
                 "$participation" . "</option>";
    } else {
        $selectBody = $selectBody . "<option value=" . "$i" . ' >' .
                 "$participation" . "</option>";
    }
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getColorSelection (string $selectName, int $colorValue = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

for ($i = Color::getLowestResultValue(); $i < Color::getNumberOfResultValues(); $i ++) {
    $color = new Color();
    $color->setGameColor($i);
    if ($i == $colorValue) {
        $selectBody = $selectBody . "<option value=" . "$i" . ' selected>' .
                 "$color" . "</option>";
    } else {
        $selectBody = $selectBody . "<option value=" . "$i" . ' >' . "$color" .
                 "</option>";
    }
}
$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getResultSelection (string $selectName, int $resultValue = -1): string
{
$selectHead = "<select name='" . "$selectName" . "'>";

$selectBody = '';

for ($i = Result::getLowestResultValue(); $i < Result::getNumberOfResultValues(); $i ++) {
    $result = new Result();
    $result->setGameResult($i);
    if ($i == $resultValue) {
        $selectBody = $selectBody . "<option value=" . "$i" . ' selected>' .
                 "$result" . "</option>";
    } else {
        $selectBody = $selectBody . "<option value=" . "$i" . ' >' . "$result" .
                 "</option>";
    }
}

$selectFooter = "</select>";

return ($selectHead . $selectBody . $selectFooter);
}

public function getPlayerUpdateForm (Player $player, UserRole $userRole): string
{
if ($player->getIsActive() == 1) {
    $checkedString = "checked";
} else {
    $checkedString = "";
}
$roleSelectName = "rolle";
$roleSelection = $this->getRoleSelection($userRole, $player->getRole(),
        $roleSelectName);
$playerForm = '<table>' . $this->getDataTableHeader() . '<tr><td> 
        <label for="nachname">Nachname:</label> </td><td> <input type="text"
                name="nachname" id="nachname" required ' .
         'value="' . $player->getLastname() .
         '">
                </td></tr> 
                <tr><td>
                <label for="vorname">Vorname:</label>  </td><td> <input type="text" name="vorname" id="vorname" value = ' .
         '"' . $player->getFirstname() . '"' .
         '>
                        </td></tr> <tr><td> <label for="mail">E-Mail:</label>  </td><td> <input type="email" name="mail" id="mail" value = ' .
         '"' . $player->getMail() . '"' . ' required>
                                </td></tr>
                                <tr><td>
                                <label for="club">Verein:</label>  </td><td> <input type="text" name="club"
                                        id="club"  value = ' . '"' .
         $player->getClub() . '"' .
         '>
                                        </td></tr>
                                        <tr><td>
                                        <label for="dwz">DWZ:</label>  </td><td> <input type="number" name="dwz"
                                                id="dwz" min="0" max="2900" value = ' .
         $player->getDwz() .
         '>
                                                </td></tr>
                                                <tr><td>
                                                <label for="elo">ELO:</label> </td><td> <input type="number" name="elo"
                                                        id="elo" min="0" max="2900" value = ' .
         $player->getElo() .
         '>
                                                        </td></tr>
                                                        <tr><td>
                                                        <label for="phone">Festnetznummer:</label> </td><td> <input type="text"
                                                                name="phone" id="phone"
                                                                        value= ' .
         '"' . $player->getTelephone() . '"' .
         '>
                                                                        </td></tr>
                                                                        <tr><td>
                                                                        <label for="mobile">Mobilnummer:</label> </td><td> <input type="text"
                                                                                name="mobile" id="mobile" value= ' .
         '"' . $player->getCellphone() . '"' .
         '> </td></tr> <tr><td> <label for="' . $roleSelectName .
         '">Rolle:</label> </td><td> ' . $roleSelection .
         '<tr><td><label for="is_active">Nimmt am aktuellen Turnier teil:</label> </td><td> <input type="checkbox" name="is_active" id="is_active"  value = ' .
         '"' . $player->getIsActive() . '" ' . "$checkedString" . '/></td></tr>' .
         '</td></tr><tr><td><label for="passwd">Passwort:</label> </td><td> <input type="password" name="passwd" id="passwd" value= ' .
         '"' . $player->getPassword() . '"' . '> </td></tr></table>';

return $playerForm;
}

public function getTournamentUpdateForm (Tournament $tournament): string
{
if ($tournament->getIsFinalized() == 1) {
    $checkedString = "checked";
    $playerDeactivationString = "";
} else {
    $checkedString = "";
    $playerDeactivationString = '</tr></td> <tr><td> <label for="deactivate_all_players">Alle Spieler deaktivieren:</label> </td><td> <input type="checkbox" name="deactivate_all_players" id="deactivate_all_players" /></td></tr>';
}

$tournamnentForm = '<table>' . $this->getDataTableHeader() . '<tr><td>' .
         '<label for="tournamnet_title">Turniertitel:</label> </td><td> <input type="text" name="tournament_title" id="tournament_title" required ' .
         'value="' . $tournament->getTournamentTitle() . '"' . '> </tr></td> <tr><td>
<label for="start_date">Startdatum:</label> </td><td> <input type="text"
					name="start_date" id="start_date" required
					pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
					title="Datumseingabe im Format TT.MM.JJJJ" value = ' .
         $tournament->getStartDate() . '> </tr></td> <tr><td>
<label for="start_date">Enddatum:</label> </td><td> <input type="text"
					name="end_date" id="end_date" required
					pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
					title="Datumseingabe im Format TT.MM.JJJJ" value = ' .
         $tournament->getEndDate() . '>' . '</tr></td> <tr><td>
                                <label for="is_finalized">Turnier abgeschlossen:</label> </td><td> <input type="checkbox" name="is_finalized"
                                        id="is_finalized"  value = ' .
         '"' . $tournament->getIsFinalized() . '" ' . "$checkedString" .
         '/></td></tr>' . $playerDeactivationString . '</table>';

return $tournamnentForm;
}

private function getRoleSelection (UserRole $userRole, UserRole $playerRole,
    $selectName): string
{
$roleSelection = "";
if ($playerRole->isGuest()) {
    if ($userRole->isGuest()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option selected value='Gast'>Gast</option></select>";
    }
    if ($userRole->isPlayer()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option selected value='Gast'>Gast</option><option value='Spieler'>Spieler</option></select>";
    }
    if ($userRole->isTournamentLeader()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option selected value='Gast'>Gast</option><option value='Spieler'>Spieler</option><option value='Turnierleiter'>Turnierleiter</option></select>";
    }
    if ($userRole->isAdmin()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option selected value='Gast'>Gast</option><option value='Spieler'>Spieler</option><option value='Turnierleiter'>Turnierleiter</option><option value='Administrator'>Administrator</option></select>";
    }
}
if ($playerRole->isPlayer()) {
    if ($userRole->isPlayer()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option value='Gast'>Gast</option><option selected value='Spieler'>Spieler</option></select>";
    }
    if ($userRole->isTournamentLeader()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option value='Gast'>Gast</option><option selected value='Spieler'>Spieler</option><option value='Turnierleiter'>Turnierleiter</option></select>";
    }
    if ($userRole->isAdmin()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option value='Gast'>Gast</option><option selected value='Spieler'>Spieler</option><option value='Turnierleiter'>Turnierleiter</option><option value='Administrator'>Administrator</option></select>";
    }
}
if ($playerRole->isTournamentLeader()) {
    if ($userRole->isPlayer()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option value='Gast'>Gast</option><option selected value='Spieler'>Spieler</option></select>";
    }
    if ($userRole->isTournamentLeader()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option value='Gast'>Gast</option><option value='Spieler'>Spieler</option><option selected value='Turnierleiter'>Turnierleiter</option></select>";
    }
    if ($userRole->isAdmin()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option value='Gast'>Gast</option><option value='Spieler'>Spieler</option><option selected value='Turnierleiter'>Turnierleiter</option><option value='Administrator'>Administrator</option></select>";
    }
}
if ($playerRole->isAdmin()) {
    if ($userRole->isPlayer()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option value='Gast'>Gast</option><option selected value='Spieler'>Spieler</option></select>";
    }
    if ($userRole->isTournamentLeader()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option value='Gast'>Gast</option><option selected value='Spieler'>Spieler</option><option value='Turnierleiter'>Turnierleiter</option></select>";
    }
    if ($userRole->isAdmin()) {
        $roleSelection = "<select name=" . "$selectName" .
                 "> <option value='Gast'>Gast</option><option selected value='Spieler'>Spieler</option><option value='Turnierleiter'>Turnierleiter</option><option selected value='Administrator'>Administrator</option></select>";
    }
}

return $roleSelection;
}

private function getScheduleForRound (array $schedules, Round $round): Schedule
{
foreach ($schedules as $schedule) {
    if ($schedule->getRound()->getId() == $round->getId()) {
        return $schedule;
    }
}
$schedule = new Schedule();
return $schedule;
}
}

?>