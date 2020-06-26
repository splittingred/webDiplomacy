<?php

namespace Diplomacy\Controllers;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Models\User;
use Diplomacy\Models\Entities\User as UserEntity;
use Diplomacy\Services\Request;
use Diplomacy\Utilities\HasPlaceholders;
use Illuminate\Log\Logger;
use Illuminate\Validation\Factory as ValidationFactory;
use libHTML;
use Twig\Environment as Twig;
use Twig\Error\Error as TwigError;

abstract class BaseController
{
    use HasPlaceholders;

    /** @var Twig */
    protected Twig $renderer;
    protected \Database $database;
    protected ?\User $currentUser;
    protected ?UserEntity $currentUserEntity;
    protected Request $request;
    protected ValidationFactory $validationFactory;

    protected string $template;
    protected bool $renderPageTitle = true;
    protected string $pageTitle = '';
    protected string $pageDescription = '';
    protected int $perPage = 10;

    protected array $footerIncludes = [];
    protected array $footerScripts = [];
    protected array $noticeMappings = [];

    protected Logger $logger;
    protected ?BaseForm $form;

    public function __construct()
    {
        global $app;
        $this->renderer = $app->make('renderer');
        $this->database = $app->make('DB');
        $this->currentUser = $app->make('user');
        if (!empty($this->currentUser->id)) {
            $this->currentUserEntity = User::find($this->currentUser->id)->toEntity();
        }
        $this->request = $app->make('request');
        $this->validationFactory = $app->make('validation.factory');
        $this->logger = $app->make('logger');
        $this->setUp();
    }

    protected function setUp(): void
    {

    }

    abstract public function call(): array;

    /**
     * @return string
     */
    protected function getTemplate() : string
    {
        return $this->template;
    }

    /**
     * @param string $class
     * @param array $defaultValues
     * @return BaseForm
     */
    protected function makeForm(string $class, array $defaultValues = []): BaseForm
    {
        $this->form = new $class($this->request, $this->renderer, $this->validationFactory, $defaultValues);
        return $this->form;
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
            $this->beforeRender();

            $header = libHTML::starthtml($this->getPageTitle(), false);

            if (!empty($this->pageTitle) && $this->renderPageTitle) {
                $pageHeader = $this->renderer->render('common/page_title.twig', [
                    'title' => $this->getPageTitle(),
                    'description' => $this->getPageDescription(),
                ]);
            } else {
                $pageHeader = '';
            }

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
     * @return string
     */
    protected function getPageTitle() : string
    {
        return $this->pageTitle;
    }

    protected function getPageDescription() : string
    {
        return $this->pageDescription;
    }

    /**
     * Sets default placeholders used on nearly all templates
     *
     * @return void
     */
    protected function setDefaultPlaceholders() : void
    {
        $this->setPlaceholder('current_user', $this->currentUser);
        $this->setPlaceholder('current_user_entity', $this->currentUserEntity);
        $this->setPlaceholder('moderator_email', \Config::$modEMail ? \Config::$modEMail : \Config::$adminEMail);
        $this->setPlaceholder('admin_email', \Config::$adminEMail);
        $this->setPlaceholder('current_page', $this->getCurrentPage());
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
    public function redirectRelative(string $path = '/', $exit = false) : void
    {
        $baseUrl = rtrim(\Config::$url, '/');
        $path = ltrim($path, '/');
        header("Location: {$baseUrl}/{$path}");
        if ($exit) { close(); }
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
            $value = str_replace('{{ current_user.username }}', $this->currentUser->username, $value);
            $output[] = $value;
        }
        return implode("\n", $output);
    }

    /**
     * Get pagination links for any amount of items
     *
     * @param int $total
     * @return string
     */
    protected function getPagination(int $total) : string
    {
        $totalPages = $this->getTotalPages($total);
        $current = $this->getCurrentPage();
        if ($current <= 1) $current = 1;

        return $this->renderPartial('common/pagination/links.twig',[
            'current' => $current,
            'pages' => range(1, $totalPages),
            'cls' => 'form-submit',
            'current_cls' => 'curr-page',
        ]);
    }

    /**
     * @param int $total
     * @return int
     */
    public function getTotalPages(int $total) : int
    {
        return ceil($total / $this->perPage);
    }

    /**
     * @return int
     */
    public function getCurrentPage() : int
    {
        return (int)$this->request->get('page', 1, Request::TYPE_GET);
    }
}