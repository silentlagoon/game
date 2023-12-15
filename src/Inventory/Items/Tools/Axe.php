<?php

namespace App\Inventory\Items\Tools;

class Axe extends BaseTool
{
    protected string $workType = 'Timbering';
    protected int $incomeModifier = 3;
    protected float $modifier = 1.5;
    protected int $maxHitPoints = 20;
    protected int $currentHitPoints = 20;
    protected int $weight = 4 ;
}