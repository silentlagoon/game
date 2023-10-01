<?php

namespace App\Entities\Living\Animals;

use App\Entities\Living\BaseLivingEntity;

class Cow extends BaseLivingEntity
{
    protected int $maxHitPoints = 75;
    protected int $currentHitPoints = 75;
    protected int $earnsGoldPerPeriod = 2;
    protected int $entityCost = 15;
}