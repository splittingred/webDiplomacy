<?php

namespace Diplomacy\Forms\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\User;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Services\Games\Creation\Request as GameCreationRequest;
use Diplomacy\Services\Request;

class NewForm extends BaseForm
{
    protected $template = 'forms/games/new_game_form.twig';
    protected $requestType = Request::TYPE_POST;
    protected $action = '/games/new';
    protected $name = 'games-new';
    protected $formCls = 'new-game-form';
    protected $fields = [
        'new_game' => [
            'type' => 'hidden',
            'default' => 1,
        ],
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
        'press_type_id'             => [
            'type' => 'Games\PressTypeSelect',
        ],
        'variant_id'                => [
            'type' => 'Games\VariantSelect',
        ],
        'pot_type_id'               => [
            'type' => 'Games\PotTypeSelect',
        ],
        'anon'                      => [
            'type'      => 'checkbox',
            'label'     => 'Anonymous players',
            'default'   => 1,
            'helpIcon'  => [
                'title' => 'Anonymous Players',
                'text'  => 'Decide if player names should be shown or hidden.</br></br> *Please note that games with no messaging are always anonymous regardless of what is set here to prevent cheating.',
            ],
        ],
        'draw_type_id'              => [
            'type' => 'Games\DrawTypeSelect',
        ],
        'min_rr'                    => [
            'type'      => 'number',
            'label'     => 'Minimum Reliability Rating',
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

    /** @var OptionsService $optionsService */
    protected $optionsService;

    public function setUp(): void
    {
        parent::setUp();
        $this->optionsService = new OptionsService();
    }

    /** @var User $currentUser */
    protected $currentUser;
    public function setCurrentUser(User $user)
    {
        $this->currentUser = $user;
    }

    /**
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'name'          => 'required|max:49',
            'bet'           => 'between:5,'.$this->currentUser->points,
            'phase_minutes' => 'required|between:5,14400|in:'.join(',', OptionsService::PHASE_LENGTHS),
            'phase_switch_period' => 'required|between:5,14400|in:'.join(',', OptionsService::NEXT_PHASE_LENGTHS),
            'join_period'   => 'required|between:5,14400|in:'.join(',', OptionsService::JOIN_PERIODS),
            'press_type_id' => 'required|in:'.join(',', OptionsService::PRESS_TYPES),
            'variant_id'    => 'required|in:'.join(',', OptionsService::getVariantIds()),
            'pot_type_id'   => 'required|in:'.join(',', OptionsService::POT_TYPES),
            'draw_type_id'  => 'required|in:'.join(',', OptionsService::DRAW_TYPES),
            'min_rr'        => 'required|between:0,'.$this->currentUser->reliabilityRating,
            'excused_missed_turns' => 'required|between:-1,100',
            'password'      => 'confirmation',
        ];
    }

    public function handleSubmit(): BaseForm
    {
        if ($this->currentUser->isBanned() || $this->currentUser->temporaryBan) {
            $this->setPlaceholder('notice', 'You are banned from creating games.');
            return $this;
        }

        $values = $this->getValues();
        $request = new GameCreationRequest($values, $this->currentUser);
        $result = $request->submit();
        if ($result->successful()) {
            $this->redirectRelative('/games/'.$result->getValue()->id.'/view');
            close();
        } else {
            $this->setPlaceholder('notice', $result->getValue()->getMessage());
        }
        return parent::handleSubmit();
    }
}