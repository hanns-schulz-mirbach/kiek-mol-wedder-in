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

require_once ("./model/schedule.php");
require_once ("./controller/schedule_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./util/session_manager.php");
require_once ("./util/formatter.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if (! $accessController->update_Schedule()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$formatter = new Formatter();

$controller = new ScheduleController();
$schedule = $controller->getScheduleById(intval($_GET["id"]));
$controller->setSchedule($schedule);

$roundSelectName = "round";
$roundSelection = $controller->getRoundSelection($roundSelectName,
        $schedule->getRound()
            ->getId());

$playerSelectName = "player";
$playerSelection = $controller->getPlayerSelection($playerSelectName,
        $schedule->getPlayer()
            ->getId());

$participationSelectName = "participation";
$participationSelection = $controller->getParticipationSelection(
        $participationSelectName,
        $schedule->getParticipation()
            ->getParticipationStatus());
$opponentSelectName = "opponent";
$opponentSelection = $controller->getPlayerSelection($opponentSelectName,
        $schedule->getDesiredOpponent()
            ->getId());

$colorSelectname = "color";
$color = new Color();
$colorSelection = $formatter->getColorSelection($colorSelectname,
        $schedule->getDesiredColor()
            ->getGameColor());

$targetURL = "schedule_update_confirmation.php?id=" . $schedule->getId();
$deleteURL = ' <a href="schedule_delete.php?id=' . $schedule->getId() .
         '">Löschen</a>';
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
			<h1>Aktualisierung Turnierrundenteilnahmedaten</h1>

			<form action="<?php echo ("$targetURL");?>" method="post">
				<table>
				<?php echo ($formatter->getDataTableHeader()); ?>
				<tr>
						<td><label for="round">Runde:</label></td>
						<td> 
				<?php echo("$roundSelection"); ?>
			</td>
					</tr>
					<tr>
						<td><label for="player">Spieler:</label></td>
						<td> 
				<?php echo("$playerSelection"); ?>
			</td>
					</tr>
					<tr>

						<td><label for="participation">Teilnahme:</label></td>
						<td>
					<?php echo ("$participationSelection");?>
				</td>
					</tr>
					<tr>
						<td><label for="$opponent">Wunschgegner:</label></td>
						<td>
				<?php echo("$opponentSelection"); ?>
			</td>
					</tr>
					<tr>
						<td><label for="$color">Wunschfarbe:</label></td>
						<td>
				<?php echo("$colorSelection"); ?>
			</td>
					</tr>
				</table>
				<?php
    if ($accessController->delete_Schedule()) {
        echo ($formatter->getSubmitResetDeleteControl($deleteURL));
    } else {
        echo ($formatter->getSubmitResetControl());
    }
    ?>
			</form>

		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>