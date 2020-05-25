<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Game;
use Diplomacy\Models\Member;

class ContactDirectController extends BaseController
{
    public $template = 'pages/help/contact_direct.twig';
    public $pageTitle = 'Contact Us';
    public $pageDescription = 'Directly submit a support request to the moderator team.';

    public function call()
    {
        return [
            'games' => $this->getGames(),
        ];
    }

    protected function getGames()
    {
        $query = Game::joinMembers()
            ->gameNotOver()
            ->notPreGame()
            ->where('wD_Members.status', '=', 'Playing')
            ->where('wD_Members.userID', '=', $this->currentUser->id);
        return $query->get();
    }
}