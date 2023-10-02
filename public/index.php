<?php

use App\Digestor;
use App\Entities\EntitiesFactory;
use App\Entities\Living\Humans\Worker as HumanWorker;
use App\Entities\Living\Animals\Cow;
use App\Entities\Structures\SmallHouse;
use App\Game;
use App\Periods\TimesOfYear;
use App\State\GameState;
use App\State\GameStateNaturalResources;
use App\State\GameStateObjects;
use App\State\GameStateSounds;

require __DIR__.'/../vendor/autoload.php';

$userName = '';
$settlementName = '';

$gameState = new GameState();
$gameState->setGameStateObject(new GameStateObjects())
    ->setGameStateSounds(new GameStateSounds())
    ->setGameStateNaturalResources(new GameStateNaturalResources());

$gameState->setUserName($userName)
    ->setSettlementName($settlementName)
    ->addStartingGoldAmount();

$digestor = new Digestor($gameState, [], new TimesOfYear(), 1);
$entitiesFactory = new EntitiesFactory();

//TODO:: Remove me, testing purposes only
    $gameState->addGoldAmount(1000);
    $digestor->addEntity($entitiesFactory->createEntityOfType(HumanWorker::class, $gameState, 5));
    $digestor->addEntity($entitiesFactory->createEntityOfType(Cow::class, $gameState));
    $digestor->addEntity($entitiesFactory->createEntityOfType(SmallHouse::class, $gameState));
//TODO:: Remove me, testing purposes only

$game = new Game($gameState, $digestor, $entitiesFactory);
$game->start();


//TODO:: Переделать интсрументы worker должен иметь возможномть использовать инструменты(equipment)
//TODO:: Интсрументы влияют на производительность труда через индексацию Income
//TODO:: период у воркера расчитывет инкам учитывая инструменты в эквипмент