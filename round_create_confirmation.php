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
require_once ("./controller/access_controller.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if (! $accessController->create_Round()) {
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

			<h1>Ergebnis Neuanlage Turnierrunde</h1>
			<?php
require_once ("./model/round.php");
require_once ("./model/tournament.php");
require_once ("./controller/round_controller.php");

$round = new Round();
$round->setRoundDescription($_POST['round_description']);
$round->setRoundDate($_POST['round_date']);
$tournament = new Tournament();
$tournament->setId(intval($_POST['round_tournament']));
$round->setTournament($tournament);

if (! $round->isRoundValid()) {
    
    echo 'Die Daten der Turnierrunde sind ungültig und können nicht übernommen werden. Bitte wiederholen Sie die  ';
    echo '<a href="round_create.php">Rundenneuanlage</a>';
    exit();
} else {
    $controller = new RoundController();
    $controller->setRound($round);
    $affectedRows = $controller->insertRound();
    
    if ($affectedRows == 1) {
        echo 'Die Neuanlage der Turnierrunde war erfolgreich. Es wurden die folgenden Daten übernommen: ';
        echo "<br />";
        echo "<br />";
        echo "$round";

    } else {
        echo 'Die Neuanlage der Turnierrunde hat nicht funktioniert. Bitte überprüfen Sie die Rundendaten und wiederholen Sie ggf. die   ';
        echo '<a href="round_create.php">Rundenneuanlage</a>';
    }
}
?>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>