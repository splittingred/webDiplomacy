<?php

namespace Diplomacy\Controllers\Games;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Forms\Games\NewForm;
use Diplomacy\Models\Game;

class NewController extends BaseController
{
    public string $template = 'pages/games/new.twig';

    public function setUp(): void
    {
        /** @var NewForm $form */
        $form = $this->makeForm(NewForm::class);
        $form->setCurrentUser($this->currentUserEntity);
        parent::setUp();
    }

    public function call(): array
    {
        return [
            'form' => $this->form->render(),
        ];
    }
}
