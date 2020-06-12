<?php

namespace Diplomacy\Forms\Fields\Games;

use Diplomacy\Forms\Fields\SelectField;
use Diplomacy\Services\Games\OptionsService;
use Diplomacy\Views\Renderer;

/**
 * A select box for available variants
 *
 * @package Diplomacy\Forms\Fields
 */
class VariantSelectField extends SelectField
{
    public function __construct(Renderer $renderer, $name, $value, array $attributes = [], array $errors = [])
    {
        if (empty($attributes['label'])) $attributes['label'] = 'Variant type (map choices)';
        parent::__construct($renderer, $name, $value, $attributes, $errors);
    }

    /**
     * @return array
     */
    public function getDefaultOptions(): array
    {
        $variants = $this->getVariants();
        $data = [];
        foreach ($variants as $variant) {
            $data[] = ['value' => $variant['id'], 'text' => $variant['name']];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function helpIcon(): string
    {
        $variants = $this->getVariants();
        $variantText = [];
        foreach ($variants as $variant) {
            $variantText[] = "<li class='{$variant['name']}'>{$variant['link']}</li>";
        }
        $this->attributes['helpIcon'] = [
            'title' => 'Variant',
            'trigger' => 'click',
            'text' => '<p>Type of Diplomacy game from a selection of maps and alternate rule settings available. Click any of the variant names to view the details on the variants page.</p>
                    <p><strong>Available variants:</strong></p>
                    <ul class=\'variants\'>' . implode($variantText) . '</ul>
                    <p>Please note that 1 vs 1 games will default to a 5 point bet as an unranked game no matter what bet/game type are selected.</p>',
        ];
        return parent::helpIcon();
    }

    /**
     * @return array
     */
    protected function getVariants(): array
    {
        return OptionsService::getVariants();
    }
}