<?php

namespace App\Entities\Living\Animals;

use App\Entities\Living\BaseLivingEntity;
use App\NaturalResources\Consumables\Meat;
use App\NaturalResources\Consumables\Milk;

class Cow extends BaseLivingEntity
{
    protected int $maxHitPoints = 75;
    protected int $currentHitPoints = 75;
    protected int $goldIncomePerPeriod = 2;
    protected int $entityCost = 15;

    protected bool $canProduceNaturalResources = true;
    protected array $produceNaturalResourcesCollection = [Milk::class, Meat::class];

    protected bool $isMovable = true;
    protected float $entitySpeed = 1.0;
}