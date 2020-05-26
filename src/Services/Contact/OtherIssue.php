<?php

namespace Diplomacy\Services\Contact;

class OtherIssue extends Issue
{
    public $subject = 'WebDip Generated Other Support Task';

    /**
     * @return string
     */
    public function getActualProblem() : string
    {
        switch ($this->code) {
            case 'rules':
                $actualProblem = 'user is requesting help with the rules';
                break;
            case 'otherGame':
                $actualProblem = 'user is requesting help with a game they are not in';
                break;
            case 'finishedGame':
                $actualProblem = 'user is requesting help with a finished game';
                break;
            case 'bug':
                $actualProblem = 'user is requesting help with a bug';
                break;
            case 'other':
            default:
                $actualProblem = 'user is requesting something else, check additional details';
                break;
        }
        return $actualProblem;
    }
}