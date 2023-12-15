<?php

namespace App\Entities\Living\Humans;

use App\Entities\Living\BaseLivingEntity;

<<<<<<< Updated upstream
class Worker extends BaseLivingEntity
=======
class  Worker extends BaseLivingEntity implements IPopulation
>>>>>>> Stashed changes
{
    protected int $maxHitPoints = 100;
    protected int $currentHitPoints = 100;
    protected int $goldIncomePerPeriod = 1;
<<<<<<< Updated upstream
=======
    protected int $resourceGatheredPerPeriod = 100;
>>>>>>> Stashed changes
    protected int $entityCost = 10;

    protected bool $shouldConsumeFood = true;
    protected int $consumeFoodAmount = 2;
    protected int $hungerDamage = 30;
    protected int $weightCapacity = 10;

    protected bool $isMovable = true;
    protected float $entitySpeed = 2.0;

    protected  bool $canUseEquipment = true;
}