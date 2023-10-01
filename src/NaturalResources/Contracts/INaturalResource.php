<?php

namespace App\NaturalResources\Contracts;

interface INaturalResource
{
    public function getName(): string;
    public function isConsumable(): bool;
    public function getFoodValue(): int;
    public function getSellCost(): int;
    public function getProducedQuantity(): int;
    public function getResourceTypeName(): string;
}