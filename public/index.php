<?php

use App\Digestor;
use App\Entities\EntitiesFactory;
use App\Game;
use App\Periods\TimesOfYear;
use App\State\GameState;
use App\State\GameStateNaturalResources;
use App\State\GameStateObjects;
use App\State\GameStateSounds;

require __DIR__.'/../vendor/autoload.php';

$userName = '';
$settlementName = '';

$gameState = new GameState(
    new GameStateObjects(),
    new GameStateSounds(),
    new GameStateNaturalResources()
);

$gameState->setUserName($userName)
    ->setSettlementName($settlementName)
    ->addStartingGoldAmount();

$digestor = new Digestor($gameState, [], new TimesOfYear(), 1);
$entitiesFactory = new EntitiesFactory();

//TODO:: Remove me, testing purposes only
    $digestor->addEntity($entitiesFactory->createWorker($gameState, 5));
    $digestor->addEntity($entitiesFactory->createCow($gameState));
    $digestor->addEntity($entitiesFactory->createSmallHouse($gameState));
//TODO:: Remove me, testing purposes only

$game = new Game($gameState, $digestor, $entitiesFactory);
$game->start();
