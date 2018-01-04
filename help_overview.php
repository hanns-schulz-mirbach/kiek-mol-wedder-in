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
if (! $accessController->access_HelpOverview()) {
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
			<h1>Allgemeine Bedienungsanleitung</h1>
			Das Progamm zur Verwaltung des <a target="_blank"
				href="tournament_description.php">Kiek mol wedder in Turniers</a>
			bietet den folgenden Funktionsumfang zur Unterstützung der
			Turnierleitung und der Turnierteilnehmer:
			<ul>
				<li>Erfassung und Pflege von Stammdaten für das Turnier, die
					Turnierrunden und die Spieler im Turnier</li>
				<li>Planung der Partien der Turnierunden mit der Möglichkeit für die
					Spieler Wunschpaarungen anzugeben</li>
				<li>Erfassung der Partieergebnisse</li>
				<li>Erstellung und Publikation von Berichten zum Turnier</li>
				<li>Automatische Erstellung der Turniertabelle incl. <a
					target="_blank"
					href="https://de.wikipedia.org/wiki/Feinwertung#Wertung_nach_Sonneborn-Berger">Feinwertung
						nach Sonneborn-Berger</a> und Ermittlung der durchschnittlichen <a
					target="_blank"
					href="https://de.wikipedia.org/wiki/Deutsche_Wertungszahl">DWZ-Wertung</a>
					der Gegner
				</li>
				<li>Zusammenfassende Übersicht zu allen abgeschlossenen Turnieren</li>
			</ul>

			Die Web Anwendung kann zum rein lesenden Zugriff auf die Daten direkt
			genutzt werden. Um Daten zu ändern oder neu zu erfassen ist ein
			personalisierter Nutzerzugang erforderlich. Ein solcher Zugang kann
			beantragt werden durch eine e-mail an an <a
				href="mailto:kiekmolwedderin@hsk1830.de">kiekmolwedderin@hsk1830.de</a>.
			Das folgende Bild zeigt den Startbildschirm ohne personalisierten
			Nutzerzugang (anonyme Nutzung). <br /> <br /> <img
				alt="Startbildschirm für anonymen Zugang"
				src="image/kmw_start_page_anonymous_small.PNG" /><br /> <br /> In
			der Navigationsleiste werden die folgenden Links angeboten: <br />

			<table>
				<tr>
					<th>Link</th>
					<th>Beschreibung</th>
				</tr>
				<tr>
					<td><a href="login.php">Anmeldung</a></td>
					<td>Anmeldung mit einem personalisierten NUtzerzugang. Dafür muß
						ein Nutzername und ein Passwort eingegeben werden</td>
				</tr>
				<tr>
					<td><a href="tournament_description.php">Ausschreibung</a></td>
					<td>Aktuelle Turnierausschreibung</td>
				</tr>
				<tr>
					<td><a href="help_overview.php">Hilfe</a></td>
					<td>Online Hilfe zu der Kiek mol wedder in Web Anwendung</td>
				</tr>
				<tr>
					<td><a href="index.php">Startseite</a></td>
					<td>Startseite der Kiek mol wedder in Web Anwendung</td>
				</tr>

			</table>

			<br /> <br /> In dem linken Seitenbereich werden die folgenden Links
			zum Zugriff auf Daten des aktuellen Turniers angeboten: <br />

			<table>
				<tr>
					<th>Link</th>
					<th>Beschreibung</th>
				</tr>
				<tr>
					<td><a href='round_overview_current_tournament.php'>Rundenplan</a></td>
					<td>Übersicht zu den Terminen der Runden des aktuellen Turniers</td>
				</tr>
				<tr>
					<td><a href='schedule_overview_per_round.php'>Partiewünsche nach
							Runden</a></td>
					<td>Partiewünsche der Spieler der aktuellen Turniers gegliedert
						nach den Turnierrunden</td>
				</tr>
				<tr>
					<td><a href='game_overview_per_round.php'>Rundenergebnisse</a> <br /></td>
					<td>Partieergebnisse des aktuellen Turniers gegliedert nach den
						Turnierrunden. Ein Partieergebnis von "Unbekannt" zeigt an, daß
						diese Partie geplant aber noch nicht durchgeführt ist. Dies gibt
						der Turnierleitung die Möglichkeit Partiepaarungen vor den
						jeweiligen Runden festzusetzen und zu publizieren. Die Spieler
						können sich so vor jeder Runde über die angesetzten Paarungen
						informieren</td>
				</tr>
				<tr>
					<td><a href='report_overview_public.php'>Berichte</a></td>
					<td>Berichte zum aktuellen Turnier</td>
				</tr>
				<tr>
					<td><a href='ranking_overview.php'>Tabelle</a></td>
					<td>Die Tabelle des aktuellen Turniers. Dort sind alle
						abgeschlossenen Partien berücksichtigt. Es gibt für jeden
						Partiegewinn drei Punkte, für jedes Remis zwei Punkte und für
						jeden Verlust einen Punkt. Bei kampflosen Partien erhält der
						Gewinner drei Punkte und der Verlierer keinen Punkt. Die
						Feinwertung wird nach dem Verfahren von <a target="_blank"
						href="https://de.wikipedia.org/wiki/Feinwertung#Wertung_nach_Sonneborn-Berger">Sonneborn-Berger</a>
						berechnet. Bei gleicher Feinwertung wird der DWZ-Schnitt der
						Gegner für die Ermittlung der Rangfolge herangezogen. Weiterhin
						enthält die Tabelle Angaben zu der gespielten Anzahl von Weiß- und
						Schwarzpartien sowie zur Anzahl der Gewinne, Remis, Verluste und
						den kampflosen Partien
					</td>
				</tr>
				<tr>
					<td><a href='tournament_overview_all_finalized.php'>Abgeschlossene
							Turniere</a></td>
					<td>Überblick über alle abgeschlosenen Turniere. In diesem
						Abschnitt werden erstmalig ab September 2018 Daten verfügbar sein.
						Dann wird das Kiek mol wedder in Turnier 2018 abgeschlossen sein</td>
				</tr>

			</table>

			<br /> <br /> <br />
			 
			 <?php
    
    if ($accessController->access_HelpPlayer()) {
        echo ("Weitergehende Informationen zum Funktionsumfang der Kiek mol wedder in Web Anwendung finden sich unter finden sich unter");
        echo ("<ul>");
        echo ("<li> <a href='help_player.php'>Bedienungsanleitung für Spieler</a> </li>");
        if ($accessController->access_HelpTournamentLeader()) {
            echo ("<li> <a href='help_tournament_leader.php'>Bedienungsanleitung für Turnierleiter</a> </li>");
        }
        if ($accessController->access_HelpAdministrator()) {
            echo ("<li> <a href='help_administrator.php'>Bedienungsanleitung für Administratoren</a> </li>");
        }
        echo ("</ul>");
    }
    
    ?>

		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>