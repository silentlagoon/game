<?php

namespace App\State\ObjectActions;

use App\Entities\Living\Animals\Cow;
use App\Entities\Living\Humans\Worker;
use App\Entities\Structures\SmallHouse;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use const raylib\MouseButton\MOUSE_BUTTON_LEFT;

class MainMenuAction extends ApplicationObjectAction
{

    /**
     * @return void
     * @throws NotEnoughGoldToSpendException
     */
    public function handle()
    {
        $objects = $this->gameState->getGameStateObjects()->getObject(MainMenuAction::class);

        foreach ($objects as $name => $object) {
            if (CheckCollisionPointRec(GetMousePosition(), $object))
            {
                if (IsMouseButtonReleased(MOUSE_BUTTON_LEFT))
                {
                    switch ($name) {
                        case $name === Worker::class:
                            $this->digestor->addEntity($this->entitiesFactory->createWorker($this->gameState));
                            break;
                        case $name === Cow::class:
                            $this->digestor->addEntity($this->entitiesFactory->createCow($this->gameState));
                            break;
                        case $name === SmallHouse::class:
                            $this->digestor->addEntity($this->entitiesFactory->createSmallHouse($this->gameState));
                            break;
                    }
                }
            }
        }
    }
}