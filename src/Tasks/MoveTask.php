<?php

namespace App\Tasks;

use App\Entities\Contracts\IEntity;
use App\Tasks\Contracts\ITask;
use raylib\Rectangle;
use raylib\Vector2;

class MoveTask implements ITask
{
    public function handle(IEntity $entity)
    {
        if ($entity->canMove()) {
            return;
        }

        $moveOptions = $entity->getMoveOptions();
        $hitPointsOptions = $entity->getEntityHitPointsOptions();
        $mousePosition = GetMousePosition();

        $moveOptions->setPosition($mousePosition);

        $hitPointsBar = $hitPointsOptions->getBar();

        $hitPointsOptions->setBar(new Rectangle(
            $mousePosition->x + 5,
            $mousePosition->y - 7,
            $hitPointsBar->width,
            $hitPointsBar->height
        ));
    }
}