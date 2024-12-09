<?php

namespace KPG\Learnplaces\service\publicapi\block;

use InvalidArgumentException;
use KPG\Learnplaces\service\publicapi\model\PictureUploadBlockModel;

/**
 * Interface PictureUploadBlockService
 *
 * @package KPG\Learnplaces\service\publicapi\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
interface PictureUploadBlockService
{
    /**
     * Updates a given picture upload block or creates a new one if the picture upload block with the given id was not found.
     *
     * @param PictureUploadBlockModel $blockModel The picture upload block which should be updated or created.
     *
     * @return PictureUploadBlockModel            The newly updated or created picture upload block model.
     */
    public function store(PictureUploadBlockModel $blockModel): PictureUploadBlockModel;


    /**
     * Deletes a picture upload block by id.
     *
     * @param int $id   The id which should be used to delete the picture upload block.
     *
     * @return void
     * @throws InvalidArgumentException
     *                  Thrown if the picture upload block with the given id was not found.
     */
    public function delete(int $id);


    /**
     * Searches a picture upload block by id.
     *
     * @param int $id               The id which should be used to search the picture upload block.
     *
     * @return PictureUploadBlockModel    The picture upload block with the given id.
     */
    public function find(int $id): PictureUploadBlockModel;
}
