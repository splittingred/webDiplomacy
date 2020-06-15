<?php

namespace Diplomacy\Views\Components\Games;

use Diplomacy\Models\Config;
use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Services\Games\VotesService;
use Diplomacy\Views\Components\BaseComponent;

class VotesComponent extends BaseComponent
{
    protected $template = 'games/votes.twig';

    /** @var Game $game */
    protected $game;
    /** @var Member $currentMember */
    protected $currentMember;
    /** @var VotesService $votesService */
    protected $votesService;

    /**
     * @param Game $game
     * @param Member $currentMember
     */
    public function __construct(Game $game, Member $currentMember)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
        $this->votesService = new VotesService();
    }

    public function attributes(): array
    {
        $show = $this->game->phase->isActive() && $this->currentMember->isAuthenticated();

        $vAllowed = \Members::$votes;
        $vSet = $this->currentMember->votes;
        $vPassed = $this->votesService->getForGame($this->game);

        $vCancel = [];
        $vVote = [];

        foreach($vAllowed as $vote)
        {
            // Set when the option to vote concede is allowed. Restrict it to games set via the config.
            if ($vote == 'Concede')
            {
                if ((empty(\Config::$concedeVariants)) || (in_array($this->game->variant->id, \Config::$concedeVariants)))
                {
                    if (in_array($vote, $vSet) && !in_array($vote, $vPassed)) {
                        $vCancel[] = $vote;
                    } else {
                        $vVote[] = $vote;
                    }
                }
            }
            else
            {
                if (in_array($vote, $vSet) && !in_array($vote, $vPassed)) {
                    $vCancel[] = $vote;
                } else {
                    $vVote[] = $vote;
                }
            }
        }

        $voteForm = $this->showVoteForm($vVote, $vCancel);

        return [
            'show' => $show,
            'helpText' => $this->getVotesHelp(),
            'form' => $voteForm,
        ];
    }

    public function getVotesHelp(): string
    {
        $buf = '<p><strong>Draw Vote: </strong></br>
					If all players vote draw, the game will be drawn. ';
        $buf .= $this->game->potType->getDescription();
        $buf .= ' ' . $this->game->drawType->getDescription();
        $buf.= '</p>';

        if ($this->game->processing->isPaused())
        {
            $buf .= '<a><strong>Unpause Vote: </strong></br>
						If all players vote unpause, the game will be unpaused. If a game is stuck paused, email the mods at <a href="mailto: '.\Config::$modEMail. '">'.\Config::$modEMail.'</a> for help.
					</p>';
        }
        else
        {
            $buf .= '<p><strong>Pause Vote: </strong></br>
						If all players vote pause, the game will be paused until all players vote unpause. If you need a game paused'. ($this->game->pressType == 'NoPress' ? '' : ' due to an emergency').', click on the Need Help? link just above this icon to contact the mods.
					</p>';
        }

        $buf .= '<p><strong>Cancel Vote: </strong></br>
					If all players vote cancel, the game will be cancelled. All points will be refunded, and the game will be deleted. Cancels are typically used in the first year or two of a game with missing players.
				</p>';

        if ($this->game->playersType->hasBots())
        {
            $buf .= '<p><strong>Bot Voting: </strong></br>
    The bots in this game do not get a pause or unpause vote, pausing and unpausing only counts human votes. <br><br>
				If a bot is winning a game and has gained supply centers in the last 4 turns, it will stop the game from being drawn or cancelled. Otherwise bot games can be drawn or cancelled anytime.  
			</p>';
        }
        return $buf;
    }

    /**
     * Returns the actual form, given the votes which can be voted for, and votes which can
     * be cancelled.
     *
     * @param array $vVote Allowed votes
     * @param array $vCancel Votes which can be cancelled
     * @return string
     */
    public function showVoteForm($vVote, $vCancel) : string
    {
        $buf = '<form onsubmit="return confirm(\'Are you sure you want to cast this vote?\');" action="board.php?gameID='.$this->game->id.'#votebar" method="post">';
        $buf .= '<input type="hidden" name="formTicket" value="'.\libHTML::formTicket().'" />';

        $buf .= '<div class="memberUserDetail">';

        foreach($vVote as $vote)
        {
            if ($vote == 'Pause' && $this->game->processing->isPaused()) $vote = 'Unpause';

            $buf .= '<input type="submit" class="form-submit" name="'.$vote.'" value="'.$vote.'" /> ';
        }
        $buf .= '</div></form>';
        $buf .= '<form onsubmit="return confirm(\'Are you sure you want to withdraw this vote?\');" action="/games/'.$this->game->id.'/view#votebar" method="post">';
        $buf .= '<input type="hidden" name="formTicket" value="'.\libHTML::formTicket().'" />';

        if( $vCancel )
        {
            $buf .= '<div class="memberGameDetail">Cancel: ';
            foreach($vCancel as $vote)
            {
                if ( $vote == 'Pause' && $this->game->processing->isPaused())
                    $vote = 'Unpause';

                $buf .= '<input type="submit" class="form-submit" name="'.$vote.'" value="'.$vote.'" /> ';
            }

            $buf .= '</div>';
        }

        $buf .= '</form>';

        $buf .= '<div style="clear:both"></div>';

        return $buf;
    }
}