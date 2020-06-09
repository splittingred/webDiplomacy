<?php
namespace Diplomacy\Views\Components\Games\ChatBox;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class TabsComponent extends BaseComponent
{
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
        $attributes = [
            'tabs' => [],
        ];

        for($countryID = 0; $countryID <= $this->game->getCountryCount(); $countryID++)
        {
            $member = $this->game->members->byCountryId($countryID);

            // Do not allow country specific tabs for restricted press games.
            if (!$this->game->pressType->allowPrivateMessages()) continue;

            $hrefPrefix = '<a href="/games/'.$this->game->id.'/view?msgCountryID='.$member->country->id.'#chatboxanchor" '.
                'class="country'.$countryID.' '.( $this->targetCountryId == $countryID ? ' current"'
                    : '" title="'.l_t('Open %s chatbox tab"',( $countryID == 0 ? 'the global' : $member->country->name."'s" )) ).'>';

            if ($this->currentMember->id == $member->id)
            {
                $tabs .= $hrefPrefix . 'Notes';
            }
            elseif ($member->isFilled())
            {
                $tabs .= $member->getRenderedCountryName($this->game, $this->currentMember->user);
            }
            else
            {
                $tabs .= $hrefPrefix . 'Global';
            }

            if ($this->targetCountryId != $countryID && in_array($countryID, $this->currentMember->newMessagesFrom) )
            {
                // This isn't the tab I am currently viewing, and it has sent me new messages
                $tabs .= ' '.libHTML::unreadMessages();
            } elseif ($this->targetCountryId == $countryID && isset($_REQUEST['MarkAsUnread'])) {
                // Mark as unread patch!
                $tabs .= ' ' . libHTML::unreadMessages();
            }

            $tabs .= '</a>';
        }

        return $attributes;
    }
}