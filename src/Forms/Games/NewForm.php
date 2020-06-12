<?php

namespace Diplomacy\Forms\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Services\Request;

class NewForm extends BaseForm
{
    protected $template = 'forms/games/new_game_form.twig';
    protected $requestType = Request::TYPE_POST;
    protected $submitFieldName = 'newGame';
    protected $nestedIn = 'newGame';
    protected $fields = [
        'name'                      => [
            'label' => 'Game Name',
        ],
        'bet'                       => [
            'label' => 'Bet Size',
            'default' => 5,
            'input' => [
                'size' => 7,
            ],
            'helpIcon' => [
                'title' => 'Bet',
                'text' => 'The bet required to join this game. This is the amount of points that all players, including you, must put into the game\'s pot (<a href=\'/help/points\' class=\'light\'>read more</a>).',
            ]
        ],
        'phase_minutes'             => [
            'type' => 'Games\PhaseLengthSelect',
        ],
        'phase_switch_period'       => [
            'type' => 'Games\PhaseSwitchPeriodSelect',
        ],
        'next_phase_minutes'        => [
            'type' => 'Games\NextPhaseLengthSelect',
        ],
        'join_period'               => [
            'type' => 'Games\JoinPeriodSelect',
        ],
        'press_type'                => [
            'type' => 'Games\PressTypeSelect',
        ],
        'variant_id'                => [
            'type' => 'Games\VariantSelect',
        ],
        'pot_type'                  => [
            'type' => 'Games\PotTypeSelect',
        ],
        'anon'                      => [
            'type'      => 'checkbox',
            'label'     => 'Anonymous players',
            'helpIcon'  => [
                'title' => 'Anonymous Players',
                'text'  => 'Decide if player names should be shown or hidden.</br></br> *Please note that games with no messaging are always anonymous regardless of what is set here to prevent cheating.',
            ],
        ],
        'draw_type'                 => [
            'type' => 'Games\DrawTypeSelect',
        ],
        'min_rr'                    => [
            'type'      => 'number',
            'label'     => 'Required Reliability Rating',
            'default'   => 80,
            'max'       => 100,
        ],
        'excused_missed_turns'      => [
            'type'      => 'number',
            'label'     => 'Excused delays per player',
            'default'   => 1,
            'max'       => 4,
            'helpIcon'  => [
                'title' => 'Excused delays per player',
                'text'  => 'The number of excused delays before a player is removed from the game and can be replaced.
                    If a player is missing orders at a deadline, the deadline will reset and the player will be
                    charged 1 excused delay. If they are out of excuses they will go into Civil Disorder.
                    The game will only progress with missing orders if no replacement is found within one phase of a player being forced into Civil Disorder.
                    Set this value low to prevent delays to your game, set it higher to be more forgiving to people who might need occasional delays.',
            ]
        ],
        'password'                  => ['type' => 'password', 'default' => ''],
        'password_confirmation'     => ['type' => 'password', 'default' => ''],
    ];

    protected $validationRules = [
        'name'      => 'required',
        'bet'       => 'between:0,1000',
        'password'  => 'confirmation',
    ];

    /** @var OptionsService $optionsService */
    protected $optionsService;

    public function setUp(): void
    {
        parent::setUp();
        $this->optionsService = new OptionsService();
    }

    public function handleSubmit()
    {
        var_dump($this->getValues()); die();
    }

    public function getVariants()
    {}

    public function beforeRender()
    {
        $this->setPlaceholders([
//            'variants' => OptionsService::getVariants((int)$this->getValue('variant_id')),
//            'phaseLengths' => $this->optionsService->getPhaseLengths((int)$this->getValue('phase_minutes')),
//            'switchPeriods' => $this->optionsService->getSwitchPeriods((int)$this->getValue('phase_switch_period')),
//            'nextPhaseLengths' => $this->optionsService->getNextPhaseLengths((int)$this->getValue('next_phase_minutes')),
//            'joinPeriods' => $this->optionsService->getJoinPeriods((int)$this->getValue('join_period')),
        ]);
    }
}