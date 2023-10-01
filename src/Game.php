<?php

namespace App;

use App\Entities\Contracts\IEntity;
use App\Entities\EntitiesAllowedToBuy;
use App\Entities\EntitiesFactory;
use App\Enums\Sounds;
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
            ],
            'cow' => [
                'alive' => [
                    'path' => 'assets/Cows/cow.png',
                    'resize' => [20, 20]
                ],
                'dead' => [
                    'path' => 'assets/Cows/cow.png',
                    'resize' => [20, 20]
                ],
            ],
            'smallhouse' => [
                'alive' => [
                    'path' => 'assets/Houses/smallhouse.png',
                    'resize' => [20, 20]
                ],
                'dead' => [
                    'path' => 'assets/Houses/smallhouse.png',
                    'resize' => [20, 20]
                ],
            ]
        ];

        return new GameTextures($images);
    }

    public function start()
    {
        InitWindow(static::SCREEN_WIDTH, static::SCREEN_HEIGHT, 'The Game');

            $this->initSounds();

            $this->gameTextures = $this->loadTextures();

            SetTargetFPS(static::TARGET_FPS);

            while (!WindowShouldClose()) {

                UpdateMusicStream($this->gameState->getGameStateSounds()->getObject(Sounds::INTRO()->getValue()));

                $this->startUpdatePhase();

                $this->startDrawingPhase();
            }

            $this->gameTextures->unload();

        CloseWindow();
    }

    protected function initSounds()
    {
        InitAudioDevice();

        $musicIntro = LoadMusicStream('assets/Music/intro.mp3');
        $this->gameState->getGameStateSounds()->addObject($musicIntro, Sounds::INTRO()->getValue());
        PlayMusicStream($musicIntro);

        $keyBoardKeypressSound = LoadMusicStream('assets/Music/keyboard_keypress.wav');
        $this->gameState->getGameStateSounds()->addObject($keyBoardKeypressSound, Sounds::KEYBOARD_SOUND()->getValue());
        PlayMusicStream($keyBoardKeypressSound);
    }

    protected function startUpdatePhase()
    {
        //UPDATE PHASE
        if (IsKeyPressed(KEY_SPACE)) {
            $this->gameState->isPaused() ?
                $this->gameState->continueGame() :
                $this->gameState->pauseGame();
        }

        //Actions Section
        try {
            if (!$this->gameState->isUserNameBeenSet()) {
                $this->fireUsernameAction();
            }

            $this->fireMainMenuAction();
        } catch (NotEnoughGoldToSpendException $e) {
            $this->gameState->setError(true);
            $this->gameState->setErrorMessage($e->getMessage());
        }
    }

    protected function startDrawingPhase()
    {
        //DRAW PHASE
        BeginDrawing();

            ClearBackground($this->colorLightGray);

            //Error Handler
            if ($this->gameState->isError()) {
                if ($this->gameState->getCurrentErrorMessageTickDuration() === 0.00) {
                    $errorDuration = GetTime() + 2;
                    $this->gameState->setCurrentErrorMessageTickDuration($errorDuration);
                }

                if ($this->gameState->getCurrentErrorMessageTickDuration() >= GetTime()) {
                    $this->drawErrorMessage($this->gameState->getErrorMessage());
                } else {
                    $this->gameState->setCurrentErrorMessageTickDuration(0.00);
                    $this->gameState->setError(false);
                    $this->gameState->setErrorMessage('');
                }
            }

            //Game Pause
            if ($this->gameState->isPaused()) {
                DrawText("PAUSED", 350, 200, 30, Color::GRAY());
                PauseMusicStream($this->gameState->getGameStateSounds()->getObject(Sounds::INTRO()->getValue()));
            } else {
                ResumeMusicStream($this->gameState->getGameStateSounds()->getObject(Sounds::INTRO()->getValue()));
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

    /**
     * @return void
     * @throws NotEnoughGoldToSpendException
     */
    protected function fireMainMenuAction(): void
    {
        $elements = [];
        $form = EntitiesAllowedToBuy::get();

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

    protected function drawErrorMessage(string $errorMessage)
    {
        DrawRectangle(
            0,
            0,
            static::SCREEN_WIDTH,
            20,
            Color::RED()
        );

        DrawText(
            $errorMessage,
            GetScreenWidth()/2 - MeasureText($errorMessage, 20) / 2,
            0,
            20,
            Color::BLACK()
        );
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

        $initialDrawInventoryPosition = $this->drawHeader();

        $initialEntityPositionY = $this->drawInventory($initialDrawInventoryPosition);

        $this->drawEntities($initialEntityPositionY);

        $this->drawDeadWorkers($initialEntityPositionY);

        $this->drawMainMenu();
    }

    //TODO:: Dynamic Header

    /**
     * @return int
     */
    protected function drawHeader(): int
    {
        //$headerPositionY value 20 here because error message rectangle requires 20 pixels
        $headerPositionY = 20;
        $headerFontSize = 20;
        $headerSpaceAfter = 10;

        DrawText(
            sprintf('Welcome %s',
                $this->gameState->getUserName()
            ),
            5,
            $headerPositionY,
            $headerFontSize,
            Color::GREEN()
        );

        DrawText(
            sprintf('Current season is: %s',
                $this->digestor->getTimesOfYear()->getCurrentPeriod()->getName()
            ),
            150,
            $headerPositionY,
            $headerFontSize,
            Color::GREEN()
        );

        DrawText(
            sprintf('Day: %d',
                $this->gameState->getDaysFromTicks()
            ),
            500,
            $headerPositionY,
            $headerFontSize,
            Color::GREEN()
        );

        DrawText(
            sprintf(
                'Year: %d',
                $this->digestor->getTimesOfYear()->getCurrentYear()
            ),
            600,
            $headerPositionY,
            $headerFontSize,
            Color::GREEN()
        );

        return $headerPositionY + $headerFontSize + $headerSpaceAfter;
    }

    protected function drawInventory(int $initialDrawInventoryPosition): int
    {
        $inventorySpaceAfter = 20;
        $inventoryFontSize = 20;

        DrawText(
            sprintf('Gold: %d',
                $this->gameState->getCurrentGoldAmount()
            ),
            5,
            $initialDrawInventoryPosition,
            $inventoryFontSize, Color::BLUE()
        );

        return $initialDrawInventoryPosition + $inventoryFontSize + $inventorySpaceAfter;
    }

    protected function drawDeadWorkers(int &$initialWorkersPositionY)
    {
        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        $deadWorkers = $this->gameState->getDeadWorkers();
        DrawText(sprintf('Dead Workers: %d', count($deadWorkers)), 5, $initialWorkersPositionY, 20, Color::MAROON());

        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        foreach ($deadWorkers as $deadWorker) {
            DrawTexture(
                $this->gameTextures->getEntityTexture($deadWorker),
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

    protected function drawEntities(&$initialWorkersPositionY)
    {
        foreach ($this->digestor->getUniqueEntities() as $uniqueEntity) {
            $entitiesCollection = $this->digestor->getEntitiesOfType($uniqueEntity);

            DrawText(
                sprintf('%s: %d',
                    (new \ReflectionClass($uniqueEntity))->getShortName(),
                    count($entitiesCollection)
                ),
                5,
                $initialWorkersPositionY,
                20,
                Color::BLUE()
            );

            $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

            foreach ($entitiesCollection as $entity) {
                $this->drawEntity($entity, $initialWorkersPositionY);
            }
        }
    }

    protected function drawEntity(IEntity $entity, &$initialWorkersPositionY)
    {
        if (!$entity->isDead()) {
            if ($entity->getCurrentHitPointsPercent() > 50) {
                DrawTexture(
                    $this->gameTextures->getEntityTexture($entity),
                    25,
                    $initialWorkersPositionY,
                    Color::GREEN()
                );
            }

            if ($entity->getCurrentHitPointsPercent() <= 50) {
                DrawTexture(
                    $this->gameTextures->getEntityTexture($entity),
                    25,
                    $initialWorkersPositionY,
                    Color::YELLOW()
                );
            }

            DrawText(
                sprintf('%s: HP=%d, Income per Season: %d',
                    $entity->getName(),
                    $entity->getCurrentHitPoints(),
                    $entity->getGoldEarningsPerPeriod()
                ),
                50,
                $initialWorkersPositionY,
                20,
                Color::BLUE()
            );

            $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;
        }
    }

    protected function drawMainMenu()
    {
        $mainMenu = $this->gameState->getGameStateObjects()->getObject(MainMenuAction::class);

        foreach ($mainMenu as $name => $element) {
            /** @var IEntity $entityClass */
            $entityClass = new $name($this->gameState, true);
            $isMouseOver = CheckCollisionPointRec(GetMousePosition(), $element);
            $text = sprintf('Buy %s - %dg.', $entityClass, $entityClass->getCost());

            if ($this->gameState->isEnoughGoldToBuy($entityClass)) {
                DrawRectangleRec($element, $isMouseOver ? Color::SKYBLUE() : Color::LIGHTGRAY());
                DrawRectangleLines($element->x, $element->y, $element->width, $element->height, $isMouseOver ? Color::BLUE() : Color::GRAY());
            } else {
                DrawRectangleRec($element, Color::MAROON());
                DrawRectangleLines($element->x, $element->y, $element->width, $element->height, Color::MAROON());
            }

            $positionX = (int)( $element->x + $element->width / 2 - MeasureText($text, 10)/2);
            $positionY = (int) $element->y + 11;
            DrawText($text, $positionX, $positionY, 10, $isMouseOver ? Color::DARKBLUE() : Color::DARKGRAY());
        }
    }
}