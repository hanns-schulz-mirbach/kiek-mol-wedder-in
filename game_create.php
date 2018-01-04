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
require_once ("./util/formatter.php");
require_once ("./controller/game_controller.php");
require_once ("./controller/access_controller.php");
session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);
if (! $accessController->create_Game()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$controller = new GameController();
$roundSelectName = "round";
$roundSelection = $controller->getRoundSelection($roundSelectName);
$playerWhiteSelectName = "playerWhite";
$playerWhiteSelection = $controller->getPlayerSelection($playerWhiteSelectName,
        $sessionManager->getUserId());
$playerBlackSelectName = "playerBlack";
$playerBlackSelection = $controller->getPlayerSelection($playerBlackSelectName,
        $sessionManager->getUserId());
$resultSelectName = "result";
$resultValue = 0; // 0 = result unknown
$resultSelection = $controller->getResultSelection($resultSelectName,
        $resultValue);
$gameDate = date("d.m.Y");
$formatter = new Formatter();

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
		<?php include ("./template/sidebar.php"); ?>
		<main class="central-display-area">
		<h1>Neuanlage Partieergebnis</h1>
		<form action="game_create_confirmation.php" method="post">
			<table>
				<?php echo ($formatter->getDataTableHeader()); ?>
				<tr>
					<td><label for="round">Runde:</label></td>
					<td> 
				<?php echo("$roundSelection"); ?>
			</td>
				</tr>
				<tr>
					<td><label for="playerWhite">Spieler Weiß:</label></td>
					<td>
				<?php echo("$playerWhiteSelection"); ?>
			</td>
				</tr>
				<tr>
					<td><label for="playerBlack">Spieler Schwarz:</label></td>
					<td>
				<?php echo("$playerBlackSelection"); ?>
			</td>
				</tr>
				<tr>
					<td><label for="gameDate">Partiedatum:</label></td>
					<td><input type="text" name="gameDate" id="gameDate"
						placeholder="TT.MM.JJJJ" required
						pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
						title="Datumseingabe im Format TT.MM.JJJJ"
						value=<?php echo ("'" . "$gameDate" . "'"); ?> /></td>
				</tr>
				<tr>

					<td><label for="result">Partieergebnis:</label></td>
					<td>
				<?php echo("$resultSelection"); ?>
				</td>
				</tr>
			</table>

											<?php
        echo ($formatter->getSubmitResetControl());
        ?>
		</form>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>