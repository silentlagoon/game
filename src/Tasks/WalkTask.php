<?php

namespace App\Tasks;

use App\Entities\Contracts\IEntity;
use App\Tasks\Contracts\ITask;
use raylib\Rectangle;
use raylib\Vector2;

class WalkTask implements ITask
{
    protected Vector2 $direction;

    public function handle(IEntity $entity)
    {
        if (!$entity->canMove()) {
            return;
        }

        $moveOptions = $entity->getMoveOptions();
        $hitPointsOptions = $entity->getEntityHitPointsOptions();

        dump('position', $moveOptions->getPosition());
        dump('direction', $this->getDirection());

        if (
            $moveOptions->getPosition()->x === $this->getDirection()->x &&
            $moveOptions->getPosition()->y === $this->getDirection()->y
        ) {
            $entity->setTask(null);
            return;
        }

        if($moveOptions->getPosition()->x > $this->getDirection()->x) {
            $entityPositionX = $moveOptions->getPosition()->x - $moveOptions->getSpeed()->x;
        } else if ($moveOptions->getPosition()->x < $this->getDirection()->x){
            $entityPositionX = $moveOptions->getPosition()->x + $moveOptions->getSpeed()->x;
        } else {
            $entityPositionX = $moveOptions->getPosition()->x;
        }

        if ($moveOptions->getPosition()->y > $this->getDirection()->y) {
            $entityPositionY = $moveOptions->getPosition()->y - $moveOptions->getSpeed()->y;
        } elseif ($moveOptions->getPosition()->y < $this->getDirection()->y) {
            $entityPositionY = $moveOptions->getPosition()->y + $moveOptions->getSpeed()->y;
        } else {
            $entityPositionY = $moveOptions->getPosition()->y;
        }

        $moveOptions->setPosition(new Vector2($entityPositionX, $entityPositionY));

        $hitPointsBar = $hitPointsOptions->getBar();

        $hitPointsOptions->setBar(new Rectangle(
            $entityPositionX + 5,
            $entityPositionY - 7,
            $hitPointsBar->width,
            $hitPointsBar->height
        ));
    }

    /**
     * @return Vector2
     */
    public function getDirection(): Vector2
    {
        return $this->direction;
    }

    /**
     * @param Vector2 $direction
     */
    public function setDirection(Vector2 $direction): void
    {
        $this->direction = $direction;
    }
}