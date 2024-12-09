<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

use KPG\Lernplaces\persistence\mapping\ExternalStreamBlockDtoMappingAware;

/**
 * Class ExternalStreamBlock
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class ExternalStreamBlockModel extends BlockModel
{
    use ExternalStreamBlockDtoMappingAware;

    /**
     * @var string $url
     */
    private $url = "";


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }


    /**
     * @param string $url
     *
     * @return ExternalStreamBlockModel
     */
    public function setUrl(string $url): ExternalStreamBlockModel
    {
        $this->url = $url;

        return $this;
    }

}
