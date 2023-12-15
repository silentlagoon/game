<?php

namespace App\Inventory\Items\Tools;

use App\Inventory\Items\Contracts\IGiveGoldIncome;
use App\Inventory\Items\Contracts\IInventoryItem;

class Hammer extends BaseTool
{

    protected string $workType = 'Building';
    protected int $incomeModifier = 2;
    protected int $maxHitPoints = 20;
    protected int $currentHitPoints = 20;
    protected int $weight = 3;

}