<?php

namespace Diplomacy\Views\Components;

use Diplomacy\Views\CanAccessRequest;
use Diplomacy\Views\CanRender;

abstract class BaseComponent
{
    use CanRender;
    use CanAccessRequest;

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Do any actions before rendering the component
     */
    public function beforeRender(): void
    {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->beforeRender();
        return $this->renderer()->render($this->getTemplate(), $this->attributes());
    }

    /**
     * @return string
     */
    protected function getTemplate(): string
    {
        return $this->template;
    }
}