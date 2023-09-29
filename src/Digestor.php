<?php

namespace App;

use App\Entities\Contracts\IEntity;
use App\Entities\Living\Humans\Worker;
use App\Entities\Structures\SmallHouse;
use App\Periods\TimesOfYear;
use App\Profile\GameState;

class Digestor
{
    protected array $entities;
    protected TimesOfYear $timesOfYear;
    protected int $currentYear;
    protected GameState $gameState;

    public function __construct(GameState $gameState, array $entities, TimesOfYear $timesOfYear, int $currentYear)
    {
        $this->gameState = $gameState;
        $this->entities = $entities;
        $this->timesOfYear = $timesOfYear;
        $this->currentYear = $currentYear;
    }

    public function digestEntities()
    {
        foreach ($this->entities as $key => $entity) {

            if ($entity->isDead()) {
                if ($entity instanceof Worker) {
                    $this->gameState->addDeadWorkerToGraveyard($entity);
                    $this->gameState->decrementTotalWorkersOwned();
                }

                unset($this->entities[$key]);
                continue;
            }

            $entity->digestPeriod($this->timesOfYear->getCurrentPeriod(), $this->gameState);
        }

        $this->currentYear++;
    }

    public function addEntity(IEntity $entity): void
    {
        $this->entities[] = $entity;

        if ($entity instanceof Worker) {
            $this->gameState->incrementTotalWorkersOwned();
        }

        if ($entity instanceof SmallHouse) {
            $this->gameState->incrementTotalHousesOwned();
        }
    }

    public function getTimesOfYear(): TimesOfYear
    {
        return $this->timesOfYear;
    }

    public function getWorkers(): array
    {
        return array_values(array_filter($this->entities, function (IEntity $entity) {
            return $entity instanceof Worker;
        }));
    }

    public function getSmallHouses(): array
    {
        return array_values(array_filter($this->entities, function (IEntity $entity) {
            return $entity instanceof SmallHouse;
        }));
    }
}