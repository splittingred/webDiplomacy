<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class BarComponent extends BaseComponent
{
    protected $template = 'games/members/bar.twig';
    protected $game;
    protected $member;
    protected $currentMember;

    public function __construct(Game $game, Member $member, Member $currentMember = null)
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
            'game' => $this->game,
            'currentMember' => $this->currentMember,
            'currentUser' => $this->currentMember->user,
            'member' => $this->member,
            'memberName' => $this->member->getRenderedName($this->game, $this->currentMember->user->id),
            'showNames' => !$this->game->isMemberNameHidden($this->member, $this->currentMember->user->id),
            'messagesFromLink' => $this->member->messagesFromLink($this->currentMember->user),
            'isSelf' => $this->member->isUser($this->currentMember->user),
            'votes' => (string)(new MemberVotesComponent($this->member, $this->game, $this->currentMember->user)),
        ];
    }
}