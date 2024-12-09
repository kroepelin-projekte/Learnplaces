<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

/**
 * Class VideoModel
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class VideoModel
{
    private string $resourceId;

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function setResourceId(string $resourceId): VideoModel
    {
        $this->resourceId = $resourceId;

        return $this;
    }
}
