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

        if (
            $moveOptions->getPosition()->x === $this->getDirection()->x &&
            $moveOptions->getPosition()->y === $this->getDirection()->y
        ) {
            $entity->setTask(null);
            return;
        }

        //We don`t know if initial speed positive or negative
        //So we take absolute speed
        $entitySpeedX = abs($moveOptions->getSpeed()->x);
        $entitySpeedY = abs($moveOptions->getSpeed()->y);

        if ($moveOptions->getPosition()->x > $this->getDirection()->x) {
            $entitySpeedX *= -1;
        }

        if ($moveOptions->getPosition()->y > $this->getDirection()->y) {
            $entitySpeedY *= -1;
        }

        if ($moveOptions->getPosition()->x === $this->getDirection()->x) {
            $entitySpeedX = 0;
        }

        if ($moveOptions->getPosition()->y === $this->getDirection()->y) {
            $entitySpeedY = 0;
        }

        $newVectorPositionX = $moveOptions->getPosition()->x + $entitySpeedX;
        $newVectorPositionY = $moveOptions->getPosition()->y + $entitySpeedY;

        $predictX = abs($newVectorPositionX - $this->getDirection()->x);
        $predictY = abs($newVectorPositionY - $this->getDirection()->y);

        if ($predictX < $moveOptions->getSpeed()->x) {
            $newVectorPositionX = $this->getDirection()->x;
        }

        if ($predictY < $moveOptions->getSpeed()->y) {
            $newVectorPositionY = $this->getDirection()->y;
        }

        $moveOptions->getPosition()->x = $newVectorPositionX;
        $moveOptions->getPosition()->y = $newVectorPositionY;

        $hitPointsBar = $hitPointsOptions->getBar();
        $hitPointsBar->x = $newVectorPositionX + 5;
        $hitPointsBar->y = $newVectorPositionY - 7;

//        $hitPointsOptions->setBar(new Rectangle(
//            $moveOptions->getPosition()->x + 5,
//            $moveOptions->getPosition()->y - 7,
//            $hitPointsBar->width,
//            $hitPointsBar->height
//        ));
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