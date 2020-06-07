<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class MemberVotesComponent extends BaseComponent
{
    public $template = 'games/members/memberVotes.twig';
    /** @var Member $member */
    protected $member;
    /** @var Game $game */
    protected $game;
    /** @var \User $currentUser */
    protected $currentUser;

    public function __construct(Member $member, Game $game, \User $currentUser)
    {
        $this->member = $member;
        $this->game = $game;
        $this->currentUser = $currentUser;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        $votes = [];
        $memberIsSelf = $this->member->isUser($this->currentUser);
        $moderatorCanView = $this->currentUser->isModerator() && !$this->game->members->isUserInGame($this->currentUser);
        // Moderators can see draws in games they're not in
        $drawVotesHidden = $this->game->drawType->hideDrawVotes() && !$memberIsSelf && !$moderatorCanView;

        foreach ($this->member->votes as $voteName)
        {
            if ($voteName == 'Pause' && $this->game->processing->isPaused())
                $voteName = 'Unpause';

            // Do we hide draws?
            if ($voteName == 'Draw' && $drawVotesHidden)
            {
                $votes[] = '(Hidden Draw)';
                continue;
            }
            $votes[] = $voteName;
        }

        return [
            'drawVotesHidden' => $drawVotesHidden,
            'votes' => $votes,
        ];
    }
}