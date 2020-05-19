<?php

namespace Diplomacy\Controllers\Variants;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Services\Variants\VariantsService;

class IndexController extends BaseController
{
    protected $template = 'pages/variants/index.twig';
    protected $pageTitle = 'webDiplomacy variants';
    protected $pageDescription = 'A list of the variants available on this server, with credits and information on variant-specific rules.';

    protected $variantsService;

    public function setUp()
    {
        $this->variantsService = new VariantsService($this->database);
    }

    public function call()
    {
        return [
            'enabled_variants' => $this->variantsService->getActive(),
            'disabled_variants' => $this->variantsService->getDisabled(),
        ];
    }
}