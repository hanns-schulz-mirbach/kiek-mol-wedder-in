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
require_once ("./controller/access_controller.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);
if (! $accessController->create_Player()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

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
		<h1>Neuanlage Spieler</h1>
		<form action="player_create_confirmation.php" method="post">
			<table>
			<?php echo ($formatter->getDataTableHeader()); ?>
				<tr>
					<td><label for="nachname">Nachname:</label></td>
					<td><input type="text" name="nachname" id="nachname"
						placeholder="Ihr Nachname" required></td>
				</tr>
				<tr>
					<td><label for="vorname">Vorname:</label></td>
					<td><input type="text" name="vorname" id="vorname"
						placeholder="Ihr Vorname"></td>
				</tr>
				<tr>
					<td><label for="mail">E-Mail:</label></td>
					<td><input type="email" name="mail" id="mail"
						placeholder="Ihre E-Mail-Adresse" required></td>
				</tr>
				<tr>
					<td><label for="club">Verein:</label></td>
					<td><input type="text" name="club" id="club"
						value="Hamburger Schachklub von 1830 e.V."></td>
				</tr>
				<tr>
					<td><label for="dwz">DWZ:</label></td>
					<td><input type="number" name="dwz" id="dwz" min="0" max="2900"
						value="0"></td>
				</tr>
				<tr>
					<td><label for="elo">ELO:</label></td>
					<td><input type="number" name="elo" id="elo" min="0" max="2900"
						value="0"></td>
				</tr>
				<tr>
					<td><label for="phone">Festnetznummer:</label></td>
					<td><input type="text" name="phone" id="phone"
						placeholder="Ihre Festnetznummer (tagsüber)"></td>
				</tr>
				<tr>
					<td><label for="mobile">Mobilnummer:</label></td>
					<td><input type="text" name="mobile" id="mobile"
						placeholder="Ihre Mobilnummer"></td>
				</tr>
				<tr>
					<td><label for="rolle">Rolle:</label></td>
					<td><select name="rolle">
							<option value="Gast" selected>Gast</option>
							<option value="Spieler">Spieler</option>
							<option value="Turnierleiter">Turnierleiter</option>
							<option value="Administrator">Administrator</option>
					</select></td>
				</tr>
								<tr>
					<td><label for="is_active">Nimmt am aktuellen Turnier teil:</label></td>
					<td><input type="checkbox" name="is_active" id="is_active" checked></td>
				</tr>

				<tr>
					<td><label for="passwd">Passwort:</label></td>
					<td><input type="password" name="passwd" id="passwd"></td>
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