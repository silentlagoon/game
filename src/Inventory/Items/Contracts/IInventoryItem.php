<?php

namespace App\Inventory\Items\Contracts;

interface IInventoryItem
{
    public function getWeightValue(): int;
}