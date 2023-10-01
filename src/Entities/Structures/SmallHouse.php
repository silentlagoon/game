<?php

namespace App\Entities\Structures;

class SmallHouse extends BaseStructureEntity
{
    protected int $maxHitPoints = 1000;
    protected int $currentHitPoints = 1000;
    protected int $earnsGoldPerPeriod = 10;
    protected bool $canCollapse = true;
    protected int $entityCost = 50;
}