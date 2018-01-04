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

require_once ("./model/report.php");
require_once ("./controller/report_controller.php");
require_once ("./controller/access_controller.php");
require_once ("./util/session_manager.php");
require_once ("./util/formatter.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if (! $accessController->access_ReportOverviewPublic()) {
    echo ("Sie haben keine Berechtigung diese Aktion auszuführen. Melden Sie sich mit einem gültigen Benutzerzugang am System an.");
    exit();
}

$controller = new ReportController();
$controller->deleteAllObsoleteReports();
$report = $controller->getReportById(intval($_GET["id"]));
$reportTitle = $report->getReportTitle();
$reportTitleDisplay = html_entity_decode($reportTitle, ENT_QUOTES | ENT_XML1,
        'UTF-8');
$reportText = $report->getReportText();
$reportTextDisplay = html_entity_decode($reportText, ENT_QUOTES | ENT_XML1,
        'UTF-8');
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
		<h1><?php echo ("$reportTitleDisplay");?></h1>
		<h2>von 
		<?php echo ($report->getReportAuthor()->getFirstname() . " " . $report->getReportAuthor()->getLastname());?>
		</h2>
		</br>
		<?php echo ("$reportTextDisplay");?>
		<br />
		<br />
		<?php echo("Publikationsdatum: " . $report->getPublicationDate()->format('d.m.Y')); ?>

		</main>
	</div>
	<?php include ("./template/footer.php"); ?>
</body>
</html>