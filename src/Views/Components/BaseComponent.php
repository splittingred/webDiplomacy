<?php

namespace Diplomacy\Views\Components;

use Diplomacy\Views\CanRender;
use Diplomacy\Views\Renderer;

abstract class BaseComponent
{
    use CanRender;

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->renderer()->render($this->template, $this->attributes());
    }
}