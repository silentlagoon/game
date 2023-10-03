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

        $entityPositionX = $moveOptions->getPosition()->x;

        if($moveOptions->getPosition()->x > $this->getDirection()->x) {

            $resultSpeedX = $moveOptions->getSpeed()->x;
            $measureDistanceToPoint = $moveOptions->getPosition()->x - $this->getDirection()->x;

            if ($measureDistanceToPoint < $moveOptions->getSpeed()->x) {
                $entityPositionX = $this->getDirection()->x;
            } else {
                $entityPositionX = $moveOptions->getPosition()->x - $resultSpeedX;
            }
        } else if ($moveOptions->getPosition()->x < $this->getDirection()->x) {

            $resultSpeedX = $moveOptions->getSpeed()->x;
            $measureDistanceToPoint = $this->getDirection()->x - $moveOptions->getPosition()->x;

            if ($measureDistanceToPoint < $moveOptions->getSpeed()->x) {
                $entityPositionX = $this->getDirection()->x;
            } else {
                $entityPositionX = $moveOptions->getPosition()->x + $resultSpeedX;
            }
        }

        $entityPositionY = $moveOptions->getPosition()->y;

        if ($moveOptions->getPosition()->y > $this->getDirection()->y) {

            $resultSpeedY = $moveOptions->getSpeed()->y;
            $measureDistanceToPoint = $moveOptions->getPosition()->y - $this->getDirection()->y;

            if ($measureDistanceToPoint < $moveOptions->getSpeed()->y) {
                $entityPositionY = $this->getDirection()->y;
            } else {
                $entityPositionY = $moveOptions->getPosition()->y - $resultSpeedY;
            }
        } elseif ($moveOptions->getPosition()->y < $this->getDirection()->y) {

            $resultSpeedY = $moveOptions->getSpeed()->y;
            $measureDistanceToPoint = $this->getDirection()->y - $moveOptions->getPosition()->y;

            if ($measureDistanceToPoint < $moveOptions->getSpeed()->y) {
                $entityPositionY = $this->getDirection()->y;
            } else {
                $entityPositionY = $moveOptions->getPosition()->y + $resultSpeedY;
            }
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