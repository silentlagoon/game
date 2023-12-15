<?php

namespace App\Inventory;

<<<<<<< Updated upstream

use App\Exceptions\Profile\TooMuchWeightToCarryException;
use App\Inventory\Items\Contracts\IInventoryItem;
class Equipment
{
    protected array $equippedItems = [];
    protected int $weightCapacity = 12;

    public function getWeightCapacity(): int
    {
        return $this->weightCapacity;
    }

    public function setWeightCapacity(int $weightCapacity): void
    {
        $this->weightCapacity = $weightCapacity;
    }

=======
use App\Entities\Contracts\IEntity;
use App\Inventory\Items\Contracts\IGiveGoldIncome;
use App\Inventory\Items\Contracts\IInventoryItem;
use App\Inventory\Items\Tools\BaseTool;

class Equipment
{
    protected array $equippedItems = [];
>>>>>>> Stashed changes
    public function all(): array
    {
        return $this->equippedItems;
    }

<<<<<<< Updated upstream
    /**
     * @throws TooMuchWeightToCarryException
     */
    public function addItem(IInventoryItem $item): void
    {

        if ($this->getCurrentWeightValue() + $item->getWeightValue() < $this->weightCapacity) {
   
            $this->equippedItems[(new \ReflectionClass($item))->getName()][] = $item;

        } else {
            throw new TooMuchWeightToCarryException('You are overweight!');
        }
    }

    protected function getCurrentWeightValue(): int
    {
        $totalWeight = 0;
        $items = $this->all();
        foreach ($items as $item) {

            if($item instanceof IInventoryItem) {
                $weight = $item->getWeightValue();
                $totalWeight = $totalWeight + $weight;

            }
        }
        return $totalWeight;
    }

}

=======
    public function addItem(IInventoryItem $item): void
    {
            $this->equippedItems[$item->getName()] = $item;
            $item->setIsEquipped(true);
    }

    public function removeItem(IInventoryItem $item): void
    {
        unset($this->equippedItems[$item->getName()]);
    }

    public function getItem(string $inventoryItemName): ?IInventoryItem
    {
        return $this->equippedItems[$inventoryItemName] ?? null;
    }

    public function getItemByType(string $workType): void
    {
        foreach ($this->equippedItems as $item) {

            if ($item instanceof IGiveGoldIncome && $item->getWorkType() === $workType ){
                $item->setIsBeingUsed(true);
            }

        }
    }

    public function getWeightProperty(IInventoryItem $item): int
    {
        return $item->getWeightValue();
    }


}
>>>>>>> Stashed changes
