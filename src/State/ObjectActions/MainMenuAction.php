<?php

namespace App\State\ObjectActions;

use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use const raylib\MouseButton\MOUSE_BUTTON_LEFT;

class MainMenuAction extends ApplicationObjectAction
{
    public function handle()
    {
        $objects = $this->gameState->getGameStateObjects()->getObject(MainMenuAction::class);

        foreach ($objects as $name => $object) {
            if (CheckCollisionPointRec(GetMousePosition(), $object))
            {
                if (IsMouseButtonReleased(MOUSE_BUTTON_LEFT))
                {
                    if ($name === 'Worker') {
                        try {
                            $this->digestor->addEntity($this->entitiesFactory->createWorker('', $this->gameState));
                        } catch (NotEnoughGoldToSpendException $e) {
                            return null;
                        }
                    }

                    if ($name === 'Cow') {
                        try {
                            $this->digestor->addEntity($this->entitiesFactory->createCow( $this->gameState));
                        } catch (NotEnoughGoldToSpendException $e) {
                            return null;
                        }
                    }

                    if ($name === 'SmallHouse') {
                        try {
                            $this->digestor->addEntity($this->entitiesFactory->createSmallHouse($this->gameState));
                        } catch (NotEnoughGoldToSpendException $e) {
                            return null;
                        }
                    }
                }
            }
        }
    }
}