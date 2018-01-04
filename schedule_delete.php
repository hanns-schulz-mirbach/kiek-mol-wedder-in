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

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if (! $accessController->delete_Schedule()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$controller = new ScheduleController();
$schedule = $controller->getScheduleById(intval($_GET["id"]));
$controller->setSchedule($schedule);

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
			<h1>Bestätigung Löschung Rundenteilnahmedaten</h1>
			<?php

if ($controller->deleteScheduleFromDatabase() == 1) {
    echo 'Die Turnierrundenteilnahmedaten wurden erfolgreich gelöscht.';
    echo "<br />";
    echo ("Gelöschter Datensatz: ");
    echo "<br />";
    echo "$schedule";
} else {
    echo ('Die Löschung der Turnierrundenteilnahmedaten hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. den ');
    echo ('<a href="schedule_update.php?id=' . $schedule->getId() .
             '">Vorgang</a> .');
}
?>
		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>

</body>
</html>