<?php

namespace App;

use App\Entities\EntitiesFactory;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\State\GameState;
use App\State\ObjectActions\MainMenuAction;
use App\State\ObjectActions\UsernameFormAction;
use raylib\Color;
use raylib\Rectangle;
use const raylib\KeyboardKey\KEY_SPACE;

class Game
{
    const SCREEN_WIDTH  = 800;
    const SCREEN_HEIGHT = 450;
    const TARGET_FPS = 60;
    const INVENTORY_POSITION_Y = 30;
    const WORKERS_UI_STEPPING_Y = 20;

    const USERNAME_FORM = 'username_form';

    protected Color $colorLightGray;
    protected Color $colorGray;

    protected GameTextures $gameTextures;

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

    protected function loadTextures(): GameTextures
    {
        //TODO:: Scan and load all image from assets directory
        $images = [
            'worker' => [
                'alive' => [
                    'path' => 'assets/Workers/worker.png',
                    'resize' => [20, 20]
                ],
                'dead' => [
                    'path' => 'assets/Workers/dead_worker.png',
                    'resize' => [20, 20]
                ]
            ]
        ];

        return new GameTextures($images);
    }

    public function start()
    {
        InitWindow(static::SCREEN_WIDTH, static::SCREEN_HEIGHT, 'The Game');

        $this->gameTextures = $this->loadTextures();

        SetTargetFPS(static::TARGET_FPS);

        while (!WindowShouldClose()) {

            $this->startUpdatePhase();

            $this->startDrawingPhase();
        }

        $this->gameTextures->unload();

        CloseWindow();
    }

    protected function startUpdatePhase()
    {
        //UPDATE PHASE
        if (IsKeyPressed(KEY_SPACE)) {
            $this->gameState->isPaused() ?
                $this->gameState->continueGame() :
                $this->gameState->pauseGame();
        }

        if (!$this->gameState->isUserNameBeenSet()) {
            $this->fireUsernameAction();
        }

        $this->fireMainMenuAction();
    }

    protected function startDrawingPhase()
    {
        //DRAW PHASE
        BeginDrawing();

            ClearBackground($this->colorLightGray);

            if ($this->gameState->isPaused()) {
                DrawText("PAUSED", 350, 200, 30, Color::GRAY());
            } else {
                if (!$this->gameState->isUserNameBeenSet()) {
                    $this->drawUsernameForm();
                }

                //DRAW UI
                if ($this->gameState->isUserNameBeenSet()) {
                    $this->drawUI();
                    $this->gameState->incrementTicks();
                }
            }

        EndDrawing();
    }

    protected function fireUsernameAction(): void
    {
        $usernameFormAction = new UsernameFormAction($this->gameState, $this->digestor, $this->entitiesFactory);
        $this->gameState->getGameStateObjects()
            ->addObject(new Rectangle(270, 200, 225, 50), UsernameFormAction::class);

        $usernameFormAction->handle();
    }

    protected function fireMainMenuAction(): void
    {
        $elements = [];
        $form = [
            'Worker',
            'Cow',
            'SmallHouse',
        ];

        foreach ($form as $key => $formElementName) {
            $height = 30;
            $positionY = 340 + ($height * $key) + $key;
            $elements[$formElementName] = new Rectangle(630, $positionY, 150, $height);
        }

        $mainMenuAction = new MainMenuAction($this->gameState, $this->digestor, $this->entitiesFactory);
        $this->gameState->getGameStateObjects()
            ->addObject($elements, MainMenuAction::class);

        $mainMenuAction->handle();
    }

    protected function drawUsernameForm()
    {
        DrawText('Enter your user name:', 260, 140, 20, $this->colorGray);

        $usernameForm = $this->gameState->getGameStateObjects()->getObject(UsernameFormAction::class);
        DrawRectangleRec($usernameForm, $this->colorLightGray);

        $isMouseOnText = CheckCollisionPointRec(GetMousePosition(), $usernameForm);

        if ($isMouseOnText) {
            DrawRectangleLines(
                (int) $usernameForm->x,
                (int) $usernameForm->y,
                (int) $usernameForm->width,
                (int) $usernameForm->height,
                Color::RED()
            );
        } else {
            DrawRectangleLines(
                (int) $usernameForm->x,
                (int) $usernameForm->y,
                (int) $usernameForm->width,
                (int) $usernameForm->height,
                $this->colorGray
            );
        }

        DrawText($this->gameState->getUserName(), (int) $usernameForm->x + 5, (int) $usernameForm->y + 8, 40, Color::RED());

        if ($isMouseOnText) {
            DrawText("_", (int) $usernameForm->x + 8 + MeasureText($this->gameState->getUserName(), 40), (int) $usernameForm->y + 12, 40, Color::RED());
            DrawText('Press BACKSPACE to delete chars...', 230, 300, 20, $this->colorGray);
        }
    }

    protected function drawUI()
    {
        $currentPeriod = $this->digestor->getTimesOfYear()->getCurrentPeriod();

        if ($this->gameState->getDaysFromTicks() > $currentPeriod->getDurationInDays()) {
            $this->digestor->digestEntities();
            $this->digestor->getTimesOfYear()->moveToNextPeriod();
            $this->gameState->setTicks(1);
        }

        $this->drawHeader();

        $initialWorkersPositionY = $this->drawInventory();

        $this->drawWorkers($initialWorkersPositionY);
        $this->drawCows($initialWorkersPositionY);
        $this->drawSmallHouses($initialWorkersPositionY);
        $this->drawDeadWorkers($initialWorkersPositionY);

        $this->drawMainMenu();
    }

    //TODO:: Dynamic Header
    protected function drawHeader()
    {
        DrawText(
            sprintf('Welcome %s',
                $this->gameState->getUserName()
            ),
            5,
            0,
            20,
            Color::GREEN()
        );

        DrawText(
            sprintf('Current season is: %s',
                $this->digestor->getTimesOfYear()->getCurrentPeriod()->getName()
            ),
            150,
            0,
            20,
            Color::GREEN()
        );

        DrawText(
            sprintf('Day: %d',
                $this->gameState->getDaysFromTicks()
            ),
            500,
            0,
            20,
            Color::GREEN()
        );

        DrawText(
            sprintf(
                'Year: %d',
                $this->digestor->getTimesOfYear()->getCurrentYear()
            ),
            600,
            0,
            20,
            Color::GREEN()
        );
    }

    protected function drawInventory(): int
    {
        DrawText(
            sprintf('Gold: %d',
                $this->gameState->getCurrentGoldAmount()
            ),
            5,
            static::INVENTORY_POSITION_Y,
            20, Color::BLUE()
        );

        return static::INVENTORY_POSITION_Y + 40;
    }

    protected function drawWorkers(int &$initialWorkersPositionY)
    {
        DrawText(sprintf('Workers: %d', $this->gameState->getTotalWorkersOwned()), 5, 50, 20, Color::BLUE());

        foreach ($this->digestor->getWorkers() as $worker) {
            if (!$worker->isDead()) {
                if ($worker->getCurrentHitPointsPercent() > 50) {
                    DrawTexture(
                        $this->gameTextures->getWorkerTexture($worker),
                        25,
                        $initialWorkersPositionY,
                        Color::GREEN()
                    );
                }

                if ($worker->getCurrentHitPointsPercent() <= 50) {
                    DrawTexture(
                        $this->gameTextures->getWorkerTexture($worker),
                        25,
                        $initialWorkersPositionY,
                        Color::YELLOW()
                    );
                }

                DrawText(
                    sprintf('%s: HP=%d, Income per Season: %d',
                        $worker->getName(),
                        $worker->getCurrentHitPoints(),
                        $worker->getGoldEarningsPerPeriod()
                    ),
                    50,
                    $initialWorkersPositionY,
                    20,
                    Color::BLUE()
                );

                $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;
            }
        }
    }

    protected function drawCows(int &$initialWorkersPositionY)
    {
        DrawText(sprintf('Cows: %d', $this->gameState->getTotalCowsOwned()),5,$initialWorkersPositionY,20,Color::BLUE());

        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        foreach ($this->digestor->getCows() as $cow) {
            DrawText(
                sprintf('%s: HP=%d, Income per Season: %d',
                    $cow->getName(),
                    $cow->getCurrentHitPoints(),
                    $cow->getGoldEarningsPerPeriod()
                ),
                25,
                $initialWorkersPositionY,
                20,
                Color::BLUE()
            );
            $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;
        }
    }
    protected function drawSmallHouses(int &$initialWorkersPositionY)
    {
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
    }

    protected function drawDeadWorkers(int &$initialWorkersPositionY)
    {
        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        $deadWorkers = $this->gameState->getDeadWorkers();
        DrawText(sprintf('Dead Workers: %d', count($deadWorkers)), 5, $initialWorkersPositionY, 20, Color::MAROON());

        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        foreach ($deadWorkers as $deadWorker) {
            DrawTexture(
                $this->gameTextures->getWorkerTexture($deadWorker),
                25,
                $initialWorkersPositionY,
                Color::MAROON()
            );

            DrawText(
                sprintf('%s: HP=%d Income per Season: %d',
                    $deadWorker->getName(),
                    $deadWorker->getCurrentHitPoints(),
                    $deadWorker->getGoldEarningsPerPeriod()
                ),
                50,
                $initialWorkersPositionY,
                20,
                Color::MAROON()
            );
            $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;
        }
    }

    protected function drawMainMenu()
    {
        $mainMenu = $this->gameState->getGameStateObjects()->getObject(MainMenuAction::class);
        $text = 'Buy ';

        foreach ($mainMenu as $name => $element) {
            $isMouseOver = CheckCollisionPointRec(GetMousePosition(), $element);
            DrawRectangleRec($element, $isMouseOver ? Color::SKYBLUE() : Color::LIGHTGRAY());
            DrawRectangleLines($element->x, $element->y, $element->width, $element->height, $isMouseOver ? Color::BLUE() : Color::GRAY());

            $positionX = (int)( $element->x + $element->width / 2 - MeasureText($name, 10)/2);
            $positionY = (int) $element->y + 11;
            DrawText($text . $name, $positionX, $positionY, 10, $isMouseOver ? Color::DARKBLUE() : Color::DARKGRAY());
        }
    }
}