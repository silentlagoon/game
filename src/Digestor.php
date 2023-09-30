<?php

namespace App;

use App\Entities\Contracts\IEntity;
use App\Entities\Living\Humans\Worker;
use App\Entities\Living\Animals\Cow;
use App\Entities\Structures\SmallHouse;
use App\Periods\TimesOfYear;
use App\State\GameState;

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
            $entity->digestPeriod($this->timesOfYear->getCurrentPeriod(), $this->gameState);

            if ($entity->isDead()) {
                $this->removeEntityFromDigest($entity, $key);
            }
        }

        $this->currentYear++;
    }

    protected function removeEntityFromDigest(IEntity $entity, int $key): void
    {
        if ($entity instanceof Worker) {
            $this->gameState->addDeadWorkerToGraveyard($entity);
            $this->gameState->decrementTotalWorkersOwned();
        }

        if ($entity instanceof Cow) {
            $this->gameState->decrementTotalCowsOwned();
        }

        $entity->setDateOfDeath(new GameDate(
            $this->currentYear,
            $this->timesOfYear->getCurrentPeriod(),
            $this->gameState->getDaysFromTicks()
        ));

        unset($this->entities[$key]);
    }

    public function addEntity(IEntity $entity): void
    {
        $this->entities[] = $entity;

        if ($entity instanceof Worker) {
            $this->gameState->incrementTotalWorkersOwned();
        }

        if ($entity instanceof  Cow) {
            $this->gameState->incrementTotalCowsOwned();
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

    public function getCows(): array
    {
        return array_values(array_filter($this->entities,function (IEntity $entity){
            return $entity instanceof Cow;
        }));
    }

    public function getSmallHouses(): array
    {
        return array_values(array_filter($this->entities, function (IEntity $entity) {
            return $entity instanceof SmallHouse;
        }));
    }
}