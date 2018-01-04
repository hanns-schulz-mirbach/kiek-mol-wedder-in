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

require_once ("./controller/tournament_controller.php");
require_once ("./controller/round_controller.php");
require_once ("./controller/game_controller.php");
require_once ("./controller/report_controller.php");
require_once ("./controller/ranking_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./model/tournament.php");
require_once ("./model/round.php");
require_once ("./model/game.php");
require_once ("./model/report.php");
require_once ("./model/ranking.php");
require_once ("./util/formatter.php");
require_once ("./util/session_manager.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();
$accessController = new AccessController($userRole);

if (! $accessController->access_TournamentOverviewAllFinalized()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$tournamentController = new TournamentController();
$tournaments = $tournamentController->getAllFinalizedTournaments();

if (! empty($tournaments)) {
    
    $roundController = new RoundController();
    
    if (isset($_GET["r_id"])) {
        $round = $roundController->getRoundById(intval($_GET["r_id"]));
        $tournament = $tournamentController->getTournamentById(
                $round->getTournament()
                    ->getId());
    } else {
        if (isset($_GET["t_id"])) {
            $tournament = $tournamentController->getTournamentById(
                    intval($_GET["t_id"]));
        } else {
            $tournament = $tournaments[0];
        }
    }
    
    $rounds = $roundController->getAllRoundsForTournament($tournament);
    if (! isset($round)) {
        $round = $rounds[0];
    }
    
    $formatter = new Formatter();
    $targetUrl = "tournament_overview_all_finalized.php";
    $idStringTournament = "?t_id=";
    
    $tournamentLinkCollection = $formatter->getAllTournamentsAsLinkCollection(
            $targetUrl, $tournaments, $idStringTournament);
    
    $idStringRound = "?r_id=";
    
    $roundLinkCollection = $formatter->getAllRoundsAsLinkCollection($targetUrl,
            $rounds, $idStringRound);
    
    $gameController = new GameController();
    $games = $gameController->getAllGamesForRound($round->getId());
    
    $reportController = new ReportController();
    $reportController->deleteAllObsoleteReports();
    $reports = $reportController->getAllReportsForRound($round->getId());
    
    $rankingController = new RankingController();
    $tournamentRanking = $rankingController->getRankingForTournament(
            $tournament);
}

?>
<!doctype html>
<html lang="de">
<head>
<?php require_once ("./template/head.php"); ?>
</head>
<body>
	<?php require_once ("./template/header.php"); ?>
	<?php require_once ("./template/navigation.php"); ?>
	<div id="workarea">
		<?php require_once ("./template/sidebar.php"); ?>
		<main class="central-display-area">
		<article>
<?php

if (! empty($tournaments)) {
    echo ("$tournamentLinkCollection");
    
    echo ("<h1> Übersicht zum abgeschlossenen Turnier " .
             $tournament->getTournamentTitle() . "</h1>");
    
    echo ("<br />");
    echo ("Startdatum: " . $tournament->getStartDate() . ", " . "Enddatum: " .
             $tournament->getStartDate());
    echo ("<h2>Abschlußtabelle</h2>");
    echo ($formatter->getTournamentRankingAsTable($tournamentRanking));
    echo ("<h2>Rundenergebnisse</h2>");
    echo ("$roundLinkCollection");
    echo ("<br />");
    echo ($formatter->getAllGamesAsReadonlyTable($games));
    echo ("<br/>");
    if (count($reports) > 0) {
        echo ("<h2>Rundenberichte</h2>");
    }
    
    foreach ($reports as $report) {
        $reportTitle = $report->getReportTitle();
        $reportTitleDisplay = html_entity_decode($reportTitle,
                ENT_QUOTES | ENT_XML1, 'UTF-8');
        $reportText = $report->getReportText();
        $reportTextDisplay = html_entity_decode($reportText,
                ENT_QUOTES | ENT_XML1, 'UTF-8');
        echo ("<h1>" . "$reportTitleDisplay" . " </h1>");
        echo ("<h2> von " . $report->getReportAuthor()->getFirstname() . " " .
                 $report->getReportAuthor()->getLastname() . "</h2> </br>");
        echo ("$reportTextDisplay" . "<br /> <br />");
        
        echo ("Publikationsdatum: " .
                 $report->getPublicationDate()->format('d.m.Y'));
        echo ("<br /> <hr />");
    }
} else {
    echo ("Es sind noch keine Daten zu abgeschlossenen Turniern vorhanden. ");
    ;
}

?>	
		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>