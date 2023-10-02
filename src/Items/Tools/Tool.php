<?php

namespace App\Items\Tools;

use App\Items\BaseItem;

class Tool extends BaseItem
{
    protected string $name = 'Hammer';
    protected bool $isConsumable = false;
    protected float $incomeIndex = 0;
    protected bool $isEquipped = false;
}