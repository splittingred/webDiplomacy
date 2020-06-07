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
    protected $currentUser;

    public function __construct(Game $game, Member $member, \User $currentUser)
    {
        $this->game = $game;
        $this->member = $member;
        $this->currentUser = $currentUser;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'game' => $this->game,
            'currentUser' => $this->currentUser,
            'member' => $this->member,
            'memberName' => $this->member->getRenderedName($this->game, $this->currentUser->id),
            'showNames' => !$this->game->isMemberNameHidden($this->member, $this->currentUser->id),
            'messagesFromLink' => $this->member->messagesFromLink($this->currentUser),
            'isSelf' => $this->member->isUser($this->currentUser),
            'votes' => (string)(new MemberVotesComponent($this->member, $this->game, $this->currentUser)),
        ];
    }
}