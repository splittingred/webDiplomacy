<?php

namespace Diplomacy\Forms\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;

class NewForm extends BaseForm
{
    protected $template = 'forms/games/new_game_form.twig';
    protected $requestType = Request::TYPE_POST;
    protected $fields = [
        'name' => '',
    ];

    public function beforeRender()
    {
        $this->setPlaceholders([
            'variants' => $this->getVariants(),
        ]);
    }

    protected function getVariants()
    {
        $variants = [];
        foreach(\Config::$variants as $variantID => $variantName)
        {
            if ($variantID == 57) continue;
            $variants[] = \libVariant::loadFromVariantName($variantName);
        }
        return $variants;
    }
}