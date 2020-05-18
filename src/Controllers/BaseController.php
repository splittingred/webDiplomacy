<?php

namespace Diplomacy\Controllers;

use libHTML;
use Twig\Environment as Twig;

abstract class BaseController
{
    /** @var Twig */
    protected $renderer;
    protected $database;
    protected $user;
    protected $template;
    protected $pageTitle = 'webDiplomacy';

    protected $footerIncludes = [];
    protected $footerScripts = [];

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
        $header = libHTML::starthtml($this->pageTitle, false);
        $body = $this->renderer->render($this->getTemplate(), $variables);
        $footer = libHTML::footer(false);
        return $header . "\n" . $body . "\n" . $footer;
    }

    public function renderPartial(string $partial, array $variables)
    {
        return $this->renderer->render($partial, $variables);
    }
}