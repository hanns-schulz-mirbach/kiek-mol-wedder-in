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

require_once ("./controller/schedule_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./controller/player_controller.php");
require_once ("./controller/round_controller.php");
require_once ("./model/schedule.php");
require_once ("./model/player.php");
require_once ("./util/formatter.php");
require_once ("./util/session_manager.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();
$playerId = $sessionManager->getUserId();
if ($sessionManager->isUserIdSet()) {
    $playerController = new PlayerController();
    $player = $playerController->getPlayerById($playerId);
} else {
    $player = null;
}
$accessController = new AccessController($userRole);

if (! $accessController->access_MySchedule()) {
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
			<h1>Partiewünsche von <?php echo ($player->getFirstname() . ' ' . $player->getLastname());?></h1>
<?php
if (isset($player)) {
    $scheduleController = new ScheduleController();
    $schedules = $scheduleController->getSchedulesForPlayer($player);
    $allPlayers = $playerController->getAllActivePlayersWithoutSelf(
            $player->getId());
    $roundController = new RoundController();
    $allRoundsForCurrentTournament = $roundController->getRoundsForCurrentTournament();
    $formatter = new Formatter();
    $targetURL = "schedule_for_player_update_confirmation.php";
    echo ($formatter->getPlayerSchedulesForm($targetURL,
            $allRoundsForCurrentTournament, $schedules, $allPlayers));
} else {
    echo ("Sie haben aktuell keine Berechtigungen für die Partiewunschübersicht. Bitte melden Sie sich am Systen an, falls Sie einen Benutzerzugang haben.");
}

?>	

		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>