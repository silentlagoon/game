<?php

namespace App\Workplaces;

use App\State\GameState;

class WorkplacesFactory
{
    public function createWorkplaceOfType(string $type, GameState $gameState)
    {
        return new $type($gameState);
    }
}