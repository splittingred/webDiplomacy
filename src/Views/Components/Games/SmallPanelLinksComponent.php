<?php

namespace Diplomacy\Views\Components\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\WatchedGame;
use Diplomacy\Views\Components\BaseComponent;
use Illuminate\Database\Eloquent\Builder;

class SmallPanelLinksComponent extends BaseComponent
{
    protected $template = 'games/small_panel_links.twig';
    /** @var Game $game */
    protected $game;
    /** @var Member $currentMember */
    protected $currentMember;

    public function __construct(Game $game, Member $currentMember)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
    }

    public function attributes(): array
    {
        return [
            'game' => $this->game,
            'currentMember' => $this->currentMember,
            'watched' => $this->getWatched(),
            'notificationButtonText' => $this->getNotificationButtonText(),
        ];
    }

    /**
     * @return string
     */
    private function getNotificationButtonText(): string
    {
        $notificationButtonText = 'Toggle Notices';
        if ($this->currentMember->hideNotifications == 1) {
            $notificationButtonText = 'Enable Notices';
        } else if ($this->currentMember->hideNotifications == 0) {
            $notificationButtonText = 'Disable Notices';
        }
        return $notificationButtonText;
    }

    /**
     * @return bool
     */
    private function getWatched(): bool
    {
        return WatchedGame::query()
                ->forUser($this->currentMember->user->id)
                ->forGame($this->game->id)
                ->count() > 0;
    }
}