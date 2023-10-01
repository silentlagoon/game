<?php

namespace App\NaturalResources\Consumables;

class Milk extends BaseConsumableNaturalResource
{
    protected string $name = 'Milk';
    protected int $foodValue = 10;
    protected int $sellCost = 5;
    protected int $producedQuantity = 5;
}