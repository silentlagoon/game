<?php

namespace App\Inventory\Items\Clothes;

use App\Inventory\Items\Contracts\IInventoryItem;

class Coat extends BaseClothes
{
    protected string $name = 'Coat';
    protected int $coldProtection = 2;
    protected int $weight = 3;

}