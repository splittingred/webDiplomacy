<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm as Base;
use Diplomacy\Models\Entities\Game;

class BaseForm extends Base
{
    /**
     * @return Game
     */
    protected function getGame()
    {
        return $this->getPlaceholder('game');
    }
}