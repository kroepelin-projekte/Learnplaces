<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\block;

use InvalidArgumentException;
use KPG\Learnplaces\persistence\repository\AnswerRepository;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;
use KPG\Learnplaces\service\publicapi\model\AnswerModel;

/**
 * Class AnswerServiceImpl
 *
 * @package KPG\Learnplaces\service\publicapi
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @deprecated Not needed for current version
 */
final class AnswerServiceImpl implements AnswerService
{
    /**
     * @var AnswerRepository $answerRepository
     */
    private $answerRepository;


    /**
     * AnswerServiceImpl constructor.
     *
     * @param AnswerRepository $answerRepository
     */
    public function __construct(AnswerRepository $answerRepository)
    {
        $this->answerRepository = $answerRepository;
    }


    /**
     * @inheritDoc
     */
    public function store(AnswerModel $answerModel): AnswerModel
    {
        $dto = $answerModel->toDto();
        $dto = $this->answerRepository->store($dto);
        return $dto->toModel();
    }


    /**
     * @inheritDoc
     */
    public function find(int $answerId): AnswerModel
    {
        try {
            $dto = $this->answerRepository->find($answerId);
            return $dto->toModel();
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException("Invalid answer id \"$answerId\" provided, unable to find object.", 0, $ex);
        }
    }


    /**
     * @inheritDoc
     */
    public function delete(int $answerId)
    {
        try {
            $this->answerRepository->delete($answerId);
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException("Invalid answer id \"$answerId\" provided, unable to delete object.", 0, $ex);
        }
    }
}
