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

require_once ("./model/game.php");
require_once ("./controller/game_controller.php");
require_once ("./util/session_manager.php");
require_once ("./util/formatter.php");
require_once ("./util/color.php");

session_start();
$sessionManager = new SessionManager();
$userRole = $sessionManager->getUserRole();

$formatter = new Formatter();

$controller = new GameController();
$game = $controller->getGameById(intval($_GET["id"]));
$controller->setGame($game);

$roundSelectName = "round";
$roundSelection = $controller->getRoundSelection($roundSelectName,
        $game->getRound()
            ->getId());

$playerId = $sessionManager->getUserId();
$color = new Color();
if ($playerId == $game->getPlayerWhite()->getId()) {
    $opponentId = $game->getPlayerBlack()->getId();
    $color->setIsWhite();
} elseif ($playerId == $game->getPlayerBlack()->getId()) {
    $opponentId = $game->getPlayerWhite()->getId();
    $color->setIsBlack();
} else {
    $opponentId = $game->getPlayerWhite()->getId();
    $color->setIsUnknown();
    ;
}

$opponentSelectName = "opponent";
$opponentSelection = $controller->getOpponentSelection($opponentSelectName,
        $playerId, $opponentId);

$colorSelectName = "color";
$colorSelection = $controller->getColorSelection($colorSelectName,
        $color->getGameColor());

$resultSelectName = "result";
$resultSelection = $controller->getResultSelection($resultSelectName,
        $game->getResult()
            ->getGameResult());

$targetURL = "game_update_confirmation_player.php?id=" . $game->getId();
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
			<h1>Aktualisierung Partieergebnis</h1>

			<form action=" <?php echo ("$targetURL"); ?> " method="post">
				<input type="hidden" name="player"
					value=" <?php echo ($playerId); ?> ">
				<table>
			<?php echo ($formatter->getDataTableHeader());?>
			<tr>
						<td><label for="round">Runde:</label></td>
						<td> 
				<?php echo("$roundSelection"); ?>
			</td>
					</tr>
					<tr>
						<td><label for="opponent">Gegner:</label></td>
						<td>
				<?php echo("$opponentSelection"); ?>
			</td>
					</tr>
					<tr>
						<td><label for="color">Farbe, mit der Sie gespielt haben:</label></td>
						<td>
				<?php echo("$colorSelection"); ?>
			</td>
					</tr>
					<tr>
						<td><label for="gameDate">Partiedatum:</label></td>
						<td><input type="text" name="gameDate" id="gameDate"
							placeholder="TT.MM.JJJJ" required
							pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$"
							title="Datumseingabe im Format TT.MM.JJJJ"
							value=<?php echo ("'" . $game->getDateOfGameForDisplay() . "'"); ?> /></td>
					</tr>
					<tr>

						<td><label for="result">Partieergebnis:</label></td>
						<td> 
				<?php echo("$resultSelection"); ?>
			</td>
					</tr>
				</table>
			<?php echo ($formatter->getSubmitResetControl());?>
		</form>

		</article>
		</main>
	</div>
	<?php require_once ("./template/footer.php"); ?>
</body>
</html>