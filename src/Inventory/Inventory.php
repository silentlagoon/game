<?php

namespace App\Inventory;

use App\Inventory\Items\Contracts\IInventoryItem;

class Inventory
{
    /** @var IInventoryItem[] $inventoryItems  */
    protected array $inventoryItems = [];

    public function addItem(IInventoryItem $inventoryItem)
    {
        $this->inventoryItems[(new \ReflectionClass($inventoryItem))->getName()] = $inventoryItem;
    }

    /**
     * @param string $inventoryItemName
     * @return IInventoryItem|null
     */
    public function getItem(string $inventoryItemName): ?IInventoryItem
    {
        return $this->inventoryItems[$inventoryItemName] ?? null;
    }

    /**
     * @return IInventoryItem[]
     */
    public function all(): array
    {
        return $this->inventoryItems;
    }
}