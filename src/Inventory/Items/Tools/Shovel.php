<?php

namespace App\Inventory\Items\Tools;

use App\Inventory\Items\Contracts\IGiveGoldIncome;
use App\Inventory\Items\Contracts\IInventoryItem;

class Shovel extends BaseTool
{

    protected int $incomeModifier = 3;
    protected int $maxHitPoints = 20;
    protected int $currentHitPoints = 20;
    protected int $weight = 4 ;

}