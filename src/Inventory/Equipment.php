<?php

namespace App\Inventory;


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

    public function all(): array
    {
        return $this->equippedItems;
    }

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

