<?php
namespace Diplomacy\Views\Components\Games\ChatBox;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class TabsComponent extends BaseComponent
{
    protected $template = 'games/chatbox/tabs.twig';

    /** @var Game $game */
    protected $game;
    /** @var Member|null $currentMember */
    protected $currentMember;
    /** @var int $targetCountryId */
    protected $targetCountryId;

    /**
     * @param Game $game
     * @param Member $currentMember
     * @param int $targetCountryId
     */
    public function __construct(Game $game, Member $currentMember, int $targetCountryId = 0)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
        $this->targetCountryId = $this->targetCountryId > Country::GLOBAL && $this->targetCountryId < $this->game->getCountryCount() ? (int)$targetCountryId : Country::GLOBAL;
    }

    public function attributes(): array
    {
        return [
            'game' => $this->game,
            'tabs' => $this->getTabs(),
            'privateMessagesAllowed' => $this->game->pressType->allowPrivateMessages(),
        ];
    }

    protected function getTabs(): array
    {
        $tabs = [];
        for($countryId = 0; $countryId <= $this->game->getCountryCount(); $countryId++)
        {
            $member = $this->game->members->byCountryId($countryId);
            $isCurrent = $this->targetCountryId == $member->country->id;

            $tab = [
                'countryId' => $member->country->id,
                'countryName' => $member->country->name,
                'current' => $isCurrent,
                'currentCls' => $isCurrent ? 'current' : '',
                'isGlobal' => $member->country->isGlobal(),
                'rendered' => '',
            ];

            if ($this->currentMember->id == $member->id)
            {
                $tab['countryName'] = 'Notes';
            }
            elseif ($member->isFilled())
            {
                $tab['rendered'] = $member->getRenderedCountryName($this->game, $this->currentMember->user);
            }

            if (!$isCurrent && in_array($member->country->id, $this->currentMember->newMessagesFrom) )
            {
                // This isn't the tab I am currently viewing, and it has sent me new messages
                $tab['unreadIcon'] = ' ' . \libHTML::unreadMessages();
            }
            $tabs[] = $tab;
        }
        return $tabs;
    }
}