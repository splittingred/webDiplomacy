<?php
namespace Diplomacy\Views;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigComponent extends AbstractExtension
{
    const CLASS_PREFIX = "\\Diplomacy\\Views\\Components\\";

    public function getFunctions()
    {
        return [
            new TwigFunction('component', [$this, 'loadComponent']),
        ];
    }

    /**
     * Dynamically create a component based on arguments from twig templates
     */
    public function loadComponent(): string
    {
        $class = func_get_arg(0);
        $class = str_replace('.', '\\', $class);
        $class .= 'Component';
        $classPrefix = static::CLASS_PREFIX;
        $fqn = "$classPrefix$class";
        $args = func_get_arg(1);

        $r = new \ReflectionClass($fqn);
        return (string)($r->newInstanceArgs($args));
    }
}