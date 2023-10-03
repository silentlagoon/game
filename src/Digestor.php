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
    /** @var $entities IEntity[] */
    protected array $entities;
    protected TimesOfYear $timesOfYear;
    protected int $currentYear;
    protected GameState $gameState;
    /** @var $entitiesSelected IEntity[] */
    protected array $entitiesSelected;

    public function __construct(
        GameState $gameState,
        array $entities,
        array$entitiesSelected,
        TimesOfYear $timesOfYear,
        int $currentYear
    )
    {
        $this->gameState = $gameState;
        $this->entities = $entities;
        $this->entitiesSelected = $entitiesSelected;
        $this->timesOfYear = $timesOfYear;
        $this->currentYear = $currentYear;
    }

    public function digestEntities()
    {
        //Make it more random
        shuffle($this->entities);

        foreach ($this->entities as $key => $entity) {
            $entity->digestPeriod($this->timesOfYear->getCurrentPeriod(), $this->gameState);

            if ($entity->isDead()) {
                $this->removeEntityFromDigest($entity, $key);
            }
        }

        $this->currentYear++;
    }

    public function digestEntitiesTasks()
    {
        foreach ($this->entities as $entity) {
            $entity->digestTasks();
        }
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

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function getUniqueEntities(): array
    {
        return array_unique($this->getEntities());
    }

    public function getEntitiesOfType(IEntity $entityType): array
    {
        return array_values(array_filter($this->entities, function (IEntity $entity) use ($entityType) {
            return $entity instanceof $entityType;
        }));
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
}