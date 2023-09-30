<?php

use App\Digestor;
use App\Entities\EntitiesFactory;
use App\Game;
use App\Periods\TimesOfYear;
use App\State\GameState;
use App\State\GameStateObjects;

require __DIR__.'/../vendor/autoload.php';

$userName = '';
$settlementName = '';

$gameState = new GameState(new GameStateObjects());
$gameState->setUserName($userName)
    ->setSettlementName($settlementName)
    ->addStartingGoldAmount();

$digestor = new Digestor($gameState, [], new TimesOfYear(), 1);
$entitiesFactory = new EntitiesFactory();

//TODO:: Remove me, testing purposes only
    $digestor->addEntity($entitiesFactory->createWorker('Vasya', $gameState, 5));
    $digestor->addEntity($entitiesFactory->createSmallHouse($gameState));
//TODO:: Remove me, testing purposes only

$game = new Game($gameState, $digestor, $entitiesFactory);
$game->start();
