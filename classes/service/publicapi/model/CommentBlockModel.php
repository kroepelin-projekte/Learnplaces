<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

use KPG\Lernplaces\persistence\mapping\CommentBlockDtoMappingAware;

/**
 * Class CommentBlockModel
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class CommentBlockModel extends BlockModel
{
    use CommentBlockDtoMappingAware;

    /**
     * @var CommentModel[]
     */
    private $comments = [];


    /**
     * @return CommentModel[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }


    /**
     * @param CommentModel[] $comments
     *
     * @return CommentBlockModel
     */
    public function setComments(array $comments): CommentBlockModel
    {
        $this->comments = $comments;

        return $this;
    }

}
