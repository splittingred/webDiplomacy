<?php

namespace Diplomacy\Services\Games\Creation;

use Diplomacy\Models\Entities\User;
use Diplomacy\Services\Monads\Result;

/**
 * Represents a request to create a new game. Automatically sanitizes incoming fields to correct objects.
 *
 * @package Diplomacy\Services\Games\Creation
 */
class Request
{
    /** @var User $user */
    public $currentUser;
    /** @var string $name */
    public $name;
    /** @var int $bet */
    public $bet;
    /** @var int $phaseMinutes */
    public $phaseMinutes;
    /** @var int $phaseSwitchPeriod */
    public $phaseSwitchPeriod;
    /** @var int $nextPhaseMinutes */
    public $nextPhaseMinutes;
    /** @var int $joinPeriod */
    public $joinPeriod;
    /** @var string $pressTypeId */
    public $pressTypeId;
    /** @var int $variantId */
    public $variantId;
    /** @var string $potTypeId */
    public $potTypeId;
    /** @var bool $anon */
    public $anon;
    /** @var int $drawTypeId */
    public $drawTypeId;
    /** @var int $minRr */
    public $minRr;
    /** @var int $excusedMissedTurns */
    public $excusedMissedTurns;
    /** @var string $password */
    public $password;
    /** @var string $passwordConfirmation */
    public $passwordConfirmation;
    /** @var string $missingPlayerPolicy */
    public $missingPlayerPolicy;
    /** @var bool $botFill */
    public $botFill;
    /** @var string $playersType */
    public $playersType;

    public function __construct(array $values, User $currentUser)
    {
        $this->currentUser = $currentUser;
        foreach ($values as $key => $value) {
            $trueKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
            if (property_exists($this, $trueKey)) {
                $this->{$trueKey} = $value;
            }
        }
        $this->sanitize();
    }

    public function sanitize(): Request
    {
        // type casting
        $this->bet = (int)$this->bet;
        $this->phaseMinutes = (int)$this->phaseMinutes;
        $this->phaseSwitchPeriod = (int)$this->phaseSwitchPeriod;
        $this->nextPhaseMinutes = (int)$this->nextPhaseMinutes;
        $this->joinPeriod = (int)$this->joinPeriod;
        $this->variantId = (int)$this->variantId;
        $this->anon = (int)$this->anon ? true : false;
        $this->minRr = (int)$this->minRr;
        $this->excusedMissedTurns = (int)$this->excusedMissedTurns;
        $this->botFill = $this->botFill == 'Yes';

        if (empty($this->missingPlayerPolicy)) $this->missingPlayerPolicy = 'Normal';

        // If a game is not live, set the next phase minutes to match the phase.
        if ($this->phaseMinutes > 60)
        {
            $this->nextPhaseMinutes = $this->phaseMinutes;
            $this->phaseSwitchPeriod = -1;
        }

        if ($this->phaseMinutes < 61 && $this->phaseSwitchPeriod != -1)
        {
            // If the next phase minutes is less than 1 day or more than 10 because someone is messing around with the console, default to 2 days if the game is live.
            if ($this->nextPhaseMinutes < 1440 || $this->nextPhaseMinutes > 14400) $this->nextPhaseMinutes = 2880;
            // If the phase Switch period is outside the allowed range default it to 3 hours.
            if ($this->phaseSwitchPeriod > 360 || $this->phaseSwitchPeriod < $this->phaseMinutes) $this->phaseSwitchPeriod = 180;
        }

        // Force 1 vs 1 variants to be unranked to prevent point farming.
        if ($this->variantId == 15 || $this->variantId == 23)
        {
            $this->bet = 5;
            $this->potTypeId = 'Unranked';
        }

        // Only classic, no press can support fill with bots.
        if ($this->variantId != 1 || $this->pressTypeId != 'NoPress') $this->botFill = false;

        // Force anonymous if NoPress
        if ($this->pressTypeId == 'NoPress') $this->anon = true;

        // Force bot games to be no press and unranked.
        if ($this->botFill == 'Yes' || $this->botFill == true)
        {
            $this->botFill = true;
            $this->pressTypeId = 'NoPress';
            $this->potTypeId = 'Unranked';
            $this->bet = 5;
            $this->playersType = 'Mixed';
        } else {
            $this->playersType = 'Members';
        }

        // Force correct MPP
        if ($this->missingPlayerPolicy != 'Wait') $this->missingPlayerPolicy = 'Normal';

        // Force valid min RR values
        if ($this->minRr < 0 || $this->minRr > 100) $this->minRr = 80;

        // Force valid excusedMissedTurns
        if ($this->excusedMissedTurns < 0 || $this->excusedMissedTurns > 4 ) $this->excusedMissedTurns = 1;

        return $this;
    }

    /**
     * @return Result
     */
    public function submit(): Result
    {
        global $app;
        $command = new Command($this, $app->make('logger'));
        return $command->call();
    }
}