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
require_once ("./util/formatter.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if (! $accessController->create_Round()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}



$selectName = "round_tournament";
$tournamentSelectionString = $sessionManager->getTournamentSelection(
        $selectName);
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
		<h1>Neuanlage Turnierrunde</h1>
		<form action="round_create_confirmation.php" method="post">
			<table>
			<?php echo ($formatter->getDataTableHeader()); ?>
			<tr>
					<td><label for="round_description">Rundentitel:</label></td>
					<td><input type="text" name="round_description" id="title" required>
					</td>
				</tr>
				<tr>
					<td><label for="round_date">Rundendatum:</label></td>
					<td><input type="text" name="round_date" id="round_date"
						placeholder="TT.MM.JJJJ" required
						pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
						title="Datumseingabe im Format TT.MM.JJJJ" /></td>
				</tr>
				<tr>

					<td><label for="<?php echo ("$selectName");?>"> Turnier:</label></td>
					<td> 
				<?php echo ("$tournamentSelectionString"); ?>
			</td>
				</tr>
			</table>
			<?php echo ($formatter->getSubmitResetControl()); ?>
		</form>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>