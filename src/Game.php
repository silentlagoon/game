<?php

namespace App;

use App\Entities\EntitiesFactory;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\Profile\GameState;
use raylib\Color;
use raylib\Rectangle;
use const raylib\KeyboardKey\KEY_BACKSPACE;
use const raylib\KeyboardKey\KEY_ENTER;
use const raylib\KeyboardKey\KEY_SPACE;
use const raylib\MouseCursor\MOUSE_CURSOR_DEFAULT;
use const raylib\MouseCursor\MOUSE_CURSOR_IBEAM;

class Game
{
    const SCREEN_WIDTH  = 800;
    const SCREEN_HEIGHT = 450;
    const TARGET_FPS = 60;

    const WORKERS_UI_STEPPING_Y = 20;

    protected Color $colorLightGray;
    protected Color $colorGray;

    protected GameState $gameState;
    protected Digestor $digestor;
    protected EntitiesFactory $entitiesFactory;

    public function __construct(GameState $gameState, Digestor $digestor, EntitiesFactory $entitiesFactory)
    {
        $this->gameState = $gameState;
        $this->digestor = $digestor;
        $this->entitiesFactory = $entitiesFactory;

        $this->init();
    }

    protected function init()
    {
        $this->colorLightGray = new Color(245, 245, 245, 255);
        $this->colorGray = new Color(200, 200, 200, 255);
    }

    public function start()
    {
        InitWindow(static::SCREEN_WIDTH, static::SCREEN_HEIGHT, 'The Game');
        SetTargetFPS(static::TARGET_FPS);

        $formRectangle = null;
        $framesCounter = 0;
        $gameTicks = 1;

        while (!WindowShouldClose()) {

            //UPDATE PHASE
            if (IsKeyPressed(KEY_SPACE)) {
                $this->gameState->isPaused() ?
                    $this->gameState->continueGame() :
                    $this->gameState->pauseGame();
            }

            if (!$this->gameState->isUserNameBeenSet()) {
                $formRectangle = new Rectangle(270, 200, 225, 50);

                if ($isMouseOnText = CheckCollisionPointRec(GetMousePosition(), $formRectangle)) {
                    SetMouseCursor(MOUSE_CURSOR_IBEAM);

                    $charPressed = GetCharPressed();

                    while($charPressed > 0) {
                        $currentProfileUserName = $this->gameState->getUserName();
                        $currentProfileUserName .= chr($charPressed);
                        $this->gameState->setUserName($currentProfileUserName);

                        $charPressed = GetCharPressed();
                    }

                    if (IsKeyPressed(KEY_BACKSPACE)) {
                        $currentProfileUserName = $this->gameState->getUserName();

                        if (!empty($currentProfileUserName)) {
                            $this->gameState->setUserName(substr($this->gameState->getUserName(), 0, -1));
                        }
                    }

                    if (IsKeyPressed(KEY_ENTER)) {
                        $this->gameState->setIsUserNameBeenSet(true);
                    }
                } else {
                    SetMouseCursor(MOUSE_CURSOR_DEFAULT);
                }

                if ($isMouseOnText) {
                    $framesCounter++;
                } else {
                    $framesCounter = 0;
                }
            }

            //DRAW PHASE
            BeginDrawing();

                ClearBackground($this->colorLightGray);

                if ($this->gameState->isPaused()) {
                    DrawText("PAUSED", 350, 200, 30, Color::GRAY());
                } else {
                    if (!$this->gameState->isUserNameBeenSet()) {
                        DrawText('Enter your user name:', 260, 140, 20, $this->colorGray);
                        DrawRectangleRec($formRectangle, $this->colorLightGray);

                        if ($isMouseOnText) {
                            DrawRectangleLines(
                                (int) $formRectangle->x,
                                (int) $formRectangle->y,
                                (int) $formRectangle->width,
                                (int) $formRectangle->height,
                                Color::RED()
                            );
                        } else {
                            DrawRectangleLines(
                                (int) $formRectangle->x,
                                (int) $formRectangle->y,
                                (int) $formRectangle->width,
                                (int) $formRectangle->height,
                                $this->colorGray
                            );
                        }

                        DrawText($this->gameState->getUserName(), (int) $formRectangle->x + 5, (int) $formRectangle->y + 8, 40, Color::RED());

                        if ($isMouseOnText) {
                            DrawText("_", (int) $formRectangle->x + 8 + MeasureText($this->gameState->getUserName(), 40), (int) $formRectangle->y + 12, 40, Color::RED());
                            DrawText('Press BACKSPACE to delete chars...', 230, 300, 20, $this->colorGray);
                        }
                    }

                    //DRAW UI
                    if ($this->gameState->isUserNameBeenSet()) {
                        $this->drawUI($gameTicks);
                        $gameTicks++;
                    }
                }

            EndDrawing();
        }

        CloseWindow();
    }

    protected function drawUI(int &$gameTicks)
    {
        if ($gameTicks / 100 > 5) {
            $this->digestor->digestEntities();
            $this->digestor->getTimesOfYear()->moveToNextPeriod();
            $gameTicks = 1;
        }

        DrawText(sprintf('Welcome %s', $this->gameState->getUserName()), 5, 0, 20, Color::GREEN());
        DrawText(sprintf('Current season is: %s', $this->digestor->getTimesOfYear()->getCurrentPeriod()->getName()), 150, 0, 20, Color::GREEN());
        DrawText(sprintf('Day: %d', $gameTicks / 100), 500, 0, 20, Color::GREEN());
        DrawText(sprintf('Year: %d', $this->digestor->getTimesOfYear()->getCurrentYear()), 600, 0, 20, Color::GREEN());
        DrawText(sprintf('Gold: %d', $this->gameState->getCurrentGoldAmount()), 5, 30, 20, Color::BLUE());
        DrawText(sprintf('Workers: %d', $this->gameState->getTotalWorkersOwned()), 5, 50, 20, Color::BLUE());

        $initialWorkersPositionY = 70;
        foreach ($this->digestor->getWorkers() as $worker) {
            DrawText(
                sprintf('%s: HP=%d, Income per Season: %d',
                    $worker->getName(),
                    $worker->getCurrentHitPoints(),
                    $worker->getGoldEarningsPerPeriod()
                ),
                25,
                $initialWorkersPositionY,
                20,
                Color::BLUE()
            );

            $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;
        }

        DrawText(sprintf('Houses: %d', $this->gameState->getTotalHousesOwned()), 5, $initialWorkersPositionY, 20, Color::BLUE());

        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        foreach ($this->digestor->getSmallHouses() as $smallHouse) {
            DrawText(
                sprintf('%s: HP=%d, Income per Season: %d',
                    $smallHouse->getName(),
                    $smallHouse->getCurrentHitPoints(),
                    $smallHouse->getGoldEarningsPerPeriod()
                ),
                25,
                $initialWorkersPositionY,
                20,
                Color::BLUE()
            );
            $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;
        }

        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        $deadWorkers = $this->gameState->getDeadWorkers();
        DrawText(sprintf('Dead Workers: %d', count($deadWorkers)), 5, $initialWorkersPositionY, 20, Color::MAROON());

        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        foreach ($deadWorkers as $deadWorker) {
            DrawText(
                sprintf('%s: HP=%d Income per Season: %d',
                    $deadWorker->getName(),
                    $deadWorker->getCurrentHitPoints(),
                    $deadWorker->getGoldEarningsPerPeriod()
                ),
                25,
                $initialWorkersPositionY,
                20,
                Color::MAROON()
            );
            $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;
        }
    }
}