<?php

if (!function_exists('dd')) {
    function dd(mixed ...$vars): never
    {
        echo '<pre>';
        var_dump($vars);
        echo '</pre>';
        exit(1);
    }
}

if (!function_exists('dump')) {
    function dump(mixed ...$vars)
    {
        echo '<pre>';
        var_dump($vars);
        echo '</pre>';
    }
}

use App\Digestor;
use App\Entities\EntitiesFactory;
use App\Game;
use App\Periods\TimesOfYear;
use App\Profile\GameState;

require __DIR__.'/../vendor/autoload.php';

//$userName = readline('Enter your username: ');
$userName = '';
//$settlementName = readline('Enter your settlement name: ');
$settlementName = '';

$gameState = new GameState();
$gameState->setUserName($userName)
    ->setSettlementName($settlementName)
    ->addStartingGoldAmount();

$digestor = new Digestor($gameState, [], new TimesOfYear(), 1);
$entitiesFactory = new EntitiesFactory();


//TODO:: Remove me, testing purposes only
$digestor->addEntity($entitiesFactory->createWorker('Vasya', $gameState));
$digestor->addEntity($entitiesFactory->createSmallHouse($gameState));

$game = new Game($gameState, $digestor, $entitiesFactory);
$game->start();
