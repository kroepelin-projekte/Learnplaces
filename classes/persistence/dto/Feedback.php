<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\FeedbackModelMappingAware;

/**
 * Class Feedback
 *
 * @package KPG\Learnplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class Feedback
{
    use FeedbackModelMappingAware;

    /**
     * @var int $id
     */
    private $id = 0;

    /**
     * @var string $content
     */
    private $content = "";
    /**
     * @var int $userId
     */
    private $userId = 0;


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
     * @return Feedback
     */
    public function setId(int $id): Feedback
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }


    /**
     * @param string $content
     *
     * @return Feedback
     */
    public function setContent(string $content): Feedback
    {
        $this->content = $content;

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
     * @return Feedback
     */
    public function setUserId(int $userId): Feedback
    {
        $this->userId = $userId;

        return $this;
    }
}
