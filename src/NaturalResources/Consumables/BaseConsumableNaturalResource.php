<?php

namespace App\NaturalResources\Consumables;

use App\NaturalResources\BaseNaturalResource;

class BaseConsumableNaturalResource extends BaseNaturalResource
{
    protected bool $isConsumable = true;
    protected string $resourceTypeName = 'Consumable';
}