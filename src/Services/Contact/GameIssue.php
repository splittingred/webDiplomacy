<?php

namespace Diplomacy\Services\Contact;

class GameIssue extends Issue
{
    public $subject = 'WebDip Generated Game Support Task';

    /**
     * @return string
     */
    public function getActualProblem() : string
    {
        switch ($this->code) {
            case 'pause':
                $this->subject .= 'URGENT-Pause';
                $actualProblem = 'user is requesting a pause';
                break;
            case 'unpause':
                $actualProblem = 'user is requesting an un-pause';
                break;
            case 'cheating':
                $actualProblem = 'user is requesting a cheating investigation';
                break;
            case 'orders':
                $actualProblem = 'user is requesting help with orders';
                break;
            case 'replace':
                $actualProblem = 'user is requesting to be replaced';
                break;
            case 'stalemate':
                $actualProblem = 'user is requesting a stalemate investigation';
                break;
            case 'wfo':
                $actualProblem = 'user is requesting help with a wfo game';
                break;
            case 'crash':
                $actualProblem = 'user is requesting help with a crashed game';
                break;
            case 'other':
            default:
                $actualProblem = 'user is requesting something else, check additional details';
                break;
        }
        return $actualProblem;
    }
}