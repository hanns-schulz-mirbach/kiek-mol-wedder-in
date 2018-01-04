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
require_once ("./controller/player_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./util/session_manager.php");

session_start();
$sessionManager = new SessionManager();
$userRole = new UserRole();
$userRole->setUserRoleDescription($sessionManager->getUserRole());

$accessController = new AccessController($userRole->getUserRoleDescription());
if (! $accessController->update_Player()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$userId = $sessionManager->getUserId();

if (isset($_GET["id"])) {
    $playerId = intval($_GET["id"]);
    $controller = new PlayerController();
    $player = $controller->getPlayerById($playerId);
} else {
    $player = null;
}

if (isset($_GET["id"])) {
    $playerId = intval($_GET["id"]);
    
    if (($playerId == $userId) || ($userRole->isTournamentLeader())) {
        // the user wants to update her own player data or is in the role
        // tournamnet leader or admin. In this case the update is permitted
        $controller = new PlayerController();
        $player = $controller->getPlayerById($playerId);
    } else {
        // a player id has been provided in the url but it is diffrent from the
        // id of the logged in user and the user has no tournament leader or
        // admin rights. In this case the update is not permitted
        
        $player = null;
    }
} else {
    
    // no id has been provided in the url. No
    // update is permitted
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
			<h1>Ergebnis Änderung Spielerdaten</h1>
			
						<?php
    if (isset($player)) {
        
        $playerUpdate = new Player($_POST["vorname"], $_POST["nachname"],
                $_POST["mail"], $_POST["club"], intval($_POST["dwz"]),
                intval($_POST["elo"]), $_POST["phone"], $_POST["mobile"],
                $_POST["rolle"], $_POST["passwd"]);
        
        if (isset($_POST['is_active'])) {
            $playerUpdate->setIsActive(1);
        } else {
            $playerUpdate->setIsActive(0);
        }
        
        $playerUpdate->setId($player->getId());
        
        if (! $playerUpdate->isPlayerValid()) {
            
            echo 'Die Spielerdaten sind ungültig und können nicht übernommen werden. Bitte wiederholen Sie die  ';
            echo '<a href="player_update.php">Änderung der Spielerdaten</a>';
            exit();
        } else {
            $controller->setPlayer($playerUpdate);
            
            $affectedRows = $controller->updatePlayer();
            
            if ($affectedRows == 1) {
                echo 'Die Spielerdaten wurden erfolgreich geändert.';
                echo "<br />";
                echo ("Ursprünglicher Datensatz: ");
                echo "<br />";
                echo "$player";
                echo "<br />";
                echo "<br />";
                echo 'Geänderter Datensatz: ';
                echo "<br />";
                echo "$playerUpdate";
            } else {
                echo ('Die Änderung der Spielerdaten hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. den ');
                echo ('<a href="player_update.php">Vorgang</a> .');
            }
        }
    } else {
        echo ('Sie haben keine Berechtigung zur Änderung der Spielerdaten');
    }
    ?>
		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>