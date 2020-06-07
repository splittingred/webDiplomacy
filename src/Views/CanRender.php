<?php

namespace Diplomacy\Views;

use Twig\Loader\FilesystemLoader;

trait CanRender
{
    protected $template = '';
    protected $renderer;

    /**
     * @return Renderer
     */
    public function renderer(): Renderer
    {
        if (!$this->renderer) {
            try {
                global $app;
                $this->renderer = $app->make('renderer');
            } catch (\Exception $e) {
                $loader = new FilesystemLoader(ROOT_PATH . 'templates');
                return new Renderer($loader, [
                    'cache' => ROOT_PATH . '/cache/templates',
                    'debug' => true,
                ]);
            }
        }
        return $this->renderer;
    }
}