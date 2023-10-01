<?php

namespace App\NaturalResources;

use App\NaturalResources\Contracts\INaturalResource;

abstract class BaseNaturalResource implements INaturalResource
{
    protected string $name;
    protected string $resourceTypeName;
    protected bool $isConsumable = false;
    protected int $foodValue = 0;
    protected int $sellCost;
    protected int $producedQuantity = 0;

    public function getName(): string
    {
        return $this->name ?? (new \ReflectionClass($this))->getShortName();
    }

    public function isConsumable(): bool
    {
        return $this->isConsumable;
    }

    public function getFoodValue(): int
    {
        return $this->foodValue;
    }

    public function getSellCost(): int
    {
        return $this->sellCost;
    }

    public function getProducedQuantity(): int
    {
        return $this->producedQuantity;
    }

    public function getResourceTypeName(): string
    {
        return $this->resourceTypeName;
    }
}