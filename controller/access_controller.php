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

require_once ("./util/user_role.php");

class AccessController
{

    private $userRole;

    public function getUserRole (): UserRole
    {
        return $this->userRole;
    }

    public function setUserRole (UserRole $userRole)
    {
        $this->userRole = $userRole;
    }

    public function __construct (string $userRoleDescription)
    {
        $this->userRole = new UserRole();
        $this->userRole->setUserRoleDescription($userRoleDescription);
    }

    public function __toString (): string
    {
        return $this->userRole->__toString();
    }

    public function access_PHP_Info (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_ResultRegistration (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function access_PlayerOverview (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function access_MyPlayerAccount (): bool
    {
        return ($this->userRole->isGuest());
    }

    public function access_MySchedule (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function access_MyGames (): bool
    {
        return ($this->userRole->isPlayer());
    }
    
    public function access_HelpOverview (): bool
    {
        return ($this->userRole->isAnonymous());
    }
    
    public function access_HelpPlayer (): bool
    {
        return ($this->userRole->isPlayer());
    }
    
    public function access_HelpTournamentLeader (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }
    
    public function access_HelpAdministrator (): bool
    {
        return ($this->userRole->isAdmin());
    }

    public function access_GameRequest (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function access_TournamentOverviewAll (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_TournamentOverviewAllFinalized (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function access_TournamentDescription (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function access_PlayerOverviewAll (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_RoundOverviewAll (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_RoundOverviewAllCurrentTournament (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_RoundOverviewCurrentTournament (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function access_ScheduleOverviewPerRound (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function access_ScheduleOverviewAll (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_ScheduleOverviewAllCurrentTournament (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_GameOverview (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_GamesPerRoundOverview (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function access_GameOverviewAll (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_GameOverviewAllCurrentTournament (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function delete_Game (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function delete_Schedule (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function access_RankingOverview (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function access_ReportOverviewAll (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_ReportOverviewAllCurrentTournament (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function access_MyReports (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function access_ReportOverviewPublic (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function access_ReportOverviewPerRound (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function access_ReportOverviewTopFive (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function delete_Report (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function create_Game (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function create_Player (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function create_Ranking (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function create_Report (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function create_ReportAll (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function create_Round (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function create_Schedule (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function create_Tournament (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function update_Game (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function update_Player (): bool
    {
        return ($this->userRole->isGuest());
    }

    public function update_Ranking (): bool
    {
        return ($this->userRole->isAnonymous());
    }

    public function update_Report (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function update_Round (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }

    public function update_Schedule (): bool
    {
        return ($this->userRole->isPlayer());
    }

    public function update_Tournament (): bool
    {
        return ($this->userRole->isTournamentLeader());
    }
}

?>