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
require_once ("./controller/tournament_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./model/tournament.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if (! $accessController->access_TournamentDescription()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$controller = new TournamentController();
$tournaments = $controller->getAllActiveTournaments();

if (! empty($tournaments)) {
    $tournament = $tournaments[0];
}

if (isset($tournament)) {
    $tournamentTitle = $tournament->getTournamentTitle();
    $startDate = $tournament->getStartDate();
    $endDate = $tournament->getEndDate();
    $dataBoundarySentence = "Das Turnier beginnt am " . $startDate .
             " und endet am " . $endDate . ". ";
    $prizeAwardSentence = "Die Urkundenverleihung erfolgt am " . $endDate . ". ";
} else {
    $tournamentTitle = '';
    $startDate = '';
    $endDate = '';
    $dataBoundarySentence = '';
    $prizeAwardSentence = '';
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
			<h1>Turnierausschreibung <?php echo ("$tournamentTitle"); ?></h1>
			<table>
				<tr>
					<td>Veranstalter:</td>
					<td>Hamburger Schachklub von 1830 e. V.</td>
				</tr>
				<tr>
					<td>Spieltag:</td>
					<td>In der Regel Freitags, Spielbeginn: 19:00 Uhr. 
					<?php echo ("$dataBoundarySentence"); ?>
					Die Rundentermine finden sich im <a
						href="round_overview_current_tournament.php">Rundenplan</a>.
					</td>
				</tr>
				<tr>
					<td>Teilnehmer:</td>
					<td>Offen für alle Mitglieder des Hamburger Schachklubs von 1830
						e.V. und Gäste.</td>
				</tr>
				<tr>
					<td>Modus:</td>
					<td>Es werden bevorzugt Partien zwischen Spielern mit ähnlich hoher
						<a target="_blank"
						href="https://de.wikipedia.org/wiki/Deutsche_Wertungszahl">DWZ-Wertung</a>
						angesetzt. Wunschpaarungen sind möglich. Mehrfache Ansetzungen
						gleicher Spielpartner sind möglich. Der Ein- oder Ausstieg bzw.
						eine Pause sind zu jeder Runde möglich. Die Karenzzeit beträgt 15
						Minuten. Es gelten die <a target="_blank"
						href="http://www.schachbund.de/turnierordnung.html">FIDE-Regeln</a>
						(Anhang G gilt nicht), mit Ausnahme zu elektronischen Geräten
						gemäß FIDE. Ausgeschaltete Geräte sind erlaubt. Bei deren Gebrauch
						durch Spieler oder Geräuschbildung entscheidet die Turnierleitung
						über Sanktionen.
					</td>
				</tr>
				<tr>
					<td>Bedenkzeit:</td>
					<td>2 Stunden für 40 Züge, danach 30 Minuten für den Rest der
						Partie, d.h. eine Partie ist spätestens um 24:00 Uhr beendet
						(HMM-Modus). Wenn sich beide Partner einigen, kann auch abweichend
						mit dem Modus 90 Minuten für 40 Züge und 30 Minuten für den Rest
						der Partie je Spieler gespielt werden.</td>
				</tr>
				<tr>
					<td>Startgeld:</td>
					<td>Kein Startgeld.</td>
				</tr>
				<tr>
					<td>Spielort:</td>
					<td>Bibliothek im HSK Schachzentrum, Schellingstr. 41 in 22089
						Hamburg.</td>
				</tr>
				<tr>
					<td>Auswertungen:</td>
					<td>Keine DWZ- oder ELO-Auswertung.</td>
				</tr>
				<tr>
					<td>Preise:</td>
					<td>Sieger des Turniers ist, wer bei Turnierende die meisten Punkte
						erzielt hat. Es gibt für jeden Partiegewinn drei Punkte, für jedes
						Remis zwei Punkte und für jeden Verlust einen Punkt. Bei
						kampflosen Partien erhält der Gewinner drei Punkte und der
						Verlierer keinen Punkt. Bei Punktgleichheit entscheidet die
						Feinwertung (<a target="_blank"
						href="https://de.wikipedia.org/wiki/Feinwertung#Wertung_nach_Sonneborn-Berger">Sonneborn-Berger</a>).
						Bei gleicher Feinwertung wird der DWZ-Schnitt der Gegner
						herangezogen. Die Spieler auf den ersten drei Plätzen der
						Abschluss Tabelle erhalten jeweils eine Urkunde. <?php echo ("$prizeAwardSentence"); ?>
					</td>
				</tr>
				<tr>
					<td>Turnierleitung/Schiedsrichter:</td>
					<td>Die Schachwarte des Hamburger Schachklubs oder von ihnen
						beauftragte Personen bilden die Turnierleitung. Sie sind
						gleichzeitig Schiedsrichter.</td>
				</tr>
				<tr>
					<td>Kontakt/Anmeldung:</td>
					<td>Anmeldungen einzelner Spieler und vorab vereinbarte Paarungen
						sowie Wunschpartien bitte bis einen Tag vor dem Spieltag über die
						Web Seite des Turniers erfassen. Bei Erhalt dieser Informationen
						werden sie bei der Planung der Spieltage berücksichtigt. Die
						Anmeldung ist auch an jedem Spieltag (Freitag) bis 18:50 Uhr am
						Spielort möglich.</td>
				</tr>
				<tr>
					<td>Zugang zur Web Seite des Turniers:</td>
					<td>Jeder Spieler kann einen personalisierten Zugang zu der Web
						Seite des Turniers bekommen. Damit besteht die Möglichkeit
						Wunschpartien anzumelden und Spielergebnisse zu erfassen. Der
						Zugang zu der Web Seite kann beantragt werden durch eine e-mail an
						<a href="mailto:kiekmolwedderin@hsk1830.de">kiekmolwedderin@hsk1830.de</a>.
					</td>
				</tr>
				<tr>
					<td>Haftungsausschluss:</td>
					<td>Die Teilnahme und der Besuch erfolgen auf eigenes Risiko. Der
						Hamburger Schachklub von 1830 e.V. übernimmt keinerlei Haftung.</td>
				</tr>
			</table>
		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>