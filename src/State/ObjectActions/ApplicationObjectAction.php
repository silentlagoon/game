<?php

namespace App\State\ObjectActions;

use App\Digestor;
use App\Entities\EntitiesFactory;
use App\GameTextures;
use App\State\GameState;
use App\State\ObjectActions\Contracts\IApplicationObjectAction;

abstract class ApplicationObjectAction implements IApplicationObjectAction
{
    protected GameState $gameState;
    protected Digestor $digestor;
    protected EntitiesFactory $entitiesFactory;
    protected GameTextures $gameTextures;

    public function __construct(
        GameState $gameState,
        Digestor $digestor,
        EntitiesFactory $entitiesFactory,
        GameTextures $gameTextures
    )
    {
        $this->gameState = $gameState;
        $this->digestor = $digestor;
        $this->entitiesFactory = $entitiesFactory;
        $this->gameTextures = $gameTextures;
    }
}