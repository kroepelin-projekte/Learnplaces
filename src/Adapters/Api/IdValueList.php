<?php

namespace fluxlabs\learnplaces\Adapters\Api;

use fluxlabs\learnplaces\Core\Domain;

class IdValueList
{
    public array $items = [];

    public static function fromObjetList(array $iliasObjectList) : IdValueList
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
        $idValueObject = new  class {

            public static function fromIliasObject(Domain\Models\IliasObject $object) : IdValue
            {
                return IdValue::new(
                    $object->objectType,
                    $object->id,
                    $object->title
                );
            }
        };

        $this->items[$object->id] = $idValueObject::fromIliasObject($object);
    }

}