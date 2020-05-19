<?php

namespace Diplomacy\Controllers;

use libHTML;
use Twig\Environment as Twig;
use Twig\Error\Error;

abstract class BaseController
{
    /** @var Twig */
    protected $renderer;
    protected $database;
    protected $user;
    protected $template;
    protected $pageTitle = '';
    protected $pageDescription = '';

    protected $footerIncludes = [];
    protected $footerScripts = [];
    protected $noticeMappings = [];

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
        $variables = array_merge([
            'notice' => $this->getNotice(),
        ], $variables);
        $header = libHTML::starthtml($this->pageTitle, false);

        if (!empty($this->pageTitle)) {
            $pageHeader = $this->renderer->render('common/page_title.twig', [
                'title' => $this->pageTitle,
                'description' => $this->pageDescription
            ]);
        } else {
            $pageHeader = '';
        }
        $body = $this->renderer->render($this->getTemplate(), $variables);

        if (!empty($this->footerScripts)) libHTML::$footerScript = $this->footerScripts;
        if (!empty($this->footerIncludes)) libHTML::$footerIncludes = $this->footerIncludes;

        $footer = libHTML::footer(false);
        return $header . "\n" . $pageHeader . "\n" . $body . "\n" . $footer;
    }

    /**
     * @param string $partial
     * @param array $variables
     * @return string
     */
    public function renderPartial(string $partial, array $variables) : string
    {
        try {
            return $this->renderer->render($partial, $variables);
        } catch (Error $e) {
            // TODO: log errors
            return $e->getMessage();
        }
    }

    /**
     * @param string $path
     */
    public function redirectRelative(string $path = '/') : void
    {
        $baseUrl = rtrim(\Config::$url, '/');
        $path = ltrim($path, '/');
        header("Location: {$baseUrl}/{$path}");
    }

    /**
     * @return string
     */
    public function getNotice() : string
    {
        $noticeKeys = array_key_exists('notice', $_REQUEST) ? strip_tags($_REQUEST['notice']) : '';
        $notices = explode(',', $noticeKeys);
        $output = [];
        foreach ($notices as $noticeKey) {
            $value = array_key_exists($noticeKey, $this->noticeMappings) ? $this->noticeMappings[$noticeKey] : '';
            $value = str_replace('{{ user.username }}', $this->user->username, $value);
            $output[] = $value;
        }
        return implode("\n", $output);
    }
}