<?php

namespace App\Entities;

use App\Entities\Contracts\IEntity;
use App\Entities\Living\Humans\Worker;
use App\State\GameState;
use Nubs\RandomNameGenerator\All;

class EntitiesFactory
{
    /**
     * @param IEntity $entity
     * @param GameState $gameState
     * @param int|null $hitPoints
     * @param string|null $name
     * @return IEntity
     */
    public function createEntityOfType(
        string $entity,
        GameState $gameState,
        ?int $hitPoints = null,
        string $name = null
    ): IEntity
    {
        $entity = new $entity($gameState);

        if (!is_null($hitPoints)) {
            $entity->setCurrentHitPoints($hitPoints);
        }

        if (!empty($name)) {
            $entity->setName($name);
        }

        if ($entity instanceof Worker) {
            $namesGenerator = All::create();
            $entity->setName($namesGenerator->getName());
        }

        return $entity;
    }
}