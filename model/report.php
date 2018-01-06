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

require_once ("./model/round.php");
require_once ("./model/player.php");

class Report
{

    private $id;

    private $round;

    private $report_author;

    private $report_title;

    private $report_text;

    private $publication_date;

    private $obsolescence_date;

    function __construct ()
    {
        // default id. The final id is generated later automatically by the
        // database
        $this->id = - 1;
        $this->round = new Round();
        $this->report_author = new Player('', '', '', '', - 1, - 1, '', '', '',
                '');
        $this->report_title = '';
        $this->report_text = '';
        $this->publication_date = new DateTime();
        $this->obsolescence_date = new DateTime();
    }

    public function __toString (): string
    {
        $textLength = strlen($this->report_text);
        $truncatedReportText = substr($this->report_text, 0, 50) .
                 " ... insgesamt " . "$textLength" . " Zeichen";
        $publicationDate = $this->getPublicationDate();
        $publicationDateString = $publicationDate->format('d.m.Y');
        $obsolescenceDate = $this->getObsolescenceDate();
        $obsolescenceDateString = $obsolescenceDate->format('d.m.Y');
        $reportDataAsHTMLTable = '<table><tr><th>Attribut</th><th>Wert</th></tr><tr><td>Id:</td><td>' .
                 "$this->id" . '</td></tr>
<tr><td>Runde:</td><td>' .
                 $this->round->getId() . '</td></tr>
<tr><td>Autor:</td><td>' .
                 $this->report_author->getId() . '</td></tr>
<tr><td>Titel:</td><td>' .
                 "$this->report_title" . '</td></tr>
<tr><td>Publikationsdatum:</td><td>' .
                 "$publicationDateString" . '</td></tr>
<tr><td>Ablaufdatum:</td><td>' .
                 "$obsolescenceDateString" . '</td></tr>
<tr><td>Text:</td><td>' .
                 "$truncatedReportText" . '</td></tr>
</table>';
        
        return $reportDataAsHTMLTable;
    }

    public function getUpdateSQL (string $tablename): string
    {
        $updateSQL = 'UPDATE ' . $tablename . ' SET ' . "round = " .
                 $this->round->getId() . ", " . "report_author =  " .
                 $this->report_author->getId() . ", " . "report_title =  '" .
                 $this->getEncodedReportTitle() . "', " . "publication_date =  '" .
                 $this->publication_date->format('Y-m-d') . "', " .
                 "obsolescence_date =  '" .
                 $this->obsolescence_date->format('Y-m-d') . "', " .
                 "report_text =  '" . $this->getEncodedReportText() .
                 "' WHERE id = " . $this->id;
        
        return $updateSQL;
    }

    public function isReportValid (): bool
    {
        return ($this->idIsValid() && $this->authorIsValid() &&
                 $this->roundIsValid());
    }

    public function getReportAsTableRow (): string
    {
        return "<tr>" . "<td>" . "$this->id" . "</td><td>" .
                 $this->round->getId() . "</td><td>" . $this->player->getId() .
                 "</td>" . "<td>" .
                 $this->participation->getParticipationStatus() . "</td><td>" .
                 $this->desired_opponent->getId() . "</td><td>" .
                 $this->desired_color . "</td></tr>";
    }

    private function idIsValid (): bool
    {
        return (isset($this->id) && is_int($this->id));
    }

    private function roundIsValid (): bool
    {
        if (isset($this->round)) {
            return (($this->round->isRoundValid));
        } else {
            return false;
        }
    }

    private function authorIsValid (): bool
    {
        if (isset($this->report_author)) {
            return ($this->report_author->isPlayerValid());
        } else {
            return false;
        }
    }

    private function reportTitleIsValid (): bool
    {
        if (isset($this->report_title)) {
            return true;
        } else {
            return false;
        }
    }

    private function reportTextIsValid (): bool
    {
        if (isset($this->report_text)) {
            return true;
        } else {
            return false;
        }
    }

    private function publicationDateIsValid (): bool
    {
        if (isset($this->publication_date)) {
            return true;
        } else {
            return false;
        }
    }

    private function obsolescenceDateIsValid (): bool
    {
        if (isset($this->obsolescence_date)) {
            return (($this->obsolescence_date >= $this->publication_date));
        } else {
            return false;
        }
    }

    public function getId (): int
    {
        return $this->id;
    }

    public function getRound (): Round
    {
        return $this->round;
    }

    public function getReportAuthor (): Player
    {
        return $this->report_author;
    }

    public function getReportTitle (): string
    {
        $reportTitle = $this->report_title;
        $reportTitleDisplay = html_entity_decode($reportTitle,
                ENT_QUOTES | ENT_XML1, 'UTF-8');
        return $reportTitleDisplay;
    }

    public function getEncodedReportTitle (): string
    {
        return $this->report_title;
    }

    public function getReportText (): string
    {
        $reportText = $this->report_text;
        $reportTextDisplay = html_entity_decode($reportText,
                ENT_QUOTES | ENT_XML1, "UTF-8");
        return $reportTextDisplay;
    }

    public function getEncodedReportText (): string
    {
        return $this->report_text;
    }

    public function getPublicationDate (): DateTime
    {
        return $this->publication_date;
    }

    public function getObsolescenceDate (): DateTime
    {
        return $this->obsolescence_date;
    }

    public function setId (int $id): void
    {
        $this->id = $id;
    }

    public function setRound (Round $round): void
    {
        $this->round = $round;
    }

    public function setReportAuthor (Player $reportAuthor): void
    {
        $this->report_author = $reportAuthor;
    }

    public function setReportTitle (string $reportTitle): void
    {
        $this->report_title = htmlentities($reportTitle, ENT_QUOTES, "UTF-8");
    }

    public function setReportText (string $reportText): void
    {
        $this->report_text = htmlentities($reportText, ENT_QUOTES, "UTF-8");
    }

    public function setPublicationDate (DateTime $publicationDate): void
    {
        $this->publication_date = $publicationDate;
    }

    public function setObsolescenceDate (DateTime $obsolescenceDate): void
    {
        $this->obsolescence_date = $obsolescenceDate;
    }
}
?>
