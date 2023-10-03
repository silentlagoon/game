<?php

namespace App\Inventory\Items;

use App\Inventory\Items\Contracts\IInventoryItem;

class Coat implements IInventoryItem
{
    protected int $coldProtection = 2;
}