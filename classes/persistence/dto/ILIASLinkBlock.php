<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\ILIASLinkBlockModelMappingAware;

/**
 * Class ILIASLinkBlock
 *
 * @package KPG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class ILIASLinkBlock extends Block
{
    use ILIASLinkBlockModelMappingAware;

    /**
     * @var int $refId
     */
    private $refId = 0;


    /**
     * @return int
     */
    public function getRefId(): int
    {
        return $this->refId;
    }


    /**
     * @param int $refId
     *
     * @return ILIASLinkBlock
     */
    public function setRefId(int $refId): ILIASLinkBlock
    {
        $this->refId = $refId;

        return $this;
    }
}
