<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class NameComponent extends BaseComponent
{
    protected string $template = 'games/members/name.twig';
    protected Member $member;
    protected Game $game;
    protected Member $currentMember;

    public function __construct(Member $member, Game $game, Member $currentMember)
    {
        $this->member = $member;
        $this->game = $game;
        $this->currentMember = $currentMember;
    }

    public function attributes(): array
    {

        return [
            'hidden' => $this->game->isMemberNameHidden($this->member, $this->currentMember),
            'member' => $this->member,
            'pointsIcon' => \libHTML::points(),
        ];
        // .(defined('AdminUserSwitch') ? ' (<a href="board.php?gameID='.$this->gameID.'&auid='.$this->userID.'" class="light">+</a>)':'');
    }
}