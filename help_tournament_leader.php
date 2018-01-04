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
			<h1>Bedienungsanleitung für Turnierleiter</h1>
			In dieser Bedieungsanleitung für Turnierleiter des Kiek mol wedder in
			Turniers werden Funktionen der Web Anwendung beschrieben, die über
			die in der <a href='help_overview.php'>Allgemeinen
				Bedienungsanleitung</a> und der <a href='help_player.php'>Bedienungsanleitung
				für Spieler</a> beschriebenen hinausgehen. Voraussetzung für die
			Nutzung dieser erweiterten Funktionalität ist ein personalisiertes
			Nutzerkonto mit Turnierleiterberechtigungen, das durch eine e-mail an
			<a href="mailto:kiekmolwedderin@hsk1830.de">kiekmolwedderin@hsk1830.de</a>
			beantragt werden kann. Das folgende Bild zeigt den Startbildschirm
			nachdem sich der Anwender mit einem Turnierleiterzugang am System
			angemeldet hat (die Anmeldung erfolgt über den Link <a
				href="login.php">Anmeldung</a> links in der Navigationsleiste). <br />
			<br /> <img
				alt="Startbildschirm für Turnierleiter des Kiek mol wedder in Turniers"
				src="image/kmw_start_page_tournament_leader_small.PNG" /><br /> <br />
			Im rechten Teil der Navigationsleiste ist der Login Name des Nutzers
			(in der Regel die e-mail Adresse) sowie die Rolle (in diesem Beispiel
			Rolle: Turnierleiter) zu sehen. <br /> <br /> In dem linken
			Seitenbereich werden zusätzlich zu den in der <a
				href='help_overview.php'>Allgemeinen Bedienungsanleitung</a> und der
			<a href='help_player.php'>Bedienungsanleitung für Spieler</a>
			beschriebenen Optionen die folgenden Links angeboten, um auf die
			Daten des aktuellen Turniers zuzugreifen: <br /> <br /> <br />

			<table>
				<tr>
					<th>Link</th>
					<th>Beschreibung</th>
				</tr>
				<tr>
					<td><a href='round_overview_all_current_tournament.php'>Alle
							Turnierrunden</a></td>
					<td>Die Daten zu allen Runden des aktuellen Turniers. Aus dieser
						Übersicht kann direkt zu den Dialogen zur Änderung der Rundendaten
						abgesprungen werden</td>
				</tr>
				<tr>
					<td><a href='schedule_overview_all_current_tournament.php'>Partiewünsche</a></td>
					<td>Übersicht zu allen Partiewünschen aller Spielers für das
						aktuelle Turnier. Pro Turnierrunde und Spieler kann ein
						Wunschgegner und eine Wunschfarbe eingegeben werden. Die
						Turnierleitung wird nach Möglichkeit versuchen, die erfassten
						Partiewünsche in den Ansetzungen der Runden zu berücksichtigen. Es
						gibt aber keine Garantie, daß jeder Partiewunsch auch umgesetzt
						werden kann. Aus dieser Übersicht kann direkt zu den Dialogen zur
						Änderung der Wunschpartien abgesprungen werden</td>
				</tr>
				<tr>
					<td><a href='game_overview_all_current_tournament.php'>Alle
							Partieergebnisse</a></td>
					<td>Partieergebnisse aller Spieler im aktuellen Turnier. Aus dieser
						Übersicht kann direkt zu den Dialogen zur Änderung der
						Partieergebnisse abgesprungen werden. Über diesen Weg wird der
						Turnierleiter in der Regel die Partieergebnisse nach jeder Runde
						erfassen. Geplante Partien werden ebenfalls über diesen Weg
						erfasst; dort wird dann das Ergebnis "Unbekannt" zugeordnet</td>
				</tr>
				<tr>
					<td><a href='report_overview_all_current_tournament.php'>Alle
							Berichte</a></td>
					<td>Übersicht zu allen Berichten, deren Publikationsdatum nach dem
						Startdatum des aktuellen Turniers liegt. Aus dieser Übersicht kann
						direkt zu den Dialogen zur Änderung der Berichte abgesprungen
						werden.</td>
				</tr>
			</table>
			<br /> <br /> Die folgenden Links bieten jeweils Zugang zu allen
			Daten, die in den bereits abgeschlossenen und im laufenden Turnier
			erfasst wurden. <br /> <br />
			<table>
				<tr>
					<th>Link</th>
					<th>Beschreibung</th>
				</tr>
				<tr>
					<td><a href='player_overview_all.php'>Alle Spieler</a></td>
					<td>Die Daten aller registrierten Spieler. Dies ist einzige Weg, um
						deaktivierte Spielerkonten erneut zu aktivieren. Aus dieser
						Übersicht kann direkt zu den Dialogen zu Änderung und Neuanlage
						von Spielerdaten abgesprungen werden</td>
				</tr>
				<tr>
					<td><a href='tournament_overview_all.php'>Alle Turniere</a></td>
					<td>Alle bisher im System erfassten Turniere. Zu jedem Zeitpunkt
						darf maximal eines dieser Turniere im aktiven Zustand sein. Alle
						anderen Turniere müssen als abgeschlossen gekennzeichnet sein. Aus
						dieser Übersicht kann direkt zu den Dialogen zu Änderung und
						Neuanlage von Turnierdaten abgesprungen werden</td>
				</tr>

				<tr>
					<td><a href='round_overview_all.php'>Alle Turnierrunden</a></td>
					<td>Die Daten zu allen Runden aller im System erfassten Turniere.
						Aus dieser Übersicht kann direkt zu den Dialogen zu Änderung und
						Neuanlage von Rundendaten abgesprungen werden</td>
				</tr>
				<tr>
					<td><a href='schedule_overview_all.php'>Alle Partiewünsche</a></td>
					<td>Übersicht zu allen Partiewünschen aller Spielers für alle im
						System erfassten Turniere. Aus dieser Übersicht kann direkt zu den
						Dialogen zu Änderung und Neuanlage von Partiewunschdaten
						abgesprungen werden</td>
				</tr>
				<tr>
					<td><a href='game_overview_all.php'>Alle Partieergebnisse</a></td>
					<td>Partieergebnisse aller Spieler aus allen im System erfassten
						Turnieren. Aus dieser Übersicht kann direkt zu den Dialogen zu
						Änderung und Neuanlage von Partieergebnisdaten abgesprungen werden
					</td>
				</tr>
				<tr>
					<td><a href='report_overview_all.php'>Alle Berichte</a></td>
					<td>Übersicht zu allen Berichten, die im System erfasst sind. Aus
						dieser Übersicht kann direkt zu den Dialogen zu Änderung und
						Neuanlage von Berichtsdaten abgesprungen werden</td>
				</tr>
			</table>
			<br /> <br /> Die Nutzung der vorgenannten Dialoge wurde im
			wesentlichen schon in der <a href='help_player.php'>Bedienungsanleitung
				für Spieler</a> beschrieben. In den folgenden Abschnitten soll daher
			lediglich auf einige besondere Nutzzungsszenarien eingegangen werden,
			die für Turnierleiter von besonderem Interesse sind.

			<h3>Aktivierung eines inaktiven Spielerkontos</h3>
			Um ein inaktives Spielerkonto zu aktivierem muß zunächst die
			Übersicht <a href='player_overview_all.php'>Alle Spieler</a> geöffnet
			werden <br /> <br /> <img alt="Spieleraktivierung"
				src="image/kmw_player_overview.PNG" /><br /> <br /> Aus der
			vorstehend gezeigten Übersicht sieht man, daß das Konto des Spielers
			Volker Gast deaktiviert ist. Durch Klick auf dem Link in der Spalte
			Id (Wert 3) wird der Dialog zum Ändern der Speilerdaten geöffnet. <br />
			<br /> <img alt="Spieleraktivierung"
				src="image/kmw_activate_player.PNG" /><br /> <br /> Durch Setzen des
			Hakens bei "Nimmt am aktuellen Turnier teil" wird das Spielerkonto
			wieder aktiviert und kann im aktuellen Turnier genutzt werden.

			<h3>Planpartien für zukünftige Runden erfassen</h3>
			Zunächst wird die Ergebnisübersicht Aktuelles Turnier: <a
				href='game_overview_all_current_tournament.php'>Alle
				Partieergebnisse</a> geöffnet. <br /> <br /> <img
				alt="Übersicht aller Partieergebnisse"
				src="image/kmw_game_overview_all_current_tournament.PNG" /><br /> <br />

			Durch einen Klick auf <a href='game_create.php'>Neues Partieergebnis</a>
			wird der Dialog zur Neuerfassung eines Partieergebnisses geöffnet. <br />
			<br /> <img alt="Erfassung Planpartie"
				src="image/kmw_new_game_planned.PNG" /><br /> <br /> Das
			Partieergebnis wird auf "Unbekannt" gesetzt. Damit ist die Partie als
			zukünftige Planpartie gekennzeichnet und wird in den personalisierten
			Partieübersichten der beteiligten Spieler sichtbar. 
			
			
			 <?php
    
    if ($accessController->access_HelpAdministrator()) {
        echo ("Weitergehende Informationen zum Funktionsumfang der Kiek mol wedder in Web Anwendung finden sich unter finden sich unter");
        echo ("<ul>");
        echo ("<li> <a href='help_administrator.php'>Bedienungsanleitung für Administratoren</a> </li>");
        echo ("</ul>");
    }
    
    ?>

			<br /> <br /> <br />

			<ul>
				<li><a href='help_overview.php'>Allgemeine Bedienungsanleitung</a></li>
				<li><a href='help_player.php'>Bedienungsanleitung für Spieler</a></li>
			</ul>


		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>