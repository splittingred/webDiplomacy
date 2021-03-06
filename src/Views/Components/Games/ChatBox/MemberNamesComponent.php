<?php
namespace Diplomacy\Views\Components\Games\ChatBox;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;
use Diplomacy\Views\Components\Games\Members\BarComponent as MemberBarComponent;

class MemberNamesComponent extends BaseComponent
{
    protected string $template = 'games/chatbox/memberNames.twig';
    protected Game $game;
    protected ?Member $currentMember;
    protected int $targetCountryId;
    protected bool $isGlobal;
    protected bool $isAll;
    protected bool $isAuthenticated;

    /**
     * @param Game $game
     * @param Member $currentMember
     * @param int $targetCountryId
     */
    public function __construct(Game $game, Member $currentMember, int $targetCountryId = 0)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
        $this->isAuthenticated = $this->currentMember->isAuthenticated();
        $this->targetCountryId = $targetCountryId >= Country::ALL && $targetCountryId < $this->game->getCountryCount() ? (int)$targetCountryId : Country::GLOBAL;
        $this->isGlobal = $this->targetCountryId == Country::GLOBAL;
        $this->isAll = $this->targetCountryId == Country::ALL;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [
            'game' => $this->game,
            'currentMember' => $this->currentMember,
            'isGlobal' => $this->isGlobal,
            'isAll' => $this->isAll,
        ];

        $currentMemberIsTargetCountry = $this->currentMember->isCountry($this->targetCountryId);
        if ($this->isGlobal || $this->isAll)
        {
            $attributes['members'] = $this->getMemberList();
        }
        else
        {
            $targetMember = $this->game->members->byCountryId($this->targetCountryId);
            $attributes['targetMemberBar'] = $this->getSingleMemberBar($targetMember);
        }

        return $attributes;
    }

    /**
     * @param Member $targetMember
     * @return string
     */
    protected function getSingleMemberBar(Member $targetMember): string
    {
        return (string)(new MemberBarComponent($this->game, $targetMember, $this->currentMember));
    }

    /**
     * @return array
     */
    protected function getMemberList()
    {
        $memberList = [];
        for ($countryID = 1; $countryID <= $this->game->getCountryCount(); $countryID++) {
            $member = $this->game->members->byCountryId($countryID);
            if ($member->isAssigned()) {
                $memberList[] = $member->getMemberNameForGame($this->game, $this->currentMember);
            }
        }
        return $memberList;
    }
}