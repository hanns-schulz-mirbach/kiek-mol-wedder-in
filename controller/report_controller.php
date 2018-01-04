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
require_once ("./model/player.php");
require_once ("./model/round.php");
require_once ("./util/formatter.php");
require_once ("./db/database.php");

class ReportController
{

    private $report;

    private $formatter;

    private $database;

    public function __construct ()
    {
        $this->report = new Report();
        $this->formatter = new Formatter();
        $this->database = new Database();
    }

    public function __destruct ()
    {
        $this->database = null;
    }

    public function instantiateFromDatabase (int $roundId, int $authorId): void
    {
        $this->report->setRound($this->database->getRoundById($roundId));
        $this->report->setReportAuthor(
                $this->database->getPlayerById($authorId));
    }

    public function instantiateSkeleton (int $roundId, int $authorId,
            DateTime $publicationDate, DateTime $obsolescenceDate,
            string $reportTitle, string $reportText): void
    {
        $this->report->getRound()->setId($roundId);
        $this->report->getReportAuthor()->setId($authorId);
        $this->report->setPublicationDate($publicationDate);
        $this->report->setObsolescenceDate($obsolescenceDate);
        $this->report->setReportTitle($reportTitle);
        $this->report->setReportText($reportText);
    }

    // inserts a new Report in the database. Returns number of new records
    // created. That should always be one. Will fail in case that a Report
    // with Report.getId() is already in the database
    // (i.e. no automatic update will be done)
    public function insertReport (): int
    {
        if ((! is_null($this->report)) && ($this->isReportValid())) {
            return $this->database->insertReport($this->report);
        } else {
            return 0;
        }
    }

    public function getRoundSelection (string $selectName, int $roundId = -1): string
    {
        $activeRounds = $this->database->getAllActiveRounds();
        $roundSelection = $this->formatter->getRoundsForSelectionWithUnknownRound(
                $activeRounds, $selectName, $roundId);
        return $roundSelection;
    }

    public function getAuthorSelection (string $selectName, $playerId = - 1): string
    {
        $players = $this->database->getAllActivePlayers();
        $playerSelection = $this->formatter->getPlayersForSelectionWithoutUnknown(
                $players, $selectName, $playerId);
        return $playerSelection;
    }

    public function getReport (): Report
    {
        return $this->report;
    }

    public function setReport (Report $report): void
    {
        $this->report = $report;
    }

    public function updateReport (): int
    {
        if (! is_null($this->report) && $this->isReportValid()) {
            return $this->database->updateReport($this->report);
        } else {
            return 0;
        }
    }

    public function getAllReports (): array
    {
        return $this->database->getAllReports();
    }
    
    public function getReportsForCurrentTournament (): array
    {
        return $this->database->getAllActiveReports();
    }
    
    
    public function getAllReportsAuthoredBy (Player $player): array
    {
        return $this->database->getAllReportsAuthoredBy($player->getId());
    }
    

    public function getAllReportsForRound (int $roundId): array
    {
        return $this->database->getAllReportsForRound($roundId);
    }

    public function getAllReportsPublishedAfter (DateTime $publicationDate): array
    {
        return $this->database->getAllReportsPublishedAfter($publicationDate);
    }
    
    public function getTopNReports (int $n): array
    {
        return $this->database->getTopNReports($n);
    }
    

    public function getReportById (int $id): Report
    {
        return $this->database->getReportById($id);
    }

    public function deleteReportFromDatabase (): int
    {
        return $this->database->deleteReport($this->report);
    }

    public function deleteAllObsoleteReports (): int
    {
        $now = new DateTime();
        
        return ($this->database->deleteReportsOlderAs($now));
    }

    private function referencedObjectsExistInDatabase (): bool
    {
        if ($this->report->getRound()->getId() != - 1) {
            return $this->database->playerAndRoundExistInDatabase(
                    $this->report->getReportAuthor()
                        ->getId(),
                    $this->report->getRound()
                        ->getId());
        } else {
            return $this->database->playerExistsInDatabase(
                    $this->report->getReportAuthor()
                        ->getId());
        }
    }

    private function isReportValid (): bool
    {
        // The report is considered valid when the references in the database
        // exist
        // The member variable $this->report might returen false on
        // $this->report->isReportValid()
        // That will happen when just the object id's have been inserted without
        // the other attributes
        // That is a valid staus for update purposes
        return $this->referencedObjectsExistInDatabase();
    }
}

?>