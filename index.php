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
require_once ("./model/report.php");
require_once ("./controller/report_controller.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

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
			<h1>Trainingspartien unter Turnierbedingungen</h1>
			Das "Kiek mol wedder in" Turnier des <a target="_blank"
				href="http://www.hsk1830.de">Hamburger Schachklubs von 1830 e.V.</a>
			bietet die Möglichkeit regelmäßig (jede Woche Freitags ab 19:00 Uhr)
			Trainingspartien zu spielen. Die Bedenkzeitregelung orientiert sich
			am Modus der Hamburger Mannschaftsmeisterschaften (2h für 40 Züge;
			danach 30 min für den Rest der Partie). Es erfolgt keine DWZ oder ELO
			Auswertung. Damit können die Teilnehmer mit für sie neuen
			schachlichen Konzepten (z.B. neue Eröffnungen, ambitionierte
			Angriffe) experimentieren und haben genügend Zeit, diese Ansätze in
			der Partie zu durchdenken. Es werden bevorzugt Partien zwischen
			Spielern ähnlicher Spielstärke (<a target="_blank"
				href="https://de.wikipedia.org/wiki/Deutsche_Wertungszahl">DWZ-Wertung</a>)
			angesetzt. Nach den Partien stehen in der Regel auf Wunsch
			spielstarke Mitglieder des Hamburger Schachklubs für eine gemeinsame
			Partieanalyse zur Verfügung. Weitere Details zum Turnier finden sich
			in der <a target="_blank" href="tournament_description.php">
				Auschreibung </a> sowie im <a target="_blank"
				href="round_overview_current_tournament.php"> Rundenplan </a> .

			
<?php
$controller = new ReportController();
$top3Reports = $controller->getTopNReports(3);

if (! empty($top3Reports)) {
    
    echo ("<h3>Neueste Nachrichten zum Turnier</h3>");
    
    foreach ($top3Reports as $report) {
        $reportAuthor = $report->getReportAuthor()->getFirstname() . " " .
                 $report->getReportAuthor()->getLastname();
        $publicationDate = $report->getPublicationDate()->format('d.m.Y');
        $reportText = $report->getReportText();
        $reportTextDecoded = html_entity_decode($reportText,
                ENT_QUOTES | ENT_XML1, 'UTF-8');
        $reportLength = strlen($reportTextDecoded);
        if ($reportLength < 501) {
            $reportTextDisplay = $reportTextDecoded;
            $targetUrl = '';
        } else {
            $reportTextDisplay = substr($reportTextDecoded, 0, 500);
            $targetUrl = "<div><a target = '_blank' href='report_show.php?id=" .
                     $report->getId() . "'>" . "Gesamter Bericht </a></div>";
        }
        
        echo ($report->getReportTitle() . ", von " . $reportAuthor .
                 ", Publikationsdatum " . "$publicationDate");
        echo ("<br/>");
        echo ("$reportTextDisplay" . '    ' . "$targetUrl");
        echo ("<br/> <hr /> <br />");
    }
    echo ("<a href='report_overview_public.php'>Alle Berichte</a>");
}

?>

		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>