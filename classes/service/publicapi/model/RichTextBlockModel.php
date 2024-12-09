<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

use KPG\Lernplaces\persistence\mapping\RichTextBlockDtoMappingAware;

/**
 * Class RichTextBlock
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class RichTextBlockModel extends BlockModel
{
    use RichTextBlockDtoMappingAware;

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
     * @return RichTextBlockModel
     */
    public function setContent(string $content): RichTextBlockModel
    {
        $this->content = $content;

        return $this;
    }

}
