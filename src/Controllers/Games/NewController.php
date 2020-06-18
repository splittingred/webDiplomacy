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
        /** @var NewForm $form */
        $form = $this->makeForm(NewForm::class);
        $form->setCurrentUser($this->currentUserEntity);
        parent::setUp();
    }

    public function call()
    {
        return [
            'form' => $this->form->render(),
        ];
    }
}
