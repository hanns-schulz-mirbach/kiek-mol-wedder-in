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

require_once ("./controller/report_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./util/session_manager.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if (! $accessController->update_Report()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

?>
<!doctype html>
<html lang="de">
<head>

<?php
require_once ("./template/head.php");
require_once ("./model/player.php");
?>

</head>
<body>
	<?php require_once ("./template/header.php"); ?>
	<?php require_once ("./template/navigation.php"); ?>
	<div id="workarea">
		<?php include ("./template/sidebar.php"); ?>
		
		<main class="central-display-area">
		<article>

			<h1>Bestätigung Aktualisierung Berichtsdaten</h1>
<?php
$authorId = intval($_POST["author"]);
$roundId = intval($_POST["round"]);
$publicationDate = DateTime::createFromFormat("d.m.Y",
        trim($_POST['publication_date']));
$obsolescenceDate = DateTime::createFromFormat("d.m.Y",
        trim($_POST['obsolescence_date']));
$reportTitle = trim($_POST["report_title"]);
$reportText = trim($_POST["report_text"]);

$controller = new ReportController();

// retrieve data prior to update from database
$report = $controller->getReportById(intval($_GET["id"]));

// the report instance of the controller receives the id of the report to update
// and all data provided for update
$controller->getReport()->setId($report->getId());
$controller->instantiateSkeleton($roundId, $authorId, $publicationDate,
        $obsolescenceDate, $reportTitle, $reportText);
$affectedRows = $controller->updateReport();

if ($affectedRows == 1) {
    echo ('Die Berichtsdatendaten wurden erfolgreich geändert.');
    echo ("<br />");
    echo ("Ursprünglicher Datensatz: ");
    echo ("<br />");
    echo ("$report");
    echo ("<br />");
    echo ("<br />");
    echo ('Geänderter Datensatz: ');
    echo ("<br />");
    echo ($controller->getReport());
} else {
    echo ('Die Änderung der Berichtsdaten hat nicht funktioniert. Bitte überprüfen Sie die Daten und wiederholen Sie ggf. den ');
    echo ('<a href="report_update.php?id=' . $report->getId() . '">Vorgang</a> .');
}

?>
		</article>
		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>