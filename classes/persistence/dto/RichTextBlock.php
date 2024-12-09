<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\RichTextBlockModelMappingAware;

/**
 * Class RichTextBlock
 *
 * @package KPG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class RichTextBlock extends Block
{
    use RichTextBlockModelMappingAware;

    /**
     * @var string $content
     */
    private $content = "";


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
     * @return RichTextBlock
     */
    public function setContent(string $content): RichTextBlock
    {
        $this->content = $content;

        return $this;
    }

}
