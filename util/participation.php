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

class Participation
{

    // 0 = no participation, 1 = confirmed participation 2 = unknown
    // participation
    private $participationStatus;

    public function __construct ()
    {
        $this->participationStatus = 2; // unknown participation status
    }

    public function __toString (): string
    {
        switch ($this->participationStatus) {
            case 0:
                $partcipationStatusDescription = "Nein";
                break;
            
            case 1:
                $partcipationStatusDescription = "Ja";
                break;
            
            case 2:
                $partcipationStatusDescription = "Unbekannt";
                break;
            
            default:
                $partcipationStatusDescription = "Unbekannt";
                break;
        }
        
        return $partcipationStatusDescription;
    }

    public function willParticipate (): bool
    {
        return (($this->participationStatus == 1));
    }

    public function willNotParticipate (): bool
    {
        return (($this->participationStatus == 0));
    }

    public function participationUnknown (): bool
    {
        return (($this->participationStatus == 2));
    }

    public function getParticipationStatus (): int
    {
        return $this->participationStatus;
    }

    public function setParticipationStatus (int $participationStatus): void
    {
        $this->participationStatus = $participationStatus;
    }

    public function isParticipationValid (): bool
    {
        return (($this->participationStatus == 0) ||
                 ($this->participationStatus == 1) ||
                 ($this->participationStatus == 2));
    }
}

?>