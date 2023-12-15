<?php

namespace App\Inventory\Items\Tools;

use App\Inventory\Items\Contracts\IGiveGoldIncome;
use App\Inventory\Items\Contracts\IInventoryItem;

class PickAxe extends BaseTool
{

    protected string $workType = 'Mining';
    protected int $incomeModifier = 3;
    protected float $modifier = 1.5;
    protected int $maxHitPoints = 20;
    protected int $currentHitPoints = 20;
    protected int $weight = 4 ;

}