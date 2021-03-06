<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class MemberNameComponent extends BaseComponent
{
    protected string $template = 'games/members/memberName.twig';
    protected Game $game;
    protected ?Member $member;
    protected ?Member $currentMember;

    /**
     * @param Game $game
     * @param Member $member
     * @param Member $currentMember
     */
    public function __construct(Game $game, Member $member, Member $currentMember)
    {
        $this->game = $game;
        $this->member = $member;
        $this->currentMember = $currentMember;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'member' => $this->member,
            'game' => $this->game,
            'isGlobal' => $this->member->country->isGlobal(),
            'isHidden' => $this->game->isMemberNameHidden($this->member, $this->currentMember),
        ];
    }
}