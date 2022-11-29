<?php

namespace fluxlabs\learnplaces\Adapters\Api;

use fluxlabs\learnplaces\Core\Domain;

class ObjectItemList
{
    public array $items = [];

    public static function fromObjetList(array $iliasObjectList) : ObjectItemList
    {
        $obj = new self();
        foreach ($iliasObjectList as $object) {
            $obj->appendItem($object);
        }
        return $obj;
    }

    private function __construct()
    {

    }

    public function appendItem(Domain\Models\IliasObject $object)
    {
        $this->items[$object->id] = $object;
    }

}