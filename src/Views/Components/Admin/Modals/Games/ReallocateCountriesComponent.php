<?php
namespace Diplomacy\Views\Components\Admin\Modals\Games;

/**
 * @package Diplomacy\Views\Components\Admin
 */
class ReallocateCountriesComponent extends BaseGameFormModalComponent
{
    /** @var string $template */
    protected $template = 'admin/modals/games/reallocate_countries.twig';
    protected $modalTitle = 'Reallocate Countries';
    protected $modalId = 'admin-game-reallocate-countries';
    protected $modalSubmitText = 'Change';
    protected $formAction = '/admin/games/:game_id/members/reallocate';

    /**
     * @return array
     */
    public function attributes(): array
    {
        $attributes = parent::attributes();
        $attributes['countries'] = $this->game->countries;
        return $attributes;
    }
}