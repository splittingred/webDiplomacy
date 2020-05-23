<?php

namespace Diplomacy\Services\Variants;

use Config;
use Diplomacy\Models\Game;
use libVariant;
use WDVariant;

class VariantsService
{
    protected $database;

    public function __construct(\Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return array
     */
    public function getActive() : array
    {
        $variantFiles = glob(ROOT_PATH . 'variants/*');

        $variants = [];
        foreach ($variantFiles as $variantDir) {
            if (!is_dir($variantDir) || !file_exists($variantDir.'/variant.php')) continue;

            $variantRealName = basename($variantDir);
            $variant = libVariant::loadFromVariantName($variantRealName);
            if (in_array($variantRealName, Config::$variants)) {
                // TODO: make this not O(N)
                $num = Game::where('variantID', $variant->id)->where('phase', '!=', 'Pre-game')->count();
                $variant->setPlays($num);

                $variants[] = $variant;
            }
        }
        return $variants;
    }

    /**
     * @return array
     */
    public function getDisabled() : array
    {
        $variantFiles = glob(ROOT_PATH . 'variants/*');

        $variants = [];
        foreach ($variantFiles as $variantDir) {
            if (!is_dir($variantDir) || !file_exists($variantDir.'/variant.php')) continue;

            $variantRealName = $this->variantNameFromDirectory($variantDir);
            if ($this->isActive($variantRealName)) continue;

            $variant = $this->loadFromDirectory($variantDir);
            if ($variant) $variants[] = $variant;
        }
        return $variants;
    }

    /**
     * @param string $variantName
     * @return bool
     */
    public function isActive(string $variantName) : bool
    {
        return in_array($variantName, Config::$variants);
    }

    /**
     * @param string $variantDir
     * @return string
     */
    private function variantNameFromDirectory(string $variantDir) : string
    {
        return basename($variantDir);
    }

    /**
     * @param string $variantDir
     * @return WDVariant|null
     */
    private function loadFromDirectory(string $variantDir)
    {
        if (!is_dir($variantDir) || !file_exists($variantDir.'/variant.php')) {
            return null;
        }

        $variantRealName = $this->variantNameFromDirectory($variantDir);
        $variant = libVariant::loadFromVariantName($variantRealName);

        // TODO: make this not O(N)
        $num = Game::where('variantID', $variant->id)->where('phase', '!=', 'Pre-game')->count();
        $variant->setPlays($num);

        return $variant;
    }

}