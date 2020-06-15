<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Status;
use Diplomacy\Models\Entities\User;
use Diplomacy\Services\Bots\BotVotesService;

class VotesService
{
    public static $votes = ['Draw','Pause','Cancel','Concede'];

    /** @var BotVotesService $botVotesService */
    protected $botVotesService;

    public function __construct()
    {
        $this->botVotesService = new BotVotesService();
    }

    public function getForGame(Game $game)
    {
        $votes = self::$votes;

        $concede = 0;

        // gets the number of players that are still playing for use of the bot
        $activePlayerCount = $game->members->totalMembersLeftInGame();
        $membersPlaying = $game->members->allWithStatus(Status::STATUS_ACTIVE);

        // initialize an int to keep track of how many bots are playing
        $botCount = 0;
        /** @var Member $member */
        foreach($membersPlaying as $member)
        {
            //Bot votes are handled differently
            if ($member->user->hasRole(User::ROLE_BOT))
            {
                $botCount += 1;
                $votes = $this->botVotesService->getForGame($member, $game);
            }
            else
            {
                $votes = array_intersect($votes, $member->votes);
                if (in_array('Concede',$member->votes)) $concede++;
            }
        }

        //This condition will force draw the game if the only players left are bots
        if ($activePlayerCount == $botCount)
        {
            $votes = ['Draw'];
        }

        if ($concede >= ($activePlayerCount - 1) && ($concede > 0))
        {
            $votes[] = 'Concede';
        }

        return $votes;
    }
}