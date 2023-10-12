<?php

namespace App\Inventory\Items;

use App\Inventory\Items\Contracts\IGiveGoldIncome;
use App\Inventory\Items\Contracts\IInventoryItem;

class PickAxe implements IInventoryItem,IGiveGoldIncome
{

    protected int $incomeModifier = 3;
    protected int $weight = 4 ;

    public function getIncomeModifier(): int
    {
        return $this->incomeModifier;
    }
    public function getWeightValue(): int
    {
        return $this->weight;
    }

}