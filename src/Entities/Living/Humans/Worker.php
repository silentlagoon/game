<?php

namespace App\Entities\Living\Humans;

use App\Entities\Living\BaseLivingEntity;
use App\Entities\Living\Humans\Contracts\IPopulation;

class Worker extends BaseLivingEntity implements IPopulation
{
    protected int $maxHitPoints = 100;
    protected int $currentHitPoints = 100;
    protected int $goldIncomePerPeriod = 1;
    protected int $resourceGatheredPerPeriod = 10000;
    protected int $entityCost = 10;

    protected bool $shouldConsumeFood = true;
    protected int $consumeFoodAmount = 2;
    protected int $hungerDamage = 30;

    protected bool $isMovable = true;
    protected float $entitySpeed = 2.0;
}