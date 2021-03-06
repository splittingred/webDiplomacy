<?php

namespace Diplomacy\Controllers\Games\View;

use libModNotes;

class ModeratorNotesController extends BaseController
{
    protected string $template = 'pages/games/view/moderator_notes.twig';

    public function call(): array
    {
        require_once ROOT_PATH . 'lib/modnotes.php';
        libModNotes::checkDeleteNote();
        libModNotes::checkInsertNote();
        return [
            'form' => libModNotes::reportBoxHTML('Game', $this->game->id),
            'notes' => libModNotes::reportsDisplay('Game', $this->game->id),
        ];
    }
}