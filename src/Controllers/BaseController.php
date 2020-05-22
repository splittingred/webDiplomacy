<?php

namespace Diplomacy\Controllers;

use Diplomacy\Services\Request;
use libHTML;
use Twig\Environment as Twig;
use Twig\Error\Error as TwigError;

abstract class BaseController
{
    use Placeholders;

    /** @var Twig */
    protected $renderer;
    /** @var \Database */
    protected $database;
    /** @var \User */
    protected $user;
    /** @var Request */
    protected $request;
    /** @var string */
    protected $template;
    /** @var string */
    protected $pageTitle = '';
    /** @var string */
    protected $pageDescription = '';

    /** @var array */
    protected $footerIncludes = [];
    /** @var array */
    protected $footerScripts = [];
    /** @var array */
    protected $noticeMappings = [];

    public function __construct()
    {
        global $renderer, $DB, $User;
        $this->renderer = $renderer;
        $this->database = $DB;
        $this->user = $User;
        $this->request = new Request();
        $this->setUp();
    }

    protected function setUp()
    {

    }

    abstract public function call();

    /**
     * @return string
     */
    protected function getTemplate() : string
    {
        return $this->template;
    }

    /**
     * @param array $parameters
     */
    public static function handle(array $parameters = []) : void
    {
        $controller = new static();
        $controller->request->setParameters($parameters);
        echo $controller->render();
    }

    public function beforeRender() : void
    {

    }

    public function afterRender() : void
    {

    }

    /**
     * @return string
     */
    public function render() : string
    {
        try {
            $this->setDefaultPlaceholders();

            $header = libHTML::starthtml($this->pageTitle, false);

            if (!empty($this->pageTitle)) {
                $pageHeader = $this->renderer->render('common/page_title.twig', [
                    'title' => $this->pageTitle,
                    'description' => $this->pageDescription,
                ]);
            } else {
                $pageHeader = '';
            }

            $this->beforeRender();
            $variables = $this->call();
            $this->afterRender();

            if (empty($variables)) $variables = [];
            $variables = array_merge([
                'notice' => $this->getNotice(),
            ], $variables, $this->getPlaceholders());

            $body = $this->renderer->render($this->getTemplate(), $variables);

            if (!empty($this->footerScripts)) libHTML::$footerScript = $this->footerScripts;
            if (!empty($this->footerIncludes)) libHTML::$footerIncludes = $this->footerIncludes;

            $footer = libHTML::footer(false);
            return $header . "\n" . $pageHeader . "\n" . $body . "\n" . $footer;
        } catch (TwigError $e) {
            return $e->getMessage();
        }
    }

    /**
     * Sets default placeholders used on nearly all templates
     *
     * @return void
     */
    protected function setDefaultPlaceholders() : void
    {
        $this->setPlaceholder('user', $this->user);
        $this->setPlaceholder('moderator_email', \Config::$modEMail ? \Config::$modEMail : \Config::$adminEMail);
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
        } catch (TwigError $e) {
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