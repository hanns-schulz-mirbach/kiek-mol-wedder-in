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

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

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
		<?php require_once ("./template/sidebar.php"); ?>
		<main class="central-display-area">
		<h1>Anmeldung</h1>
		<form action="login_confirmation.php" method="post">
			<table>
		<?php echo ($formatter->getDataTableHeader()); ?>
			<tr>
					<td><label for="mail">E-Mail:</label></td>
					<td><input type="email" name="mail" id="mail"
						placeholder="Ihre E-Mail-Adresse" required></td>
				</tr>

				<tr>
					<td><label for="passwd">Passwort:</label></td>
					<td><input type="password" name="passwd" id="passwd"></td>
				</tr>
			</table>
			<?php echo ($formatter->getSubmitResetControl());?>
		</form>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>