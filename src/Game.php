<?php

namespace App;

use App\Entities\Contracts\IEntity;
use App\Entities\EntitiesAllowedToBuy;
use App\Entities\EntitiesFactory;
use App\Entities\Living\Humans\Worker as HumanWorker;
use App\Entities\Living\Animals\Cow;
use App\Entities\Structures\SmallHouse;
use App\Enums\Sounds;
use App\Exceptions\Profile\NotEnoughGoldToSpendException;
use App\State\GameState;
use App\State\ObjectActions\MainMenuAction;
use App\State\ObjectActions\UsernameFormAction;
use App\Tasks\MoveTask;
use App\Tasks\WalkTask;
use raylib\Color;
use raylib\Rectangle;
use const raylib\KeyboardKey\KEY_SPACE;
use const raylib\MouseButton\MOUSE_BUTTON_LEFT;
use const raylib\MouseButton\MOUSE_BUTTON_RIGHT;

class Game
{
    const SCREEN_WIDTH  = 1920;
    const SCREEN_HEIGHT = 1080;
    const TARGET_FPS = 60;
    const WORKERS_UI_STEPPING_Y = 20;

    const MENU_ELEMENT_HEIGHT = 30;
    const MENU_ELEMENT_WIDTH = 150;
    const USERNAME_INPUT_HEIGHT = 50;
    const USERNAME_INPUT_WIDTH = 225;

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
                    'resize' => [20, 20],
                ],
                'dead' => [
                    'path' => 'assets/Workers/dead_worker.png',
                    'resize' => [20, 20],
                ],
            ],
            'cow' => [
                'alive' => [
                    'path' => 'assets/Cows/cow.png',
                    'resize' => [20, 20],
                ],
                'dead' => [
                    'path' => 'assets/Cows/cow.png',
                    'resize' => [20, 20],
                ],
            ],
            'smallhouse' => [
                'alive' => [
                    'path' => 'assets/Houses/smallhouse.png',
                    'resize' => [20, 20],
                ],
                'dead' => [
                    'path' => 'assets/Houses/smallhouse.png',
                    'resize' => [20, 20],
                ],
            ],
        ];

        return new GameTextures($images);
    }

    public function start()
    {
        InitWindow(static::SCREEN_WIDTH, static::SCREEN_HEIGHT, 'The Game');

            $this->initSounds();

            $this->gameTextures = $this->loadTextures();

            //TODO:: DElete me
            $workerEntity = $this->entitiesFactory->createEntityOfType(HumanWorker::class, $this->gameState, 5);
            $workerEntity->getMoveOptions()
                ->setTexture($this->gameTextures->getEntityTexture($workerEntity));
            $this->digestor->addEntity($workerEntity);

            $cowEntity = $this->entitiesFactory->createEntityOfType(Cow::class, $this->gameState);
            $cowEntity->getMoveOptions()
                ->setTexture($this->gameTextures->getEntityTexture($cowEntity));
            $this->digestor->addEntity($cowEntity);

            $smallHouseEntity = $this->entitiesFactory->createEntityOfType(SmallHouse::class, $this->gameState);
            $smallHouseEntity->getMoveOptions()
                ->setTexture($this->gameTextures->getEntityTexture($smallHouseEntity));
            $this->digestor->addEntity($smallHouseEntity);

            //TODO:: DElete me

            SetTargetFPS(static::TARGET_FPS);

            while (!WindowShouldClose()) {

                //UpdateMusicStream($this->gameState->getGameStateSounds()->getObject(Sounds::INTRO()->getValue()));

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

            foreach ($this->digestor->getEntities() as $entity) {
                $entityPosition = $entity->getMoveOptions()->getPosition();
                $texture = $this->gameTextures->getEntityTexture($entity);

                $isMouseOnEntity = CheckCollisionPointRec(
                    GetMousePosition(),
                    new Rectangle($entityPosition->x, $entityPosition->y, $texture->width, $texture->height)
                );

                if ($entity->canMove()) {
                    if ($isMouseOnEntity) {
                        if (IsMouseButtonReleased(MOUSE_BUTTON_LEFT)) {
                            $entity->setSelected(true);
                        }
                    }

                    if (!$isMouseOnEntity) {
                        if (IsMouseButtonReleased(MOUSE_BUTTON_LEFT)) {
                            $entity->setSelected(false);
                        }

                        if ($entity->isSelected()) {
                            if (IsMouseButtonReleased(MOUSE_BUTTON_RIGHT)) {
                                $walkTask = new WalkTask();
                                $walkTask->setDirection(GetMousePosition());

                                //Clear current walk tasks being executed
                                $entity->getTaskQueue()->clearOfType($walkTask);

                                if ($entity->getTask() instanceof $walkTask) {
                                    $entity->setTask(null);
                                }

                                $entity->getTaskQueue()->push($walkTask);
                            }
                        }
                    }
                }

                if (!$entity->canMove()) {
                    if ($isMouseOnEntity) {
                        if (IsMouseButtonReleased(MOUSE_BUTTON_LEFT)) {
                            $entity->setSelected(!$entity->isSelected());
                            if (!$entity->isSelected()) {
                                $entity->setTask(null);
                            } else {
                                if ($entity->isSelected()) {
                                    $moveTask = new MoveTask();
                                    $entity->getTaskQueue()->push($moveTask);
                                }
                            }
                        }
                    }
                }
            }
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
        $usernameFormAction = new UsernameFormAction($this->gameState, $this->digestor, $this->entitiesFactory, $this->gameTextures);
        $rectanglePositionX = GetScreenWidth() / 2 -  self::USERNAME_INPUT_WIDTH / 2;
        $rectanglePositionY = GetScreenHeight() / 2;
        $this->gameState->getGameStateObjects()
            ->addObject(new Rectangle(
                $rectanglePositionX,
                $rectanglePositionY,
                static::USERNAME_INPUT_WIDTH,
                static::USERNAME_INPUT_HEIGHT
            ), UsernameFormAction::class);

        $usernameFormAction->handle();
    }

    /**
     * @return void
     * @throws NotEnoughGoldToSpendException
     */
    protected function fireMainMenuAction(): void
    {
        $elements = [];
        $form = array_unique(EntitiesAllowedToBuy::get());

        $maxHeight = count($form) * static::MENU_ELEMENT_HEIGHT + 5;
        $startPositionX = GetScreenWidth() - 5 - static::MENU_ELEMENT_WIDTH;
        $startPositionY = GetScreenHeight() - $maxHeight;

        foreach ($form as $key => $formElementName) {
            $positionY = $startPositionY + (static::MENU_ELEMENT_HEIGHT * $key) + $key;
            $elements[$formElementName] = new Rectangle($startPositionX, $positionY, 150, static::MENU_ELEMENT_HEIGHT);
        }

        $mainMenuAction = new MainMenuAction(
            $this->gameState,
            $this->digestor,
            $this->entitiesFactory,
            $this->gameTextures
        );

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
        $usernameTextFontSize = 20;
        $explainText = 'Enter your user name:';
        $positionX = (int) (GetScreenWidth() / 2 - self::USERNAME_INPUT_WIDTH + MeasureText($explainText, $usernameTextFontSize)) - self::USERNAME_INPUT_WIDTH / 2;
        $positionY = (int) (GetScreenHeight() / 2 - self::USERNAME_INPUT_HEIGHT);
        DrawText($explainText, (int) $positionX, (int) $positionY, $usernameTextFontSize, $this->colorGray);

        $usernameForm = $this->gameState->getGameStateObjects()->getObject(UsernameFormAction::class);
        DrawRectangleRec($usernameForm, $this->colorLightGray);

        $isMouseOnText = CheckCollisionPointRec(GetMousePosition(), $usernameForm);

        DrawRectangleLines(
            (int) $usernameForm->x,
            (int) $usernameForm->y,
            (int) $usernameForm->width,
            (int) $usernameForm->height,
            $isMouseOnText ? Color::RED() : $this->colorGray
        );

        DrawText(
            $this->gameState->getUserName(),
            (int) $usernameForm->x + 5,
            (int) $usernameForm->y + 8,
            40,
            Color::RED()
        );

        if ($isMouseOnText) {
            DrawText(
                "_",
                (int) $usernameForm->x + 8 + MeasureText($this->gameState->getUserName(), 40),
                (int) $usernameForm->y + 12,
                40,
                Color::RED()
            );

            $explainTextFontSize = 20;
            $explainText = 'Press BACKSPACE to delete chars...';
            $positionX = (int) (GetScreenWidth() / 2 + self::USERNAME_INPUT_WIDTH - MeasureText($explainText, $explainTextFontSize) + $usernameTextFontSize * 4) - self::USERNAME_INPUT_WIDTH / 2;
            $positionY = (int) (GetScreenHeight() / 2 + self::USERNAME_INPUT_HEIGHT + $usernameTextFontSize);
            DrawText($explainText, (int) $positionX, (int) $positionY, $explainTextFontSize, $this->colorGray);
        }
    }

    protected function drawUI()
    {
        $this->digestor->digestEntitiesTasks();

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

        $this->drawHitPoints();
    }

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

        $foodPositionX = MeasureText(sprintf(
            'Year: %d',
            $this->digestor->getTimesOfYear()->getCurrentYear()
        ), 20);

        $food = $this->gameState->getGameStateNaturalResources()->getTotalFoodValue();
        DrawText(
            sprintf(
                'Food: %d',
                $food
            ),
            600 + $foodPositionX + 10,
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

        $nexPosition = $initialDrawInventoryPosition + $inventoryFontSize + $inventorySpaceAfter;

        DrawText(
            sprintf('Population: %d',
                $this->digestor->getPopulation()
            ),
            5,
            $nexPosition,
            $inventoryFontSize, Color::BLUE()
        );

        return $nexPosition + $inventoryFontSize + $inventorySpaceAfter;
    }

    protected function drawDeadWorkers(int &$initialWorkersPositionY)
    {
        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        $deadWorkers = $this->gameState->getDeadWorkers();
        DrawText(sprintf('Dead Workers: %d', count($deadWorkers)), 5, $initialWorkersPositionY, 20, Color::MAROON());

        $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;

        /** @var  $deadWorkers IEntity[] */
        foreach ($deadWorkers as $deadWorker) {
            DrawTexture(
                $this->gameTextures->getEntityTexture($deadWorker),
                25,
                $initialWorkersPositionY,
                Color::MAROON()
            );

            if ($deadWorker->canMove()) {
                DrawTexture(
                    $this->gameTextures->getEntityTexture($deadWorker),
                    (int) $deadWorker->getMoveOptions()->getPosition()->x,
                    (int) $deadWorker->getMoveOptions()->getPosition()->y,
                    Color::MAROON()
                );
            }

            DrawText(
                sprintf('%s: HP=%d Income per Season: %d',
                    $deadWorker->getName(),
                    $deadWorker->getCurrentHitPoints(),
                    $deadWorker->getGoldIncomePerPeriod()
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

            /** @var  $entitiesCollection IEntity[] */
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

                DrawTexture(
                    $this->gameTextures->getEntityTexture($entity),
                    (int) $entity->getMoveOptions()->getPosition()->x,
                    (int) $entity->getMoveOptions()->getPosition()->y,
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

                DrawTexture(
                    $this->gameTextures->getEntityTexture($entity),
                    (int) $entity->getMoveOptions()->getPosition()->x,
                    (int) $entity->getMoveOptions()->getPosition()->y,
                    Color::YELLOW()
                );
            }

            DrawText(
                sprintf('%s: HP=%d, Income per Season: %d',
                    $entity->getName(),
                    $entity->getCurrentHitPoints(),
                    $entity->getGoldIncomePerPeriod()
                ),
                50,
                $initialWorkersPositionY,
                20,
                Color::BLUE()
            );

            if ($entity->isSelected()) {
                DrawRectangleLines(
                    (int) $entity->getMoveOptions()->getPosition()->x,
                    (int) $entity->getMoveOptions()->getPosition()->y,
                    $this->gameTextures->getEntityTexture($entity)->width,
                    $this->gameTextures->getEntityTexture($entity)->width,
                    Color::GREEN()
                );
            }

            $initialWorkersPositionY += static::WORKERS_UI_STEPPING_Y;
        }
    }

    protected function drawMainMenu()
    {
        $mainMenu = $this->gameState->getGameStateObjects()->getObject(MainMenuAction::class);

        foreach ($mainMenu as $name => $element) {
            $entityClass = $this->entitiesFactory->createFreeEntityOfType($name, $this->gameState);
            $isMouseOver = CheckCollisionPointRec(GetMousePosition(), $element);
            $text = sprintf('Buy %s - %dg.', $entityClass, $entityClass->getCost());

            $rectangleColor = Color::MAROON();
            $rectangleLinesColor = Color::MAROON();

            if ($this->gameState->isEnoughGoldToBuy($entityClass)) {
                $rectangleColor = $isMouseOver ? Color::SKYBLUE() : Color::LIGHTGRAY();
                $rectangleLinesColor = $isMouseOver ? Color::BLUE() : Color::GRAY();
            }

            DrawRectangleRec($element, $rectangleColor);
            DrawRectangleLines($element->x, $element->y, $element->width, $element->height, $rectangleLinesColor);

            $positionX = (int)( $element->x + $element->width / 2 - MeasureText($text, 10)/2);
            $positionY = (int) $element->y + 11;
            DrawText($text, $positionX, $positionY, 10, $isMouseOver ? Color::DARKBLUE() : Color::DARKGRAY());
        }
    }

    protected function drawHitPoints()
    {
        /** @var IEntity[] $entities */
        $entities = $this->digestor->getEntities();

        foreach ($entities as $entity) {
            $hitPointsOptions = $entity->getEntityHitPointsOptions();
            $hitPointsBar = $hitPointsOptions->getBar();

            //Calculate Hitpoins dimensions change (if hitpoins goes up/down the bar dimensions must be increased/decreased)
            $hitPointsBarPerPercent = $hitPointsBar->width / 100;
            $entityCurrentHitPointsPercent = $entity->getCurrentHitPointsPercent();

            $hitPointsBarNewWidth = $entityCurrentHitPointsPercent * $hitPointsBarPerPercent;

            $newBar = new Rectangle($hitPointsBar->x, $hitPointsBar->y, $hitPointsBarNewWidth, $hitPointsBar->height);
            DrawRectangleRec($newBar, Color::MAROON());
            DrawRectangleLines((int) $hitPointsBar->x, (int) $hitPointsBar->y, $hitPointsBar->width, $hitPointsBar->height, Color::SKYBLUE());
        }
    }
}