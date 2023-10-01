<?php

namespace App\State\ObjectActions;

use App\Enums\Sounds;
use const raylib\KeyboardKey\KEY_BACKSPACE;
use const raylib\KeyboardKey\KEY_ENTER;
use const raylib\MouseCursor\MOUSE_CURSOR_DEFAULT;
use const raylib\MouseCursor\MOUSE_CURSOR_IBEAM;

class UsernameFormAction extends ApplicationObjectAction
{
    public function handle()
    {
        $isMouseOnText = CheckCollisionPointRec(
            GetMousePosition(),
            $this->gameState->getGameStateObjects()->getObject(UsernameFormAction::class)
        );

        if ($isMouseOnText) {
            SetMouseCursor(MOUSE_CURSOR_IBEAM);

            $charPressed = GetCharPressed();

            while($charPressed > 0) {
                UpdateMusicStream(
                    $this->gameState->getGameStateSounds()
                        ->getObject(Sounds::KEYBOARD_SOUND()->getValue())
                );

                $currentProfileUserName = $this->gameState->getUserName();
                $currentProfileUserName .= chr($charPressed);
                $this->gameState->setUserName($currentProfileUserName);

                $charPressed = GetCharPressed();
            }

            if (IsKeyPressed(KEY_BACKSPACE)) {
                UpdateMusicStream(
                    $this->gameState->getGameStateSounds()
                        ->getObject(Sounds::KEYBOARD_SOUND()->getValue())
                );

                $currentProfileUserName = $this->gameState->getUserName();

                if (!empty($currentProfileUserName)) {
                    $this->gameState->setUserName(substr($this->gameState->getUserName(), 0, -1));
                }
            }

            if (IsKeyPressed(KEY_ENTER)) {
                $this->gameState->setIsUserNameBeenSet(true);
                SetMouseCursor(MOUSE_CURSOR_DEFAULT);
            }
        } else {
            SetMouseCursor(MOUSE_CURSOR_DEFAULT);
        }
    }
}