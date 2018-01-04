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
require_once ("./controller/player_controller.php");
require_once ("./controller/access_controller.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$controller = new PlayerController();

$accessController = new AccessController($userRole);
if ((! $accessController->create_Player()) && ($controller->anyPlayerExistsInDatabase())) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
exit();
}

?>
<!doctype html>
<html lang="de">
<head>

<?php
require_once ("./template/head.php");
require_once ("./model/player.php");
?>

</head>
<body>
	<?php require_once ("./template/header.php"); ?>
	<?php require_once ("./template/navigation.php"); ?>
	<div id="workarea">
		<?php include ("./template/sidebar.php"); ?>
		
		<main class="central-display-area">
		<article>

			<h1>Ergebnis Spielerneuregistrierung</h1>
			<?php

$player = new Player($_POST["vorname"], $_POST["nachname"], $_POST["mail"],
        $_POST["club"], intval($_POST["dwz"]), intval($_POST["elo"]),
        $_POST["phone"], $_POST["mobile"], $_POST["rolle"], $_POST["passwd"]);

if (isset($_POST['is_active'])) {
    $player->setIsActive(1);
} else {
    $player->setIsActive(0);
}

if (! $player->isPlayerValid()) {
    
    echo 'Die Spielerdaten sind ungültig und können nicht übernommen werden. Bitte wiederholen Sie die  ';
    echo '<a href="player_registration.php">Spielerregistrierung</a>';
    exit();
} else {
    $controller->setPlayer($player);
    $affectedRows = $controller->insertPlayer();
    
    if ($affectedRows == 1) {
        echo 'Die Spielerneuanlage war erfolgreich. Es wurden die folgenden Daten übernommen: ';
        echo "<br />";
        echo "<br />";
        echo "$player";
    } else {
        echo 'Die Neuanlage der Spielerdaten hat nicht funktioniert. Bitte überprüfen Sie die Spielerdaten und wiederholen Sie ggf. die   ';
        echo '<a href="player_registration.php">Spielerregistrierung</a>';
    }
}
?>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>