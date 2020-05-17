<?php

namespace Diplomacy\Controllers;

use Twig\Environment as Twig;

abstract class BaseController
{
    /** @var Twig */
    protected $renderer;
    protected $database;
    protected $user;
    protected $template;

    public function __construct()
    {
        global $renderer, $DB, $User;
        $this->renderer = $renderer;
        $this->database = $DB;
        $this->user = $User;
        $this->setUp();
    }

    protected function setUp()
    {

    }

    abstract public function call();

    protected function getTemplate()
    {
        return $this->template;
    }

    public function render()
    {
        $variables = $this->call();
        return $this->renderer->render($this->getTemplate(), $variables);
    }

    public function renderPartial(string $partial, array $variables)
    {
        return $this->renderer->render($partial, $variables);
    }
}