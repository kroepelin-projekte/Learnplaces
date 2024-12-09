<?php

namespace KPG\Learnplaces\persistence\repository;

use KPG\Learnplaces\persistence\dto\Learnplace;
use KPG\Learnplaces\persistence\dto\PictureBlock;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

/**
 * Interface PictureBlockRepository
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
interface PictureBlockRepository {

	/**
	 * Updates the picture block or creates a new one if the id of the picture block was not found.
	 *
	 * @param PictureBlock $pictureBlock    The picture block which should be updated or created.
	 *
	 * @return PictureBlock                 The newly created or updated picture block.
	 */
	public function store(PictureBlock $pictureBlock): PictureBlock;


	/**
	 * Searches a picture block by block id.
	 *
	 * @param int $id       The block id which should be used to fetch the picture block.
	 *
	 * @return PictureBlock The picture block with the given block id.
	 *
	 * @throws EntityNotFoundException
	 *                      Thrown if the picture block with the given id was not found.
	 */
	public function findByBlockId(int $id): PictureBlock;


	/**
	 * Removes the picture block with the given id.
	 *
	 * @param int $id   The id of the block which should be removed.
	 *
	 * @return void
	 *
	 * @throws EntityNotFoundException
	 *                  Thrown if the picture block with the given id was not found.
	 */
	public function delete(int $id);


	/**
	 * Searches all picture blocks which belong to the given learnplace.
	 *
	 * @param Learnplace $learnplace    The learnplace which should be used to search all the corresponding picture blocks.
	 *
	 * @return PictureBlock[]           All found picture blocks which belong to the given learnplace.
	 */
	public function findByLearnplace(Learnplace $learnplace): array;
}