<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class PrivatizeComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/privatize.twig';
    protected $modalId = 'admin-game-privatize';
    protected $modalTitle = 'Make Private';
    protected $modalSubmitText = 'Privatize';
    protected $formAction = '/admin/games/:game_id/privatize';
}