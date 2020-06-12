<?php

namespace Diplomacy\Forms\Fields\Games;

use Diplomacy\Forms\Fields\SelectField;
use Diplomacy\Views\Renderer;

/**
 * @package Diplomacy\Forms\Fields
 */
class PotTypeSelectField extends SelectField
{
    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        if (!array_key_exists('label', $attributes)) $attributes['label'] = 'Scoring: (<a href="/help/points#DSS">See scoring types here</a>)';
        if (!array_key_exists('default', $attributes)) $attributes['default'] = 'Regular';
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    public function helpIcon(): string
    {
        $this->attributes['helpIcon'] = [
            'title' => 'Scoring',
            'text'  => '<p>This setting determines how points are split up if/when the game draws.</p>
                    <p>In <strong>Draw-Size Scoring</strong>, the pot is split equally between the remaining players when the game draws (this setting used to be called WTA).</p>
                    <p>In <strong>Sum-of-Squares</strong> scoring, the pot is divided depending on how many centers you control when the game draws.</p>
                    <p>In both <strong>Draw-Size Scoring</strong> and <strong>Sum-of-Squares</strong>, any solo winner receives the whole pot.</p>
                    <p><strong>Unranked</strong> games have no effect on your points at the end of the game; your bet is refunded whether you won, drew or lost.</p>',
        ];
        return parent::helpIcon();
    }

    protected function getDefaultOptions(): array
    {
        return [
            [
                'value' => 'Winner-takes-all',
                'text' => 'DSS (Equal split for draws)'],
            [
                'value' => 'Sum-of-squares',
                'text' => 'SoS (Weighted split on draw)'
            ],
            [
                'value' => 'Unranked',
                'text' => 'Unranked'
            ],
        ];
    }
}