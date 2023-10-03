<?php

namespace App\State\ObjectActions;

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
                    $entity = $this->entitiesFactory->createEntityOfType($name, $this->gameState);
                    $entity->getMoveOptions()->setTexture($this->gameTextures->getEntityTexture($entity));
                    $this->digestor->addEntity($entity);
                }
            }
        }
    }
}