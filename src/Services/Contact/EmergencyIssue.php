<?php

namespace Diplomacy\Services\Contact;

class EmergencyIssue extends Issue
{
    public $subject = 'WebDip Generated Emergency Pause';

    /**
     * @return string
     */
    public function getActualProblem() : string
    {
        switch ($this->code) {
            case 'naturalDisaster':
                $actualProblem = 'user paused all games for a natural disaster impacting them';
                break;
            case 'medical':
                $actualProblem = 'user paused all games for a medical emergency';
                break;
            case 'powerOutage':
                $actualProblem = 'user paused all games due to a power outage';
                break;
            case 'other':
            default:
                $actualProblem = 'user is requesting something else, check additional details';
                break;
        }
        return $actualProblem;
    }
}