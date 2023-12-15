<?php

namespace App\State;

class GameStateResources extends GameStateEntity
{
    public function getTotalResourceValue(): int
    {
        $foodValue = 0;
        foreach ($this->getObjects() as $consumableName => $consumableValue) {
            $foodValue += $consumableValue;
        }

        return $foodValue;
    }
}