<aside class="sidebar-display-area">
		
<?php
require_once ("./controller/access_controller.php");
require_once ("./util/session_manager.php");

$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$accessController = new AccessController($userRole);

if ($accessController->access_MyPlayerAccount()) {
    echo ("<a href='player_update.php'>Meine Spielerdaten</a> <br />");
}

if ($accessController->access_MySchedule()) {
    echo ("<a href='schedule_overview_player.php'>Meine Partiewünsche</a> <br />");
}

if ($accessController->access_MyGames()) {
    echo ("<a href='game_overview_player.php'>Meine Ergebnisse</a> <br />");
}

if ($accessController->access_MyReports()) {
    echo ("<a href='report_overview_player.php'>Meine Berichte</a> <br /> <br /> <br />");
}

if ($accessController->access_PlayerOverview()) {
    echo ("<a href='player_overview_readonly.php'>Spieler</a> <br />");
}

if ($accessController->access_RoundOverviewCurrentTournament()) {
    echo ("<a href='round_overview_current_tournament.php'>Rundenplan</a> <br />");
}

if ($accessController->access_ScheduleOverviewPerRound()) {
    echo ("<a href='schedule_overview_per_round.php'>Partiewünsche nach Runden</a> <br />");
}

if ($accessController->access_GamesPerRoundOverview()) {
    echo ("<a href='game_overview_per_round.php'>Rundenergebnisse</a> <br />");
}

if ($accessController->access_ReportOverviewPublic()) {
    echo ("<a href='report_overview_public.php'>Berichte</a> <br />");
}

if ($accessController->access_RankingOverview()) {
    echo ("<a href='ranking_overview.php'>Tabelle</a> <br /> <br />");
}

if ($accessController->access_TournamentOverviewAllFinalized()) {
    echo ("<a href='tournament_overview_all_finalized.php'>Abgeschlossene Turniere</a> <br /> <br />");
}

if ($accessController->access_RoundOverviewAllCurrentTournament()) {
    echo ("<hr />");
    echo ("Aktuelles Turnier: <br />");
    echo ("<a href='round_overview_all_current_tournament.php'>Alle Turnierrunden</a> <br />");
}

if ($accessController->access_ScheduleOverviewAllCurrentTournament()) {
    echo ("<a href='schedule_overview_all_current_tournament.php'>Partiewünsche</a> <br />");
}

if ($accessController->access_GameOverviewAllCurrentTournament()) {
    echo ("<a href='game_overview_all_current_tournament.php'>Alle Partieergebnisse</a> <br />");
}

if ($accessController->access_ReportOverviewAllCurrentTournament()) {
    echo ("<a href='report_overview_all_current_tournament.php'>Alle Berichte</a> <br /> <br /> <hr />");
}

if ($accessController->access_PlayerOverviewAll()) {
    echo ("<a href='player_overview_all.php'>Alle Spieler</a> <br />");
}

if ($accessController->access_TournamentOverviewAll()) {
    echo ("<a href='tournament_overview_all.php'>Alle Turniere</a> <br />");
}

if ($accessController->access_RoundOverviewAll()) {
    echo ("<a href='round_overview_all.php'>Alle Turnierrunden</a> <br />");
}

if ($accessController->access_ScheduleOverviewAll()) {
    echo ("<a href='schedule_overview_all.php'>Alle Partiewünsche</a> <br />");
}

if ($accessController->access_GameOverviewAll()) {
    echo ("<a href='game_overview_all.php'>Alle Partieergebnisse</a> <br />");
}

if ($accessController->access_ReportOverviewAll()) {
    echo ("<a href='report_overview_all.php'>Alle Berichte</a> <br /> <br /> <br />");
}

if ($accessController->access_PHP_Info()) {
    echo ("<a href='php_info.php'>PHP Info</a> <br />");
}

?>
		
</aside>