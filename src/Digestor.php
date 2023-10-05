<?php

namespace App;

use App\Entities\Contracts\IEntity;
use App\Entities\Living\Humans\Contracts\IPopulation;
use App\Entities\Living\Humans\Worker;
use App\Entities\Living\Animals\Cow;
use App\Entities\Structures\SmallHouse;
use App\Periods\TimesOfYear;
use App\State\GameState;
use App\Workplaces\Contracts\IWorkplace;
use http\Exception\RuntimeException;

class Digestor
{
    /** @var $entities IEntity[] */
    protected array $entities;
    protected TimesOfYear $timesOfYear;
    protected int $currentYear;
    protected GameState $gameState;
    /** @var $entitiesSelected IEntity[] */
    protected array $entitiesSelected;
    /** @var $workplaces IWorkplace[]  */
    protected array $workplaces;

    public function __construct(
        GameState $gameState,
        array $entities,
        array$entitiesSelected,
        TimesOfYear $timesOfYear,
        array $workplaces
    )
    {
        $this->gameState = $gameState;
        $this->entities = $entities;
        $this->entitiesSelected = $entitiesSelected;
        $this->timesOfYear = $timesOfYear;
        $this->workplaces = $workplaces;
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
    }

    public function digestWorkplaces()
    {
        foreach ($this->workplaces as $workplace) {
            $workplace->digestPeriod($this->timesOfYear->getCurrentPeriod());

            if ($workplace->isEmpty()) {
                $this->removeEntitiesFromWorkplace($workplace);
            }
        }
    }

    public function digestEntitiesTasks()
    {
        foreach ($this->entities as $entity) {
            $entity->digestTasks();
        }
    }

    public function getPopulation(): int
    {
        $population = array_filter($this->entities, function (IEntity $entity) {
            return $entity instanceof IPopulation;
        });

        return count($population);
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

        if ($entity instanceof IPopulation) {
            $this->gameState->incrementPopulation();
        }
    }

    public function addWorkplace(IWorkplace $workplace)
    {
        $this->workplaces[] = $workplace;
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
            $this->getTimesOfYear()->getCurrentYear(),
            $this->timesOfYear->getCurrentPeriod(),
            $this->gameState->getDaysFromTicks()
        ));

        unset($this->entities[$key]);
    }

    public function addEntityToWorkplace(IEntity $worker, IWorkplace $workplace): void
    {
        $result = null;

        foreach ($this->entities as $key => $entity) {
            if ($entity->getName() === $worker->getName()) {
                $result = $entity;
                unset($this->entities[$key]);
            }
        }

        if (is_null($result)) {
            throw new RuntimeException('Could not find worker at digestor entities array');
        }

        $workplace->addWorker($result);
    }

    protected function removeEntitiesFromWorkplace(IWorkplace $workplace): void
    {
        foreach ($workplace->getWorkers() as $entity) {
            $this->addEntity($entity);
        }

        $workplace->removeWorkers();
    }
}