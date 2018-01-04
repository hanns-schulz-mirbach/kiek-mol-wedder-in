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

class Result
{

    // 0=unknown (no result yet available), 1=draw 2=black wins 3=white wins
    // 4=black wins by default (kampflos) 5=white wins by default (kampflos)
    private $gameResult;

    public function __construct ()
    {
        $this->gameResult = 0; // unknown (no result yet available)
    }

    public function __toString (): string
    {
        switch ($this->gameResult) {
            case 0:
                $resultDescription = "Unbekannt";
                break;
            
            case 1:
                $resultDescription = "1/2 - 1/2";
                break;
            
            case 2:
                $resultDescription = "0 - 1";
                break;
            
            case 3:
                $resultDescription = "1 - 0";
                break;
            
            case 4:
                $resultDescription = "-  +";
                break;
            
            case 5:
                $resultDescription = "+  -";
                break;
            
            default:
                $resultDescription = "Unbekannt";
                break;
        }
        
        return $resultDescription;
    }

    public function getGameScoreForWhite (): int
    {
        switch ($this->gameResult) {
            case 0:
                $gameScore = 0; // unknown; no points
                break;
            
            case 1:
                $gameScore = 2; // draw; two points
                break;
            
            case 2:
                $gameScore = 1; // black wins; one point for participation
                break;
            
            case 3:
                $gameScore = 3; // white wins; three points for victory
                break;
            
            case 4:
                $gameScore = 0; // black wins by default; no points
                break;
            
            case 5:
                $gameScore = 3; // white wins by defaults; three points
                break;
            
            default:
                $gameScore = 0; // that should never happen; no points
                break;
        }
        
        return $gameScore;
    }

    public function getGameScoreForBlack (): int
    {
        switch ($this->gameResult) {
            case 0:
                $gameScore = 0; // unknown; no points
                break;
            
            case 1:
                $gameScore = 2; // draw; two points
                break;
            
            case 2:
                $gameScore = 3; // black wins; three points for victory
                break;
            
            case 3:
                $gameScore = 1; // white wins; one point for participation
                break;
            
            case 4:
                $gameScore = 3; // black wins by default; three points
                break;
            
            case 5:
                $gameScore = 0; // white wins by defaults; no points
                break;
            
            default:
                $gameScore = 0; // that should never happen; no points
                break;
        }
        
        return $gameScore;
    }

    public function isUnknown (): bool
    {
        return (($this->gameResult == 0));
    }

    public function isDraw (): bool
    {
        return (($this->gameResult == 1));
    }

    public function isBlackWin (): bool
    {
        return (($this->gameResult == 2));
    }

    public function isWhiteWin (): bool
    {
        return (($this->gameResult == 3));
    }

    public function isBlackWinByDefault (): bool
    {
        return (($this->gameResult == 4));
    }

    public function isWhiteWinByDefault (): bool
    {
        return (($this->gameResult == 5));
    }

    public function getGameResult (): int
    {
        return $this->gameResult;
    }

    public function setGameResult (int $gameResult): void
    {
        $this->gameResult = $gameResult;
    }

    public function setResultIsUnknown ()
    {
        $this->gameResult = 0;
    }

    public function setResultIsDraw ()
    {
        $this->gameResult = 1;
    }

    public function setResultIsBlackWin ()
    {
        $this->gameResult = 2;
    }

    public function setResultIsWhiteWin ()
    {
        $this->gameResult = 3;
    }

    public function setResultIsBlackWinByDefault ()
    {
        $this->gameResult = 4;
    }

    public function setResultIsWhiteWinByDefault ()
    {
        $this->gameResult = 5;
    }

    public function isResultValid (): bool
    {
        return (($this->gameResult == 0) || ($this->gameResult == 1) ||
                 ($this->gameResult == 2) || ($this->gameResult == 3) ||
                 ($this->gameResult == 4) || ($this->gameResult == 5));
    }

    public static function getNumberOfResultValues ()
    {
        // There are six result values:
        // 0=unknown (no result yet available),
        // 1=draw
        // 2=black wins
        // 3=white wins
        // 4=black wins by default (kampflos)
        // 5=white wins by default (kampflos)
        return 6;
    }

    public static function getLowestResultValue ()
    {
        return 0;
    }
}

?>