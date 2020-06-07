<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class NameComponent extends BaseComponent
{
    protected $template = 'games/members/name.twig';
    /** @var Member $member */
    protected $member;
    /** @var Game $game */
    protected $game;
    /** @var int $currentUserId */
    protected $currentUserId;

    public function __construct(Member $member, Game $game, int $currentUserId = 0)
    {
        $this->member = $member;
        $this->game = $game;
        $this->currentUserId = $currentUserId;
    }

    public function attributes(): array
    {

        return [
            'hidden' => $this->game->isMemberNameHidden($this->member, $this->currentUserId),
            'member' => $this->member,
            'pointsIcon' => \libHTML::points(),
        ];
        // .(defined('AdminUserSwitch') ? ' (<a href="board.php?gameID='.$this->gameID.'&auid='.$this->userID.'" class="light">+</a>)':'');
    }
}