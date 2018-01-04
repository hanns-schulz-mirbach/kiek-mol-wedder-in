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

require_once ("./util/session_manager.php");
require_once ("./model/round.php");
require_once ("./controller/round_controller.php");
require_once ("./controller/tournament_controller.php");
require_once ("./controller/access_controller.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if (! $accessController->update_Round()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
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
			<h1>Bestätigung Aktualisierung Turnierrundendaten</h1>
			<?php
$controller = new RoundController();
$tournamentController = new TournamentController();
$round = $controller->getRoundById(intval($_GET["id"]));
$tournament = $tournamentController->getTournamentById(
        intval($_POST["tournament"]));

$roundUpdate = new Round();

$roundUpdate->setId($round->getId());
$roundUpdate->setRoundDescription($_POST["round_description"]);
$roundUpdate->setRoundDate($_POST["round_date"]);
$roundUpdate->setTournament($tournament);

$controller->setRound($roundUpdate);
if ($controller->updateRound() == 1) {
    echo 'Die Turnierrundendaten wurden erfolgreich geändert.';
    echo "<br />";
    echo ("Ursprünglicher Datensatz: ");
    echo "<br />";
    echo "$round";
    echo "<br />";
    echo "<br />";
    echo 'Geänderter Datensatz: ';
    echo "<br />";
    echo "$roundUpdate";
} else {
    echo ('Die Änderung der Turnierrundendaten hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. den ');
    echo ('<a href="round_update.php?id=' . $tournament->getId() .
             '">Vorgang</a> .');
}

?>
		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>