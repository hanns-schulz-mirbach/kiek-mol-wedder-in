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
require_once ("./util/user_role.php");
require_once ("./util/formatter.php");

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
    
    if (($playerId == $userId) || ($userRole->isTournamentLeader())) {
        // the user wants to update her own player data or is in the role
        // tournamnet leader or admin. In this case the update is permitted
        $controller = new PlayerController();
        $player = $controller->getPlayerById($playerId);
        $updateConfirmationURL = "player_update_confirmation.php?id=" .
                 "$playerId";
    } else {
        // a player id has been provided in the url but it is different from the
        // id of the logged in user and the user has no tournament leader or
        // admin rights. In this case the update is not permitted
        
        $player = null;
        $updateConfirmationURL = '';
    }
} else {
    
    if ($sessionManager->isUserIdSet()) {
        // no id has been provided in the url. In case that user is logged in
        // she
        // can change her own data
        $controller = new PlayerController();
        $player = $controller->getPlayerById($userId);
        $updateConfirmationURL = "player_update_confirmation.php?id=" . "$userId";
    } else {
        // no id has been provided in the url and the user is not logged in. No
        // update is permitted
        $player = null;
        $updateConfirmationURL = '';
    }
}

if (isset($player)) {
    $formatter = new Formatter();
    $playerUpdateForm = $formatter->getPlayerUpdateForm($player, $userRole);
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
			<h1>Aktualisierung Spielerdaten</h1>
			
			<?php
if (isset($player)) {
    echo ("<form action='" . $updateConfirmationURL . "' method='post'>");
    echo ("$playerUpdateForm");
    echo ($formatter->getSubmitResetControl());
    echo ("</form>");
} else {
    echo ("Es sind keine Spielerdaten zum Update vorhanden oder Sie haben keine Berechtigung für diesen Vorgang. Falls Sie einen Benutzerzugang haben, melden Sie sich bitte nochmals am System an.");
}

?>

		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>