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
require_once ("./model/game.php");
require_once ("./controller/game_controller.php");
require_once ("./controller/ranking_controller.php");
require_once ("./controller/access_controller.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);
if (! $accessController->update_Game()) {
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
			<h1>Bestätigung Aktualisierung Partieergebnis</h1>
			<?php
$controller = new GameController();

// retrieve data prior to update from database
$game = $controller->getGameById(intval($_GET["id"]));

// retrieve posted data for update
$game_id = $game->getId();
$round_id = intval($_POST['round']);
$player_id = intval($_POST['player']);
$opponent_id = intval($_POST['opponent']);
$color_id = intval($_POST['color']);
$gameDate = DateTime::createFromFormat("d.m.Y", trim($_POST['gameDate']));
$resultValue = intval($_POST['result']);

// transfer posted data for update to game object managed by the controller
$controller->mapPlayerAndInstantiateSkeleton($game_id, $round_id, $player_id,
        $opponent_id, $color_id, $gameDate, $resultValue);
$affectedRows = $controller->updateGame(); // might be one (actual db update) or
                                           // 0 in case nothing has changed

if (($affectedRows == 0) || ($affectedRows == 1)) {
    echo 'Die Partiedaten wurden erfolgreich geändert.';
    echo "<br />";
    echo ("Ursprünglicher Datensatz: ");
    echo "<br />";
    echo "$game";
    echo "<br />";
    echo "<br />";
    echo 'Geänderter Datensatz: ';
    echo "<br />";
    echo $controller->getGame();
    $rankingController = new RankingController();
    $rankingController->deleteRankingsForActiveTournaments();
    $rankingController->createRankingsForActiveTournaments();
} else {
    echo ('Die Änderung der Partiedaten hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. den ');
    echo ('<a href="game_update.php?id=' . $game->getId() . '">Vorgang</a> .');
}

?>
		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>