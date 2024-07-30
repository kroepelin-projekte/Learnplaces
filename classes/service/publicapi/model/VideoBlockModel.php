<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\service\publicapi\model;

use SRAG\Lernplaces\persistence\mapping\VideoBlockDtoMappingAware;

/**
 * Class VideoBlock
 *
 * @package SRAG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class VideoBlockModel extends BlockModel
{
    use VideoBlockDtoMappingAware;

    private string $resourceId;

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function setResourceId(string $resourceId): VideoBlockModel
    {
        $this->resourceId = $resourceId;

        return $this;
    }
}
