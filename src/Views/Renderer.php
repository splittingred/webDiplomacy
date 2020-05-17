<?php

namespace Diplomacy\Views;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class Renderer extends Environment
{
    public static function getInstance()
    {
        $loader = new FilesystemLoader(ROOT_PATH . 'templates');
        return new static($loader, [
            'cache' => ROOT_PATH . '/cache/templates',
            'debug' => true,
        ]);
    }

    /**
     * @param string|TemplateWrapper $name
     * @param array $context
     * @return string
     */
    public function render($name, array $context = []): string
    {
        try {
            return $this->load($name)->render($context);
        } catch (\Exception $e) {
            // TODO: Log something here
            return '';
        }
    }
}