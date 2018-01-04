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

require_once ("./util/formatter.php");
require_once ("./controller/tournament_controller.php");

class SessionManager
{

    public function getUserRole (): string
    {
        if (isset($_SESSION["user_role"])) {
            $user_role = $_SESSION["user_role"];
        } else {
            $user_role = 'Anonymous';
        }
        return $user_role;
    }

    public function isUserRoleSet (): bool
    {
        return isset($_SESSION["user_role"]);
    }

    public function getUserName (): string
    {
        if (isset($_SESSION["user_name"])) {
            $user_name = $_SESSION["user_name"];
        } else {
            $user_name = 'Anonymous';
        }
        return $user_name;
    }

    public function isUserNameSet (): bool
    {
        return isset($_SESSION["user_name"]);
    }

    public function getUserId (): int
    {
        if (isset($_SESSION["user_id"])) {
            $user_id = intval($_SESSION["user_id"]);
        } else {
            $user_id = - 1;
        }
        return $user_id;
    }

    public function isUserIdSet (): bool
    {
        return isset($_SESSION["user_id"]);
    }

    public function getTournamentSelection (string $selectName): string
    {
        if (isset($_SESSION["tournament_selection"])) {
            $tournamentSelectionString = $_SESSION["tournament_selection"];
        } else {
            $tournamentController = new TournamentController();
            $allActiveTournaments = $tournamentController->getAllActiveTournaments();
            $formatter = new Formatter();
            $tournamentSelectionString = $formatter->getTournamentsForSelection(
                    $allActiveTournaments, $selectName);
            $_SESSION["tournament_selection"] = $tournamentSelectionString;
        }
        return $tournamentSelectionString;
    }

    public function isTournamentSelectionSet (): bool
    {
        return isset($_SESSION["tournament_selection"]);
    }

    public function flushTournamentCache (): void
    {
        if (isset($_SESSION["tournament_selection"])) {
            unset($_SESSION["tournament_selection"]);
        }
    }
}

?>