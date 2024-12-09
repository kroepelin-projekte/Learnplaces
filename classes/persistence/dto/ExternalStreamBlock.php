<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\ExternalStreamBlockModelMappingAware;

/**
 * Class ExternalStreamBlock
 *
 * @package KPG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class ExternalStreamBlock extends Block
{
    use ExternalStreamBlockModelMappingAware;

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
     * @return ExternalStreamBlock
     */
    public function setUrl(string $url): ExternalStreamBlock
    {
        $this->url = $url;

        return $this;
    }

}
