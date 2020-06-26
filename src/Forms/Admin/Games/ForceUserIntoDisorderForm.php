<?php

namespace Diplomacy\Forms\Admin\Games;

use Diplomacy\Services\Request;

class ForceUserIntoDisorderForm extends BaseForm
{
    public string $id = 'admin-game-publicize';
    protected string $name = 'admin-game-publicize';
    protected string $template = 'forms/admin/games/force_user_into_disorder.twig';
    protected string $requestType = Request::TYPE_POST;

    /**
     * @return array
     */
    public function getFieldDefinitions(): array
    {
        $this->fields = [
            'game_id' => [
                'type'      => 'hidden',
                'default'   => 0,
            ],
            'user_id' => [
                'type'      => 'select',
                'label'     => 'User',
                'showAll'   => true,
                'options'   => $this->getUserOptions(),
            ]
        ];
        return $this->fields;
    }

    public function handleSubmit(): BaseForm
    {
        return $this;
    }

    /**
     * @return array
     */
    protected function getUserOptions(): array
    {
        $opts = [];
        $game = $this->getGame();
        if (empty($game)) return $opts;

        foreach ($game->members as $member) {
            $opts[] = [
                'value' => $member->user->id,
                'text' => ($member->user->username.' ('.$member->country->name.') - '.round($member->user->reliabilityRating, 2)).' RR',
            ];
        }
        return $opts;
    }
}

