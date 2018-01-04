<nav id="mainnav">
<?php
if (isset($_SESSION["user_name"]) && isset($_SESSION["user_role"])) {
    $user_role = $_SESSION["user_role"];
    $user_name = $_SESSION["user_name"];
    echo ('<a href="logout.php">Abmeldung</a> | <a href="tournament_description.php">Ausschreibung</a> | <a href="help_overview.php">Hilfe</a> | <a href="index.php">Startseite</a>' .
             '<span class="login-info"> Angemeldet als: ' . "$user_name" . '. Rolle: ' . "$user_role" . '</span>');
} else {
    echo ('<a href="login.php">Anmeldung</a> | <a href="tournament_description.php">Ausschreibung</a> | <a href="help_overview.php">Hilfe</a> | <a href="index.php">Startseite</a>');
}

?> 
</nav>