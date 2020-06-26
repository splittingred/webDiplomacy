<?php

namespace Diplomacy\Controllers\Variants;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Services\Variants\VariantsService;

class IndexController extends BaseController
{
    protected string $template = 'pages/variants/index.twig';
    protected string $pageTitle = 'webDiplomacy variants';
    protected string $pageDescription = 'A list of the variants available on this server, with credits and information on variant-specific rules.';
    protected VariantsService $variantsService;

    public function setUp(): void
    {
        $this->variantsService = new VariantsService($this->database);
    }

    public function call(): array
    {
        return [
            'enabled_variants' => $this->variantsService->getActive(),
            'disabled_variants' => $this->variantsService->getDisabled(),
        ];
    }
}