<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class PublicizeComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/publicize.twig';
    protected $modalId = 'admin-game-publicize';
    protected $modalTitle = 'Make Public';
    protected $modalSubmitText = 'Publicize';
    protected $formAction = '/admin/games/:game_id/publicize';
}