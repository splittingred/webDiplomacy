<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ForceUserIntoDisorderComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/force_user_into_disorder.twig';
    protected $formAction = '/admin/games/:game_id/users/disorders';
    protected $modalId = 'admin-game-force-user-cd';
    protected $modalTitle = 'Force User into Civil Disorder';
    protected $modalSubmitText = 'Force User';
}