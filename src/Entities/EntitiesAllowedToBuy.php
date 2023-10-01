<?php

namespace App\Entities;

use App\Entities\Living\Animals\Cow;
use App\Entities\Living\Humans\Worker;
use App\Entities\Structures\SmallHouse;

class EntitiesAllowedToBuy
{
    public static function get(): array
    {
        return [
            Cow::class,
            Worker::class,
            SmallHouse::class
        ];
    }
}