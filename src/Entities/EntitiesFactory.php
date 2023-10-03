<?php

namespace App\Entities;

use App\Entities\Contracts\IEntity;
use App\Entities\Living\Humans\Worker;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\Inventory\Inventory;
use App\Position\EntityHitPointsOptions;
use App\Position\EntityMoveOptions;
use App\State\GameState;
use App\Tasks\TaskQueue;
use Nubs\RandomNameGenerator\All;
use raylib\Rectangle;
use raylib\Vector2;

class EntitiesFactory
{
    /**
     * @param string $entity
     * @param GameState $gameState
     * @param int|null $hitPoints
     * @param string|null $name
     * @throws NotEnoughGoldToSpendException
     * @return IEntity
     */
    public function createEntityOfType(
        string $entity,
        GameState $gameState,
        ?int $hitPoints = null,
        string $name = null
    ): IEntity
    {
        /** @var IEntity $entity */
        $entity = new $entity($gameState);

        $entity->setEntityMoveOptions(new EntityMoveOptions(
            new Vector2($entity->getInitialPositionX(), $entity->getInitialPositionY()),
            new Vector2($entity->getEntitySpeed(), $entity->getEntitySpeed())
        ));

        if (!is_null($hitPoints)) {
            $entity->setCurrentHitPoints($hitPoints);
        }

        $entity->setEntityHitPointsOptions(new EntityHitPointsOptions(
            new Rectangle(
                $entity->getInitialPositionX() + 5,
                $entity->getInitialPositionY() - 7,
                15,
                5
            )
        ));

        if (!empty($name)) {
            $entity->setName($name);
        }

        if ($entity instanceof Worker) {
            $namesGenerator = All::create();
            $entity->setName($namesGenerator->getName());
        }

        $entity->setTaskQueue(new TaskQueue());

        $entity->setInventory(new Inventory());

        return $entity;
    }

    /**
     * @param string $entity
     * @param GameState $gameState
     * @param int|null $hitPoints
     * @param string|null $name
     * @throws NotEnoughGoldToSpendException
     * @return IEntity
     */
    public function createFreeEntityOfType(
        string $entity,
        GameState $gameState,
        ?int $hitPoints = null,
        string $name = null
    ): IEntity
    {
        /** @var IEntity $entity */
        $entity = new $entity($gameState, true);

        if ($entity->canMove()) {
            $entity->setEntityMoveOptions(new EntityMoveOptions(
                new Vector2(0,0),
                new Vector2($entity->getEntitySpeed(), $entity->getEntitySpeed())
            ));
        } else {
            $entity->setEntityMoveOptions(new EntityMoveOptions(
                new Vector2(0, 0),
                new Vector2(0, 0)
            ));
        }

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

        $entity->setTaskQueue(new TaskQueue());

        return $entity;
    }
}