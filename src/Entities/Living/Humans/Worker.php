<?php

namespace App\Entities\Living\Humans;

use App\Entities\Living\BaseLivingEntity;

class Worker extends BaseLivingEntity
{
    protected int $maxHitPoints = 100;
    protected int $currentHitPoints = 100;
    protected int $goldIncomePerPeriod = 1;
    protected int $entityCost = 10;

    protected bool $shouldConsumeFood = true;
    protected int $consumeFoodAmount = 2;
    protected int $hungerDamage = 30;

    protected bool $isMovable = true;
    protected float $entitySpeed = 2.0;
}