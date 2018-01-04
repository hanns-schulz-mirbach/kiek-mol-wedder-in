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

require_once ("./controller/access_controller.php");
require_once ("./util/session_manager.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if (! $accessController->create_Schedule()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

?>
<!doctype html>
<html lang="de">
<head>

<?php
require_once ("./template/head.php");
?>

</head>
<body>
	<?php require_once ("./template/header.php"); ?>
	<?php require_once ("./template/navigation.php"); ?>
	<div id="workarea">
		<?php require_once ("./template/sidebar.php"); ?>
		
		<main class="central-display-area">
		<article>

			<h1>Ergebnis Neuanlage Rundenteilnahme</h1>
			<?php
require_once ("./controller/schedule_controller.php");
require_once ("./util/participation.php");
require_once ("./util/color.php");

$round_id = intval($_POST['round']);
$player_id = intval($_POST['player']);
$participationStatus = intval($_POST['participation']);
$desiredOpponent = intval($_POST['opponent']);
$desiredColor = intval($_POST['color']);

$controller = new ScheduleController();
$controller->instantiateSkeleton($round_id, $player_id, $participationStatus,
        $desiredOpponent, $desiredColor);
$affectedRows = $controller->insertSchedule();

if ($affectedRows == 0) {
    
    echo 'Die Rundenteilnahmedaten sind ungültig und können nicht übernommen werden. Bitte wiederholen Sie die  ';
    echo '<a href="schedule_create.php">Rundenteilnahmedatenanlage</a>';
    exit();
} else {
    if ($affectedRows == 1) {
        echo 'Die Rundenteilnahmedatenanlage war erfolgreich. Es wurden die folgenden Daten übernommen: ';
        echo "<br />";
        echo "<br />";
        echo ($controller->getSchedule());
    } else {
        echo 'Die Neuanlage der Rundenteilnahmedaten hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. die   ';
        echo '<a href="schedule_create.php">Rundenteilnahmedatenanlage</a>';
    }
}
?>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>