<?php

namespace Diplomacy\Views\Components\Users\Profile;

use Diplomacy\Views\Components\BaseComponent;

/**
 * @package Diplomacy\Views\Components\Users\Profile
 */
class RankingStatsPanelComponent extends BaseComponent
{
    protected string $template = 'users/profile/ranking_stats_panel.twig';
    protected string $title;
    protected array $stats;
    protected bool $show;

    public function __construct(string $title, array $stats, bool $show = false)
    {
        $this->title = $title;
        $this->stats = $stats;
        $this->show = $show;
    }

    public function attributes(): array
    {
        return [
            'title' => $this->title,
            'stats' => $this->stats,
            'show'  => $this->show,
        ];
    }
}