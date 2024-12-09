<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\Comment;
use KPG\Learnplaces\persistence\dto\CommentBlock;
use KPG\Learnplaces\service\publicapi\model\CommentBlockModel;
use KPG\Learnplaces\service\publicapi\model\CommentModel;

/**
 * Trait CommentBlockDtoMappingAware
 *
 * Adds functionality to map a comment block model to a comment block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait CommentBlockDtoMappingAware
{
    public function toDto(): CommentBlock
    {
        /**
         * @var CommentBlockDtoMappingAware|CommentBlockModel $this
         */
        $dto = new CommentBlock();
        $dto->setComments($this->mapComments($this->getComments()));
        $this->fillBaseBlock($dto);

        return $dto;
    }

    private function mapComments(array $commentModels): array
    {
        return array_map(
            function (CommentModel $commentModel) {return $commentModel->toDto();},
            $commentModels
        );
    }
}

/**
 * Trait CommentBlockModelMappingAware
 *
 * Adds functionality to map a comment block dto to a comment block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait CommentBlockModelMappingAware
{
    public function toModel(): CommentBlockModel
    {
        /**
         * @var CommentBlockModelMappingAware|CommentBlock $this
         */
        $model = new CommentBlockModel();
        $model->setComments($this->mapComments($this->getComments()));
        $this->fillBaseBlock($model);

        return $model;
    }

    private function mapComments(array $commentDtos): array
    {
        return array_map(
            function (Comment $commentDto) {return $commentDto->toModel();},
            $commentDtos
        );
    }
}
