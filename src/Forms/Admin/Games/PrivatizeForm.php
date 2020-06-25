<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Models\Game;
use Diplomacy\Services\Request;

class PrivatizeForm extends BaseForm
{
    public $id = 'admin-game-privatize';
    protected $name = 'admin-game-privatize';
    protected $template = 'forms/admin/games/privatize.twig';
    protected $requestType = Request::TYPE_POST;
    protected $fields = [
        'game_id' => [
            'type' => 'hidden',
            'default' => 0,
        ],
        'password'              => [
            'type' => 'password',
            'default' => ''
        ],
        'password_confirmation' => [
            'type' => 'password',
            'default' => '',
            'label' => 'Confirm Password',
        ],
    ];

    /**
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'password'              => 'required|min:8|confirmation',
            'password_confirmation' => 'required',
        ];
    }

    public function handleSubmit(): BaseForm
    {
        $password = filter_var($this->getValue('password'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $passwordConfirm = filter_var($this->getValue('password_confirmation'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        if ($password != $passwordConfirm) {
            return $this->setNotice('Passwords must match.');
        }

        $game = Game::find((int)$this->getValue('game_id'));
        $game->password = $password;
        $game->save();
        return $this;
    }
}

