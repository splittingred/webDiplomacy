<?php

namespace Diplomacy\Controllers\Games;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Forms\Games\NewForm;
use Diplomacy\Models\Game;

class NewController extends BaseController
{
    public $template = 'pages/games/new.twig';

    public function setUp()
    {
        $this->makeForm(NewForm::class);
        parent::setUp();
    }

    public function call()
    {
        return [
            'form' => $this->form->render(),
        ];
    }
}
