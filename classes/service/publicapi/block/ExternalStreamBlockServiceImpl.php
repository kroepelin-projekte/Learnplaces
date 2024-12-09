<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\block;

use Exception;
use InvalidArgumentException;
use KPG\Learnplaces\persistence\repository\ExternalStreamBlockRepository;
use KPG\Learnplaces\service\publicapi\model\ExternalStreamBlockModel;

/**
 * Class External
 *
 * @package KPG\Learnplaces\service\publicapi\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @deprecated Not needed for current version
 */
final class ExternalStreamBlockServiceImpl implements ExternalStreamBlockService
{
    /**
     * @var ExternalStreamBlockRepository $externalStreamBlockRepository
     */
    private $externalStreamBlockRepository;


    /**
     * ExternalStreamBlockServiceImpl constructor.
     *
     * @param ExternalStreamBlockRepository $externalStreamBlockRepository
     */
    public function __construct(ExternalStreamBlockRepository $externalStreamBlockRepository)
    {
        $this->externalStreamBlockRepository = $externalStreamBlockRepository;
    }


    /**
     * @inheritDoc
     */
    public function store(ExternalStreamBlockModel $blockModel): ExternalStreamBlockModel
    {
        throw new Exception('Not implemented yet.');
    }


    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        throw new Exception('Not implemented yet.');
    }


    /**
     * @inheritDoc
     */
    public function find(int $id): ExternalStreamBlockModel
    {
        throw new Exception('Not implemented yet.');
    }
}
