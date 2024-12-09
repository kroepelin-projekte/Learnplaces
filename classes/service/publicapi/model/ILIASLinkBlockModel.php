<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

use KPG\Lernplaces\persistence\mapping\ILIASLinkBlockDtoMappingAware;

/**
 * Class ILIASLinkBlock
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class ILIASLinkBlockModel extends BlockModel
{
    use ILIASLinkBlockDtoMappingAware;

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
     * @return ILIASLinkBlockModel
     */
    public function setRefId(int $refId): ILIASLinkBlockModel
    {
        $this->refId = $refId;

        return $this;
    }
}
