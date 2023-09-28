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

use App\Entities\Living\Humans\Worker;
use App\Periods\TimesOfYear;

require __DIR__.'/../vendor/autoload.php';

$timesOfYear = new TimesOfYear();
$entities = [new Worker()];

$currentYear = 1;

while (true) {

    foreach ($entities as $entity) {
        $yearResult = $entity->digestPeriod($timesOfYear->getCurrentPeriod());
        dump($yearResult);

        if ($entity->isDead()) {
            dd(sprintf('%s has died this %s, on Year: %d', $entity->getName(), $timesOfYear->getCurrentPeriod()->getName(), $currentYear));
        }
    }

    $timesOfYear->moveToNextPeriod();
    $currentYear++;
}