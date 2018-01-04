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
if (! $accessController->access_HelpPlayer()) {
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
			<h1>Bedienungsanleitung für Spieler</h1>
			In dieser Bedieungsanleitung für Spieler des Kiek mol wedder in
			Turniers werden Funktionen der Web Anwendung beschrieben, die über
			die in der <a href='help_overview.php'>Allgemeinen
				Bedienungsanleitung</a> beschriebenen hinausgehen. Voraussetzung für
			die Nutzung dieser erweiterten Funktionalität ist ein
			personalisiertes Nutzerkonto mit Spielerberechtigungen, das durch
			eine e-mail an <a href="mailto:kiekmolwedderin@hsk1830.de">kiekmolwedderin@hsk1830.de</a>
			beantragt werden kann. Das folgende Bild zeigt den Startbildschirm
			nachdem sich der Anwender mit einem Spielerzugang am System
			angemeldet hat (die Anmeldung erfolgt über den Link <a
				href="login.php">Anmeldung</a> links in der Navigationsleiste). <br />
			<br /> <img
				alt="Startbildschirm für Spieler des Kiek mol wedder in Turniers"
				src="image/kmw_start_page_player_small.PNG" /><br /> <br /> Im
			rechten Teil der Navigationsleiste ist der Login Name des Nutzers (in
			der Regel die e-mail Adresse) sowie die Rolle (in diesem Beispiel
			Rolle: Spieler) zu sehen. <br /> <br /> In dem linken Seitenbereich
			werden zusätzlich zu den in der <a href='help_overview.php'>Allgemeinen
				Bedienungsanleitung</a> beschriebenen Optionen die folgenden Links
			angeboten: <br />

			<table>
				<tr>
					<th>Link</th>
					<th>Beschreibung</th>
				</tr>
				<tr>
					<td><a href='player_update.php'>Meine Spielerdaten</a></td>
					<td>Die Daten des Spielers. Dies umfasst neben dem Namen
						Kontaktinformationen wie Telefon und e-mail sowie die
						Vereinszugehörigkeit und die DWZ und ELO Zahlen. Die e-mail
						Adresse ist ein Pflichtfeld und wird in der Applikation (u.a. beim
						Login) als Nutzername verwendet</td>
				</tr>
				<tr>
					<td><a href='schedule_overview_player.php'>Meine Partiewünsche</a></td>
					<td>Partiewünsche des Spielers für das aktuelle Turnier. Pro
						Turnierrunde kann ein Wunschgegner und eine Wunschfarbe eingegeben
						werden. Die Turnierleitung wird nach Möglichkeit versuchen, die
						erfassten Partiewünsche in den Ansetzungen der Runden zu
						berücksichtigen. Es gibt aber keine Garantie, daß jeder
						Partiewunsch auch umgesetzt werden kann</td>
				</tr>
				<tr>
					<td><a href='game_overview_player.php'>Meine Ergebnisse</a></td>
					<td>Partieergebnisse des Spielers im aktuellen Turnier. Hier kann
						jeder Spieler für die eigenen Partien seine Partieergebnisse
						selber erfassen. In der Regel wird die Ergebniserfassung nach
						jeder Runde durch die Turnierleitung umgesetzt</td>
				</tr>
				<tr>
					<td><a href='report_overview_player.php'>Meine Berichte</a></td>
					<td>Übersicht zu allen Berichten, die der Spieler selber verfasst
						hat. Dies umfasst auch Berichte aus abgeschlossenen Turnieren. Der
						Spieler kann auch neue Berichte erstellen</td>
				</tr>
			</table>



			<h3>Spielerdaten</h3>
			Unter dem Link <a href='player_update.php'>Meine Spielerdaten</a>
			kann jeder Spieler seine eigenen Spielerdaten pflegen. Die
			Erfassungsmaske sieht wie folgt aus <br /> <br /> <img
				alt="Spielerdatenerfassung" src="image/kmw_player_screen.PNG" /><br />
			<br /> Die unter e-mail gemachte Angabe wird als Nutzername verwendet
			und muß neben dem Passwort bei der Anmeldung angegeben werden. Unter
			"Nimmt am aktuellen Turnier teil" sollte der Haken gesetzt werden,
			falls der Spieler beabsichtigt am aktuellen Turnier teilzunehmen. Ist
			dieser Haken nicht gesetzt, kann der Spieler weder für die Planung
			der Partiepaarungen noch für die Erfassung von Partieergebnissen
			ausgewählt werden.

			<h3>Partiewünsche</h3>
			Jeder Spieler kann seine Wünsche für zukünftige Partien unter dem
			Link <a href='schedule_overview_player.php'>Meine Partiewünsche</a>
			erfassen.Die Erfassungsmaske sieht wie folgt aus <br /> <br /> <img
				alt="Partiewünsche" src="image/kmw_schedule_screen.PNG" /><br /> <br />
			Unter "Teilnahme" sollte jeder Spieler auswählen, ob er an der
			jeweiligen Runde teilnimmt. Auch die Nichtteilnahme sollte durch
			Auswahl von "Nein" erfasst werden, um der Turnierleitung die
			Rundenplanung zu erleichtern. Bei "Wunschgegner" und "Wunschfarbe"
			kann die Vorauswahl "Unbekannt" übernommen werden, falls der Spieler
			hierzu keine Präferenzen hat.

			<h3>Angesetzte Partien für die nächste Runde</h3>
			Unter dem Link <a href='game_overview_player.php'>Meine Ergebnisse</a>
			bekommt jeder Spieler seine persönliche Gesamtübersicht über seine
			Partieergebnisse sowie über geplante zukünftige Partien. Diese
			Übersichtsdarstellung sieht wie folgt aus <br /> <br /> <img
				alt="Angesetzte Partien" src="image/kmw_game_planned.PNG" /><br /> <br />
			Ein Ergebnis von "Unbekannt" zeigt eine für einen zukünftigen Termin
			verbindlich angesetzte Partie an. Im Gegensatz zu einem Partiewunsch
			(die Übersicht seiner Partiewünsche findet jeder Spieler unter <a
				href='schedule_overview_player.php'>Meine Partiewünsche</a>) ist
			eine solche Ansetzung durch die Turnierleitung bestätigt. Jedes
			Partieergebnis kann durch den Spieler durck Klicken des Links in der
			Spalte "Id" aktualisiert werden. Durch Klicken des Links <a
				href='schedule_overview_player.php'>Neues Partieergebnis</a> kann
			jeder Spieler seine neuen Partieergebnisse incl. verbindlich
			geplanter zukünftiger Partien selber erfassen.

			<h3>Erfassung Partieergebnisse</h3>
			Die Maske zur Erfassung einer Partieergebnisse sieht wie folgt aus <br />
			<br /> <img alt="Partieergebnisse" src="image/kmw_game_screen.PNG" /><br />
			<br /> Unter Partieergebnis stehen die folgenden Optionen zur
			Verfügung
			<ul>
				<li>Unbekannt: die Partie ist verbindlich für einen zukünftigen
					Zeitpunkt geplant</li>
				<li>1/2 - 1/2 Remis</li>
				<li>0 - 1 der Spieler mit Schwarz hat die Partie gewonnen</li>
				<li>1 - 0 der Spieler mit Weiß hat die Partie gewonnen</li>
				<li>- + der Spieler mit Schwarz hat die Partie kampflos gewonnen (da
					der mit Weiß geplante Spieler nicht rechtzeitig zu der Partie
					gekommen ist)</li>
				<li>+ - der Spieler mit Weiß hat die Partie kampflos gewonnen (da
					der mit Schwarz geplante Spieler nicht rechtzeitig zu der Partie
					gekommen ist)</li>

			</ul>

			<h3>Übersicht Berichte</h3>
			Unter dem Link <a href='report_overview_player.php'>Meine Berichte</a>
			findet jeder Spieler eine Übersicht zu allen von ihm verfassten
			Berichten. Dies sind nicht nur Berichte zum aktuellen Turnier,
			sondern auch Berichte ohne Turnierbezug oder Berichte zu
			abgeschlossenen Turnieren. Die Übersicht mit den Berichten des
			Spielers sieht wie folgt aus <br /> <br /> <img alt="Berichte"
				src="image/kmw_report_overview_screen.PNG" /><br /> <br /> Das
			Publikationsdatum ist das Datum, an dem der Bericht veröffentlicht
			wurde. Das Ablaufdatum gibt an, wann der Bericht automatisch vom
			System gelöscht wird. Diese Datum sollte je nach Inhalt des Berichts
			gewählt werden. In der vorstehenden Abbildung hat der Bericht mit der
			Id 3 ein Ablaufdatum, das nur sieben Tage nach dem Publikationsdatum
			liegt, da der Inhalt nir für eine Woche relevant ist. Der Bericht mit
			der Id 1 hat das Ablaudatum 31.12.2099; dies ist die vom System
			vorgegebene Obergrenze für das Ablaufdatum. Jeder Bericht kann durch
			den Spieler durck Klicken des Links in der Spalte "Id" aktualisiert
			werden. Der Inhalt der Spalte Text enthält den gekürzten
			Berichtstext. Der gesamte Bericht kann durch Klicken in die Spalte
			Text sichtbar gemacht werden. Durch Klicken des Links <a
				href='report_create_player.php'>Neuer Datensatz</a> kann der Spieler
			einen neuen Bericht erstellen.

			<h3>Neuerstellung eines Berichts</h3>
			Die Maske zur Erstellung eines neuen Berichts sieht wie folgt aus <br />
			<br /> <img alt="Bericht erstellen" src="image/kmw_report_create.PNG" /><br />
			<br /> Im Textbereich kann beliebiges html Markup mit angegeben
			werden. Natürlich kann auch nur reiner Text eingegeben werden. In der
			Anzeige des Reports wird das html Markup dann korrekt interpretiert.
			<br /> <br /> <img alt="Bericht darstellen"
				src="image/kmw_report_display.PNG" /><br /> <br />
			Die Einbindung von Bildern, Vidoes oder Audio Dateien in die Berichte wird nicht unterstützt. 
			 
			 
			 <?php
    
    if ($accessController->access_HelpTournamentLeader()) {
        echo ("Weitergehende Informationen zum Funktionsumfang der Kiek mol wedder in Web Anwendung finden sich unter finden sich unter");
        echo ("<ul>");
        echo ("<li> <a href='help_tournament_leader.php'>Bedienungsanleitung  für Turnierleiter</a> </li>");
        if ($accessController->access_HelpAdministrator()) {
            echo ("<li> <a href='help_administrator.php'>Bedienungsanleitung für Administratoren</a> </li>");
        }
        echo ("</ul>");
    }
    
    ?>
    
   			<ul>
				<li><a href='help_overview.php'>Allgemeine Bedienungsanleitung</a></li>

			</ul>


		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>