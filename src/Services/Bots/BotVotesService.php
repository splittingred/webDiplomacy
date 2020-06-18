<?php

namespace Diplomacy\Services\Bots;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Status;
use Diplomacy\Models\Entities\User;
use Diplomacy\Models\Territory;
use Diplomacy\Models\TerritoryStatusArchive;
use Illuminate\Database\Eloquent\Builder;

class BotVotesService
{
    public static $possibleVotes = ['Draw','Pause','Cancel','Concede'];

    /**
     * @param Member $member
     * @param Game $game
     * @return array
     */
    public function getForGame(Member $member, Game $game)
    {
        $membersPlaying = $game->members->allWithStatus(Status::STATUS_ACTIVE);

        //Each bot will automatically vote for a pause
        $botVotes = ['Pause'];
        $botSC = $member->supplyCenterCount;

        //This loop checks to see if the bot is winning or tied for the lead, since it will not vote draw or cancel in these cases
        foreach($membersPlaying as $currentMember)
        {
            if ($botSC < $currentMember->supplyCenterCount)
            {
                $botVotes = ['Draw','Pause','Cancel'];
            }
        }

        //This variable and the following query get the SC count for 2 years ago, which will be used to prevent bot stalemates
        $oldSC = 0;

        /** @var Builder $q */
        $oldSC = \Diplomacy\Models\TerritoryStatusArchive::query()
            ->join(Territory::getTableName(), TerritoryStatusArchive::getTableName().'.terrID', '=', Territory::getTableName().'.id')
            ->forCountry($member->country->id)
            ->forGame($game->id)
            ->forTurn(max(0, $game->currentTurn->id - 4))
            ->where(Territory::getTableName().'.supply', '=', 'Yes')
            ->where(Territory::getTableName().'.mapID', '=', $game->variant->mapID)
            ->count(TerritoryStatusArchive::getTableName().'.terrID');

        //A bot will draw or cancel if it is stalled out or if it is the first year
        if ($oldSC >= $botSC || $game->currentTurn->id < 2)
        {
            $botVotes = ['Draw','Pause','Cancel'];
        }
        //A bot will always cancel in the first two years
        elseif ($game->currentTurn->id < 4)
        {
            $botVotes = ['Pause','Cancel'];
        }
        return array_intersect(static::$possibleVotes, $botVotes);
    }
}