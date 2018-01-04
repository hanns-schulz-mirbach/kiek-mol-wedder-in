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

require_once ("./controller/game_controller.php");
require_once ("./controller/round_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./controller/tournament_controller.php");
require_once ("./model/game.php");
require_once ("./util/formatter.php");
require_once ("./util/session_manager.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();
$accessController = new AccessController($userRole);

if (! $accessController->access_GameOverviewAllCurrentTournament()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$tournamentController = new TournamentController();
$currentTournament = $tournamentController->getMostRecentActiveTournament();

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

echo ("<h1>Partieergebnisübersicht " . $currentTournament->getTournamentTitle() .
         "</h1>");

$roundController = new RoundController();
$allRounds = $roundController->getRoundsForCurrentTournament();

if (! empty($allRounds)) {
    
    if (isset($_GET["id"])) {
        $roundId = intval($_GET["id"]);
    } else {
        $roundId = $allRounds[0]->getId();
    }
    
    $gameController = new GameController();
    $games = $gameController->getAllGamesForRound($roundId);
    
    $formatter = new Formatter();
    $targetUrl = "game_overview_all_current_tournament.php";
    
    echo ($formatter->getAllRoundsAsLinkCollection($targetUrl, $allRounds));
    echo ("<br/>");
    
    echo ($formatter->getAllGamesAsTable($games));
    echo ("<br />");
    echo ("<a href='game_create.php'>Neues Partieergebnis</a>");
} else {
    echo ("Es sind noch keine Rundenergebnisse für das aktuelle Turnier vorhanden. ");
}
?>	

		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>