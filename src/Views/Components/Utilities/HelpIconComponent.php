<?php

namespace Diplomacy\Views\Components\Utilities;

use Diplomacy\Views\Components\BaseComponent;

class HelpIconComponent extends BaseComponent
{
    protected $template = 'forms/fields/help_icon.twig';

    /** @var string $id */
    protected $id;
    /** @var string $text */
    protected $text;
    /** @var string $title */
    protected $title;
    /** @var string $toggle */
    protected $toggle;
    /** @var string $trigger */
    protected $trigger;
    /** @var string $alt */
    protected $alt;
    /** @var bool|string $delay */
    protected $delay;

    /**
     * @param string $id
     * @param string $text
     * @param string $title
     * @param string $toggle
     * @param string $trigger
     * @param string $alt
     * @param bool|string $delay
     */
    public function __construct(
        string $id,
        string $text,
        string $title = '',
        string $toggle = 'popover',
        string $trigger = 'hover',
        string $alt = 'Help',
        $delay = false
    ) {
        $this->id = $id;
        $this->text = $text;
        $this->title = $title;
        $this->toggle = $toggle;
        $this->trigger = $trigger;
        $this->alt = $alt;
        $this->delay = $delay;
    }

    public function attributes(): array
    {
        return [
            'id' => $this->id.'-help',
            'title' => $this->title,
            'text' => $this->text,
            'toggle' => $this->toggle,
            'trigger' => $this->trigger,
            'alt' => $this->alt,
            'delay' => $this->delay,
        ];
    }
}