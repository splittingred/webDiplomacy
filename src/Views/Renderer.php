<?php

namespace Diplomacy\Views;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class Renderer extends Environment
{
    public static function getInstance()
    {
        global $User;
        $loader = new FilesystemLoader(ROOT_PATH . 'templates');
        $env = new static($loader, [
            'cache' => ROOT_PATH . '/cache/templates',
            'debug' => true,
        ]);
        $env->addGlobal('user', $User);
        return $env;
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
            return $e->getMessage() . ' - <pre>'.$e->getTraceAsString(). '</pre>';
        }
    }
}