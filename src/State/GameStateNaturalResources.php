<?php

namespace App\State;

use App\NaturalResources\Contracts\INaturalResource;

class GameStateNaturalResources extends GameStateEntity
{
    /**
     * @return int
     */
    public function getTotalFoodValue(): int
    {
        $foodValue = 0;
        foreach ($this->getObjects() as $consumableName => $consumableValue) {
            $foodValue += $consumableValue;
        }

        return $foodValue;
    }
}