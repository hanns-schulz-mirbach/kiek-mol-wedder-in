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

require_once ("./model/player.php");
require_once ("./controller/login_controller.php");

session_start();

session_destroy();
$_SESSION = array();

// check that provided player/passwd match with database and get player role
$controller = new LoginController($_POST["mail"], $_POST["passwd"]);

if ($controller->isPlayerValid()) {
    session_start();
    $controller->addPlayerDataToSession();
} else {
    $_SESSION["user_role"] = 'Anonymous';
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
		<h1>Anmeldebestätigung</h1>
<?php
if ($controller->isPlayerValid()) {
    $player = $controller->getPlayer();
    $playerUpdateTargetURL = "player_update.php?id=" . $player->getId();
    echo ("Ihre Anmeldung war erfolgreich. Hier sind Ihre aktuellen Nutzerdaten: <br /> <br /> $player");
    echo ("<br /> <br /> Bitte <a href=" . "$playerUpdateTargetURL" .
             "> aktualisieren</a> Sie Ihre Nutzerdaten, falls die vorstehenden Angaben nicht korrekt sind. <br /> ");
} else {
    echo ("Die eingegebenen Daten sind ungültig. Bitte wiederholen Sie die " .
             "<a href='login.php'>Anmeldung</a> .");
}

?>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>