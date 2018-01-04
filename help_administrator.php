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

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);
if (! $accessController->access_HelpTournamentLeader()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
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
			<h1>Bedienungsanleitung für Administratoren</h1>
			In dieser Bedieungsanleitung für Administratoren des Kiek mol wedder
			in Turniers werden Funktionen der Web Anwendung beschrieben, die über
			die in der <a href='help_overview.php'>Allgemeinen
				Bedienungsanleitung</a> und der <a href='help_player.php'>Bedienungsanleitung
				für Spieler</a> sowie der <a href='help_tournament_leader.php'>Bedienungsanleitung
				für Turnierleiter</a> beschriebenen hinausgehen. Voraussetzung für
			die Nutzung dieser erweiterten Funktionalität ist ein
			personalisiertes Nutzerkonto mit Administratorberechtigungen, das
			durch eine e-mail an <a href="mailto:kiekmolwedderin@hsk1830.de">kiekmolwedderin@hsk1830.de</a>
			beantragt werden kann. Das folgende Bild zeigt den Startbildschirm
			nachdem sich der Anwender mit einem Administratorzugang am System
			angemeldet hat (die Anmeldung erfolgt über den Link <a
				href="login.php">Anmeldung</a> links in der Navigationsleiste). <br />
			<br /> <img
				alt="Startbildschirm für Administratoren des Kiek mol wedder in Turniers"
				src="image/kmw_start_page_administrator_small.PNG" /><br /> <br />
			Im rechten Teil der Navigationsleiste ist der Login Name des Nutzers
			(in der Regel die e-mail Adresse) sowie die Rolle (in diesem Beispiel
			Rolle: Administrator) zu sehen. <br /> <br /> In dem linken
			Seitenbereich wird zusätzlich zu den in der <a
				href='help_overview.php'>Allgemeinen Bedienungsanleitung</a> und der
			<a href='help_player.php'>Bedienungsanleitung für Spieler</a> sowie
			der <a href='help_tournament_leader.php'>Bedienungsanleitung für
				Turnierleiter</a> beschriebenen Optionen der Link <a
				href='php_info.php'>PHP Infor</a> angeboten. Damit besteht die
			Möglichkeit detaillierte Information zur <a
				href="https://de.wikipedia.org/wiki/PHP">PHP</a> Umgebung der
			Installation zu bekommen. <br /> <br /> <img alt="PHP Info"
				src="image/php_info.PNG" /><br /> <br /> Diese Informationen werden
			in der Regel für den Betrieb der Kiek mol wedder in Anwendung nicht
			benötigt. Im Fall von technischen Problemen kann diese Information
			für den Administrator wesentlich sein. <br /> <br />

			<h3>Stammdateneingabe nach der Erstinstallation</h3>
			Nach der Erstinstallation und der Erzeugung des initialen
			Administrator Benuzterkontos müssen die grundlegenden Stammdaten
			erfasst werden. Dazu wird wie folgt vorgegangen
			<ol>
				<li>Eingabe der Benutzerkonten über <a
					href='player_overview_all.php'>Alle Spieler</a>. Es sollten
					frühzeitig Konten für die Rolle Turnierleiter und Spieler vergeben
					werden. Über diese Konten sollte die weitere Dateneingabe abgewickelt
					werden. Weiterhin sollte zumindest ein weiterer Administrator
					Zugang angelegt werden
				</li>
				<li>Einrichtiung eines aktiven Turniers (keinen Haken bei "Turnier
					abgeschlossen" setzen) über <a href='tournament_overview_all.php'>Alle
						Turniere</a>
				</li>
				<li>Eingabe der Runden für das neue Turnier über <a
					href='round_overview_all.php'>Alle Turnierrunden</a></li>
			</ol>
			Nach diesen Schritten ist die Basiskonfiguration abgeschlossen und
			die Spieler können ihre Wunschpartien erfassen und der Turnierleiter
			kann die Partien der Runden planen.

			<ul>
				<li><a href='help_overview.php'>Allgemeine Bedienungsanleitung</a></li>
				<li><a href='help_player.php'>Bedienungsanleitung für Spieler</a></li>
				<li><a href='help_tournament_leader.php'>Bedienungsanleitung für
						Turnierleiter</a></li>

			</ul>


		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>