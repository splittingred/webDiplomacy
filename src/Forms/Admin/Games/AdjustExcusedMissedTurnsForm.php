<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Models\Member;
use Diplomacy\Services\Request;

class AdjustExcusedMissedTurnsForm extends BaseForm
{
    public string $id = 'admin-game-adjust-excused-missed-turns';
    protected string $name = 'admin-game-adjust-excused-missed-turns';
    protected string $template = 'forms/admin/games/adjust_excused_missed_turns.twig';
    protected string $requestType = Request::TYPE_POST;
    protected array $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
        'amount' => [
            'type' => 'number',
            'label' => 'Set To',
            'default' => 1,
            'min' => -100,
            'max' => 100,
        ]
    ];

    public function handleSubmit(): BaseForm
    {
        $gameEntity = $this->getGame();
        Member::forGame($gameEntity->id)->where('excusedMissedTurns', '>', 0)->update([
            'excusedMissedTurns' => (int)$this->getValue('amount'),
        ]);
        $this->redirectRelative($this->request->getCurrentUri());
        return $this;
    }
}

