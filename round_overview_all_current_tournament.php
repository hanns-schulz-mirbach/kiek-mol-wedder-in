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

require_once ("./controller/round_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./controller/tournament_controller.php");
require_once ("./model/round.php");
require_once ("./util/formatter.php");
require_once ("./util/session_manager.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();
$accessController = new AccessController($userRole);

if (! $accessController->access_RoundOverviewAllCurrentTournament()) {
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

$roundController = new RoundController();
$rounds = $roundController->getRoundsForCurrentTournament();
$formatter = new Formatter();

echo ("<h1>Übersicht Turnierrunden " . $currentTournament->getTournamentTitle() .
         "</h1>");

echo ($formatter->getAllRoundsAsTableForUpdate($rounds));
echo ("<br />");
echo ("<a href='round_create.php'>Neuer Datensatz</a>");

?>	
		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>