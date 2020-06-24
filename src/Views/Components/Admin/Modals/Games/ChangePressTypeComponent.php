<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

use Diplomacy\Services\Games\OptionsService;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ChangePressTypeComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/change_press_type.twig';
    protected $modalId = 'admin-game-change-press-type';
    protected $modalTitle = 'Change Press Type';
    protected $modalSubmitText = 'Change';
    protected $formAction = '/admin/games/:game_id/press-type';

    /**
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'press_types' => OptionsService::getPressTypes((string)$this->game->pressType),
        ]);
    }
}