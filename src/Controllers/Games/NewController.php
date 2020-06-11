<?php

namespace Diplomacy\Controllers\Games;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Forms\Games\NewForm;
use Diplomacy\Models\Game;

class NewController extends BaseController
{
    public $template = 'pages/games/new.twig';
    /** @var NewForm $form */
    protected $form;

    public function setUp()
    {
        $this->form = new NewForm($this->request, $this->renderer);
        parent::setUp();
    }

    public function call()
    {
        return [
            'form' => $this->form->render(),
        ];
    }
}
