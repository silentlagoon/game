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
use App\Profile\Profile;

require __DIR__.'/../vendor/autoload.php';

$userName = readline('Enter your username: ');
$settlementName = readline('Enter your settlement name: ');

$profile = new Profile();
$profile->setUserName($userName)
    ->setSettlementName($settlementName)
    ->addStartingGoldAmount();

$digestor = new Digestor([], new TimesOfYear(), 1);
$entitiesFactory = new EntitiesFactory();

$game = new Game($profile, $digestor, $entitiesFactory);
$game->start();
