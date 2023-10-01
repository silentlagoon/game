<?php

namespace App\Entities\Living\Humans;

use App\Entities\Living\BaseLivingEntity;

class Marauder extends BaseLivingEntity
{
    protected int $maxHitPoints = 100;
    protected int $currentHitPoints = 100;
}