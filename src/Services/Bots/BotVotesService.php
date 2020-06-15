<?php

namespace Diplomacy\Services\Bots;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Status;
use Diplomacy\Models\Entities\User;

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
        global $DB;

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
        list($oldSC) = $DB->sql_row("SELECT COUNT(ts.terrID) FROM wD_TerrStatusArchive ts INNER JOIN wD_Territories t ON ( ts.terrID = t.id ) 
				WHERE t.supply='Yes' AND ts.countryID = ".$member->country->id." AND ts.gameID = ".$game->id." 
				AND t.mapID=".$game->variant->mapID." AND ts.turn = ".max(0,$game->currentTurn->id - 4));

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