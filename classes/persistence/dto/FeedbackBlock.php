<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\FeedbackBlockModelMappingAware;

/**
 * Class FeedbackBlock
 *
 * @package KPG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class FeedbackBlock extends Block
{
    use FeedbackBlockModelMappingAware;
}
