<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Models\Game;
use Diplomacy\Services\Request;

class PrivatizeForm extends BaseForm
{
    public string $id = 'admin-game-privatize';
    protected string $name = 'admin-game-privatize';
    protected string $template = 'forms/admin/games/privatize.twig';
    protected string $requestType = Request::TYPE_POST;
    protected array $fields = [
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

        $game = Game::find($this->getGame()->id);
        $game->password = $password;
        $game->save();
        return $this;
    }
}

