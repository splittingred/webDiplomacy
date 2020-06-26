<?php

namespace Diplomacy\Views;

use Illuminate\Container\Container;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class Renderer extends Environment
{
    /**
     * @param Container $app
     * @return Renderer
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function initialize(Container $app)
    {
        $loader = new FilesystemLoader(ROOT_PATH . 'templates');
        $env = new Renderer($loader, [
            'cache' => ROOT_PATH . '/cache/templates',
            'debug' => true,
        ]);
        $env->addExtension(new \Diplomacy\Views\TwigComponent());
        $env->addGlobal('current_user', $app->make('user'));
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