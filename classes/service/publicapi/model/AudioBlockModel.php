<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

use KPG\Lernplaces\persistence\mapping\AudioBlockDtoMappingAware;

/**
 * Class AudioBlockModelModel
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class AudioBlockModel extends BlockModel
{
    use AudioBlockDtoMappingAware;

    /**
     * @var string $path
     */
    private $path = "";


    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * @param string $path
     *
     * @return AudioBlockModel
     */
    public function setPath(string $path): AudioBlockModel
    {
        $this->path = $path;

        return $this;
    }

}
