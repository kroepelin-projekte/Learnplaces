<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use DateTime;
use ilDatabaseException;
use KPG\Learnplaces\persistence\dto\Answer;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

use function array_map;
use function is_null;
use function stringValue;

/**
 * Class AnswerRepositoryImpl
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class AnswerRepositoryImpl implements AnswerRepository
{
    /**
     * @var PictureRepository $pictureRepository
     */
    private $pictureRepository;

    /**
     * AnswerRepositoryImpl constructor.
     *
     * @param PictureRepository $pictureRepository
     */
    public function __construct(PictureRepository $pictureRepository)
    {
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @inheritdoc
     */
    public function store(Answer $answer): Answer
    {
        $activeRecord = $this->mapToEntity($answer);
        $activeRecord->store();
        return $this->mapToDTO($activeRecord);
    }

    /**
     * @inheritdoc
     */
    public function find(int $id): Answer
    {
        try {
            $answerEntity = \KPG\Learnplaces\persistence\entity\Answer::findOrFail($id);
            return $this->mapToDTO($answerEntity);
        } catch (arException $ex) {
            throw new EntityNotFoundException("Answer with id \"$id\" not found.", $ex);
        }
    }


    /**
     * @inheritdoc
     */
    public function findByCommentId(int $id): array
    {
        $answers = \KPG\Learnplaces\persistence\entity\Answer::where(['fk_comment_id' => $id])->get();
        return array_map(
            function (\KPG\Learnplaces\persistence\entity\Answer $answer) { return $this->mapToDTO($answer); },
            $answers
        );
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $answerEntity = \KPG\Learnplaces\persistence\entity\Answer::findOrFail($id);
            $answerEntity->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Answer with id \"$id\" not found.", $ex);
        } catch(ilDatabaseException $ex) {
            throw new ilDatabaseException("Unable to delete answer with id \"$id\"");
        }
    }

    private function mapToDTO(\KPG\Learnplaces\persistence\entity\Answer $answerEntity): Answer
    {

        $answer = new Answer();
        $answer
            ->setId($answerEntity->getPkId())
            ->setTitle($answer->getTitle())
            ->setContent($answer->getContent())
            ->setCreateDate(new DateTime('@' . strval($answerEntity->getCreateDate())))
            ->setUserId($answerEntity->getFkIluserId())
            ->setPicture($this->pictureRepository->find($answerEntity->getFkPictureId()));

        return $answer;
    }

    private function mapToEntity(Answer $answer): \KPG\Learnplaces\persistence\entity\Answer
    {

        /**
         * @var \KPG\Learnplaces\persistence\entity\Answer $activeRecord
         */
        $activeRecord = new \KPG\Learnplaces\persistence\entity\Answer($answer->getId());
        $picture = $answer->getPicture();
        if(!is_null($picture)) {
            $activeRecord->setFkPictureId($picture->getId());
        }

        $activeRecord
            ->setPkId($answer->getId())
            ->setTitle($answer->getTitle())
            ->setContent($answer->getContent())
            ->setCreateDate($answer->getCreateDate()->getTimestamp())
            ->setFkIluserId($answer->getUserId());

        return $activeRecord;
    }

}
