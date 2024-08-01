<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\persistence\dto;

use SRAG\Lernplaces\persistence\mapping\VideoBlockModelMappingAware;

/**
 * Class VideoBlock
 *
 * @package SRAG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class VideoBlock extends Block
{
    use VideoBlockModelMappingAware;

    private string $resourceId;

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function setResourceId(string $resourceId): VideoBlock
    {
        $this->resourceId = $resourceId;

        return $this;
    }
}
