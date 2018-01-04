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
require_once ("./util/formatter.php");
require_once ("./util/session_manager.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);
if (! $accessController->access_MySchedule()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$playerId = $sessionManager->getUserId();
if ($sessionManager->isUserIdSet()) {
    $playerController = new PlayerController();
    $player = $playerController->getPlayerById($playerId);
} else {
    $player = null;
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
			<h1>Bestätigung Aktualisierung Spielplandaten für <?php echo ($player->getFirstname() . ' ' . $player->getLastname());?></h1>
			<?php
if (isset($player)) {
    $scheduleController = new ScheduleController();
    $schedules = $scheduleController->getSchedulesForPlayer($player);
    $roundController = new RoundController();
    $allRoundsForCurrentTournament = $roundController->getRoundsForCurrentTournament();
    $formatter = new Formatter();
    
    foreach ($allRoundsForCurrentTournament as $round) {
        foreach ($schedules as $schedule) {
            $roundScheduleIdString = $round->getId() . "-" . $schedule->getId();
            $opponentIdString = "o-" . $roundScheduleIdString;
            $colorIdString = "c-" . $roundScheduleIdString;
            if (isset($_POST[$roundScheduleIdString])) {
                $newParticipationStatus = intval($_POST[$roundScheduleIdString]);
                $newOpponent = intval($_POST[$opponentIdString]);
                $newColor = intval($_POST[$colorIdString]);
                $scheduleNeedsUpdate = ($newParticipationStatus !=
                         $schedule->getParticipation()->getParticipationStatus()) ||
                         ($newOpponent !=
                         $schedule->getDesiredOpponent()->getId()) || ($newColor !=
                         $schedule->getDesiredColor()->getGameColor());
                if ($scheduleNeedsUpdate) {
                    $schedule->getParticipation()->setParticipationStatus(
                            intval($_POST[$roundScheduleIdString]));
                    $schedule->getDesiredOpponent()->setId(
                            intval($_POST[$opponentIdString]));
                    $schedule->getDesiredColor()->setGameColor(
                            intval($_POST[$colorIdString]));
                    $scheduleController->setSchedule($schedule);
                    $scheduleController->updateSchedule();
                }
            }
        }
        $roundNewScheduleIdString = $round->getId() . "-" . "-1";
        $opponentIdString = "o-" . $roundNewScheduleIdString;
        $colorIdString = "c-" . $roundNewScheduleIdString;
        
        if (isset($_POST[$roundNewScheduleIdString])) {
            $newSchedule = new Schedule();
            $newSchedule->getParticipation()->setParticipationStatus(
                    intval($_POST[$roundNewScheduleIdString]));
            $newSchedule->setPlayer($player);
            $newSchedule->setRound($round);
            $newSchedule->getDesiredOpponent()->setId(
                    intval($_POST[$opponentIdString]));
            $newSchedule->getDesiredColor()->setGameColor(
                    intval($_POST[$colorIdString]));
            $scheduleController->setSchedule($newSchedule);
            $scheduleController->insertSchedule();
        }
    }
    
    $updatedSchedules = $scheduleController->getSchedulesForPlayer($player);
    
    echo ("<br />");
    echo ($formatter->getAllSchedulesAsReadonlyTable($updatedSchedules));
} else {
    echo ("Sie haben aktuell keine Berechtigungen zur Aktualisierung des Spielplans. Bitte melden Sie sich am Systen an, falls Sie einen Benutzerzugang haben.");
}

?>
		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>