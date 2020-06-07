<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class BetWonComponent extends BaseComponent
{
    protected $template = 'games/members/betWon.twig';
    /** @var Member $member */
    protected $member;
    /** @var Game $game */
    protected $game;

    public function __construct(Member $member, Game $game)
    {
        $this->member = $member;
        $this->game = $game;
    }

    public function attributes(): array
    {



//        if ( $this->Game->phase == 'Pre-game' )
//            return l_t('Bet:').' <em>'.$this->bet.libHTML::points().'</em>';
//
//        if( $this->status == 'Playing' || $this->status == 'Left' )
//        {
//            $buf .= l_t('worth:').' <em';
//            $value = $this->Game->Scoring->pointsForDraw($this);
//            if ( $value > $this->bet )
//                $buf .= ' class="good"';
//            elseif ( $value < $this->bet )
//                $buf .= ' class="bad"';
//
//            $buf .= '>'.$value.libHTML::points().'</em>';
//            return $buf;
//        }
//        elseif ( $this->status == 'Won' || ($this->Game->potType == 'Points-per-supply-center' &&  $this->status == 'Survived') || $this->status == 'Drawn' )
//        {
//            $buf .= l_t('won:').' <em';
//            $value = $this->pointsWon;
//            if ( $value > $this->bet )
//                $buf .= ' class="good"';
//            elseif ( $value < $this->bet )
//                $buf .= ' class="bad"';
//
//            $buf .= '>'.$value.libHTML::points().'</em>';
//            return $buf;
//        }
//        else
//        {
//            return l_t('Bet:').' <em class="bad">'.$this->bet.libHTML::points().'</em>';
//        }

        $playingOrLeft = $this->member->status->isPlaying() || $this->member->status->left();
        $hasWonPoints = $this->member->status->won() || ($this->game->potType->grantsPointsOnSurvivals() && $this->member->status->survived()) || $this->member->status->drew();

        $betValue = $this->game->potType->amount;

        if ($playingOrLeft) {
            $betValue = $this->game->potType->pointsForDraw($this->game, $this->member);
        } elseif ($hasWonPoints) {
            $betValue = $this->member->pointsWon;
        }
        $betClass = $betValue > $this->member->bet ? 'good' : 'bad';
        return [
            'bet' => $this->member->bet,
            'betValue' => $betValue,
            'betClass' => $betClass,
            'hasWonPoints' => $hasWonPoints,
            'member' => $this->member,
            'pointsIcon' => \libHTML::points(),
        ];
    }
}