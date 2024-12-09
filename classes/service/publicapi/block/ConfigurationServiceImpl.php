<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\block;

use InvalidArgumentException;
use KPG\Learnplaces\persistence\repository\ConfigurationRepository;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;
use KPG\Learnplaces\service\publicapi\model\ConfigurationModel;

/**
 * Class ConfigurationServiceImpl
 *
 * @package KPG\Learnplaces\service\publicapi\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class ConfigurationServiceImpl implements ConfigurationService
{
    /**
     * @var ConfigurationRepository $configurationRepository
     */
    private $configurationRepository;


    /**
     * ConfigurationServiceImpl constructor.
     *
     * @param ConfigurationRepository $configurationRepository
     */
    public function __construct(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }


    /**
     * @inheritDoc
     */
    public function store(ConfigurationModel $configurationModel): ConfigurationModel
    {
        $dto = $this->configurationRepository->store($configurationModel->toDto());
        return $dto->toModel();
    }


    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        try {
            $this->configurationRepository->delete($id);
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException('The configuration with the given id could not be deleted, because the it was not found.', 0, $ex);
        }
    }


    /**
     * @inheritDoc
     */
    public function find(int $id): ConfigurationModel
    {
        try {
            $dto = $this->configurationRepository->find($id);
            return $dto->toModel();
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException('The configuration with the given id does not exist.', 0, $ex);
        }
    }


    /**
     * @inheritDoc
     */
    public function findByObjectId(int $objectId): ConfigurationModel
    {
        try {
            $dto = $this->configurationRepository->findByObjectId($objectId);
            return $dto->toModel();
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException('The configuration could not be found, because the learnplace with the given object id is missing.', 0, $ex);
        }
    }
}
