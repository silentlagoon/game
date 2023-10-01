<?php

namespace App\NaturalResources\Consumables;

class Meat extends BaseConsumableNaturalResource
{
    protected string $name = 'Meat';
    protected int $foodValue = 20;
    protected int $sellCost = 10;
    protected int $producedQuantity = 1;
}