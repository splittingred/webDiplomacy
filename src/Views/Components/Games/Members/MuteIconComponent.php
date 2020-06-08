<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Services\Games\MembersService;
use Diplomacy\Services\Request;
use Diplomacy\Views\Components\BaseComponent;

class MuteIconComponent extends BaseComponent
{
    public $template = 'games/members/muteIcon.twig';
    /** @var Member $member */
    protected $member;
    /** @var Game $game */
    protected $game;
    /** @var Member $currentMember */
    protected $currentMember;
    /** @var bool $muted */
    protected $muted;
    /** @var MembersService $membersService */
    protected $membersService;

    /**
     * @param Game $game
     * @param Member $member
     * @param Member|null $currentMember
     */
    public function __construct(Game $game, Member $member, Member $currentMember = null)
    {
        $this->game = $game;
        $this->member = $member;
        $this->currentMember = $currentMember;
        $this->muted = $this->currentMember && $this->currentMember->hasMutedCountry($this->member->country->id);
        $this->membersService = new MembersService();
    }

    /**
     * Dynamically render depending on muted status
     *
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->muted ? '/games/members/unmuteIcon.twig' : '/games/members/muteIcon.twig';
    }

    /**
     * Handle if someone mutes or unmutes a country
     */
    public function beforeRender(): void
    {
        $toggleMute = $this->getRequest()->get('toggleMute', null, Request::TYPE_REQUEST);
        if (!empty($toggleMute) && $this->currentMember && $toggleMute == $this->member->country->id) {
            $this->membersService->toggleCountryMute($this->currentMember, $this->member->country);
        }
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        $show = $this->currentMember && !$this->member->isUser($this->currentMember->user->id);
        $url = '/board.php?gameID='.$this->game->id.'&toggleMute='.$this->member->country->id.'&rand='.rand(1,99999).'#chatboxanchor';
        return [
            'show' => $show,
            'url' => $url,
            'targetCountryName' => $this->member->country->name,
        ];
    }
}

