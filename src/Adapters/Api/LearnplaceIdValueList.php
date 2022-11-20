<?php

namespace fluxlabs\learnplaces\Adapters\Api;

use fluxlabs\learnplaces\Core\Domain;

class LearnplaceIdValueList
{
    public array $items = [];

    public static function fromLearnplaces(array $learnplaces) : LearnplaceIdValueList
    {
        $obj = new self();
        foreach ($learnplaces as $learnplace) {
            $obj->appendItem($learnplace);
        }
        return $obj;
    }

    private function __construct()
    {

    }

    public function appendItem(Domain\Models\Learnplace $learnplace)
    {
        $idValueObject = new  class {

            public static function fromLearnplace(Domain\Models\Learnplace $learnplace) : IdValue
            {
                return IdValue::new(
                    'xsrl_id',
                    $learnplace->ref_id,
                    $learnplace->title->value
                );
            }
        };

        $this->items[$learnplace->ref_id] = $idValueObject::fromLearnplace($learnplace);
    }

}