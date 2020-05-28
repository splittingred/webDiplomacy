<?php

namespace Diplomacy\Controllers\Games\Search;

use Diplomacy\Models\Game;

trait HasGamesTab
{
    public function getGamesTabs($current = 'mine') : string
    {
        $values = $this->getGamesTabsValues();
        return $this->renderer->render('pages/games/list/_tabs.twig', array_merge($values, [
            'current' => $current,
            'current_user' => $this->currentUser,
        ]));
    }

    private function getGamesTabsValues() : array
    {
        $userId = $this->currentUser->id;
        if ($this->currentUser->isAuthenticated()) {
            $mine = Game::activeForUser($userId)->count();
        } else {
            $mine = 0;
        }

        if ($this->currentUser->userIsTempBanned()) {
            $open = 0;
        } else {
            $open = Game::joinableForUser($userId, $this->currentUser->points, $this->currentUser->reliabilityRating)->count();
        }

        return [
            'mine' => $mine,
            'new' => Game::preGame()->count(),
            'open' => $open,
            'active' => Game::active()->count(),
        ];
    }
}
