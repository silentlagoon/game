<?php

namespace App\Resources;

use App\Resources\Contracts\IResources;

class BaseResources implements IResources
{
    protected string $name;
    protected string $resourceTypeName;
    protected int $sellCost;

    public function getName(): string
    {
        return $this->name ?? (new \ReflectionClass($this))->getShortName();
    }

    public function getResourceTypeName(): string
    {
        return $this->resourceTypeName;
    }

    public function getSellCost(): int
    {
        return $this->sellCost;
    }

}