<?php
namespace Diplomacy\Views\Components\Games\ChatBox;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class TabsComponent extends BaseComponent
{
    protected string $template = 'games/chatbox/tabs.twig';
    protected Game $game;
    protected ?Member $currentMember;
    protected int $targetCountryId;

    /**
     * @param Game $game
     * @param Member $currentMember
     * @param int $targetCountryId
     */
    public function __construct(Game $game, Member $currentMember, int $targetCountryId = 0)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
        $this->targetCountryId = $targetCountryId > Country::GLOBAL && $targetCountryId < $this->game->getCountryCount() ? (int)$targetCountryId : Country::GLOBAL;
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
        for ($countryId = 0; $countryId <= $this->game->getCountryCount(); $countryId++)
        {
            $member = $this->game->members->byCountryId($countryId);
            $isCurrent = $this->targetCountryId == $member->country->id;
            $isGlobal = $countryId == 0; // only the first
            $isSelf = $this->currentMember->id == $member->id;
            $isCountry = !$isGlobal && !$isSelf;

            $tab = [
                'countryId' => $member->country->id,
                'countryName' => $isGlobal ? 'Global' : $member->country->name,
                'current' => $isCurrent,
                'currentCls' => $isCurrent ? 'current' : '',
                'isGlobal' => $isGlobal,
                'isCountry' => $isCountry,
                'isAssigned' => $member->isAssigned(),
            ];

            if ($isSelf) $tab['countryName'] = 'Notes';

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