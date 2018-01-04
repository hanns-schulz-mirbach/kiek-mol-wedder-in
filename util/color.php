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

class Color
{

    // 0=unknown, 1=black 2=white
    private $gameColor;

    public function __construct ()
    {
        $this->gameColor = 0; // unknown
    }

    public function __toString (): string
    {
        switch ($this->gameColor) {
            case 0:
                $colorDescription = "Unbekannt";
                break;
            
            case 1:
                $colorDescription = "Schwarz";
                break;
            
            case 2:
                $colorDescription = "Weiß";
                break;
            
            default:
                $colorDescription = "Unbekannt";
                break;
        }
        
        return $colorDescription;
    }

    public function isUnknown (): bool
    {
        return (($this->gameColor == 0));
    }

    public function isBlack (): bool
    {
        return (($this->gameColor == 1));
    }

    public function isWhite (): bool
    {
        return (($this->gameColor == 2));
    }

    public function getGameColor (): int
    {
        return $this->gameColor;
    }

    public function setGameColor (int $gameColor): void
    {
        $this->gameColor = $gameColor;
    }

    public function setIsUnknown ()
    {
        $this->gameColor = 0;
    }

    public function setIsBlack ()
    {
        $this->gameColor = 1;
    }

    public function setIsWhite ()
    {
        $this->gameColor = 2;
    }

    public function isColorValid (): bool
    {
        return (($this->gameColor == 0) || ($this->gameColor == 1) ||
                 ($this->gameColor == 2));
    }

    public static function getNumberOfResultValues ()
    {
        // There are three result values:
        // 0=unknown
        // 1=black
        // 2=white
        return 3;
    }

    public static function getLowestResultValue ()
    {
        return 0;
    }
}

?>