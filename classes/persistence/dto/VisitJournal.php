<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use DateTime;
use KPG\Lernplaces\persistence\mapping\VisitJournalModelMappingAware;

/**
 * Class VisitJournal
 *
 * @package KPG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class VisitJournal
{
    use VisitJournalModelMappingAware;

    /**
     * @var int $id
     */
    private $id = 0;
    /**
     * @var int $userId
     */
    private $userId = 0;
    /**
     * @var DateTime $time
     */
    private $time;


    /**
     * VisitJournal constructor.
     */
    public function __construct()
    {
        $this->time = new DateTime();
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param int $id
     *
     * @return VisitJournal
     */
    public function setId(int $id): VisitJournal
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }


    /**
     * @param int $userId
     *
     * @return VisitJournal
     */
    public function setUserId(int $userId): VisitJournal
    {
        $this->userId = $userId;

        return $this;
    }


    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }


    /**
     * @param DateTime $time
     *
     * @return VisitJournal
     */
    public function setTime(DateTime $time): VisitJournal
    {
        $this->time = $time;

        return $this;
    }
}
