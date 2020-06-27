<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm as ModalBaseForm;
use Diplomacy\Models\Entities\Game;

class BaseForm extends ModalBaseForm
{
    /**
     * @return Game
     */
    protected function getGame()
    {
        return $this->getPlaceholder('game');
    }
}