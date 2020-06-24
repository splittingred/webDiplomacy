<?php
namespace Diplomacy\Services\Games;

class OptionsService
{

    const PHASE_LENGTHS = [5, 7, 10, 15, 20, 30, 60, 120, 240, 360, 480, 600, 720, 840, 960, 1080, 1200, 1320,
        1440, 1500, 2160, 2880, 3000, 4320, 5760, 7200, 8640, 10080, 14400];
    const NEXT_PHASE_LENGTHS = [1440, 1500, 2160, 2880, 3000, 4320, 5760, 7200, 8640, 10080, 14400];
    const SWITCH_PERIODS = [-1, 10, 15, 20, 30, 60, 90, 120, 150, 180, 210, 240, 270, 300, 330, 360];
    const JOIN_PERIODS = [5,7, 10, 15, 20, 30, 60, 120, 240, 360, 480, 600, 720, 840, 960, 1080, 1200, 1320,
        1440, 1440+60, 2160, 2880, 2880+60*2, 4320, 5760, 7200, 8640, 10080, 14400, 20160];
    const PRESS_TYPES = ['Regular', 'PublicPressOnly', 'NoPress', 'RulebookPress'];
    const POT_TYPES = ['Winner-takes-all', 'Sum-of-squares', 'Unranked'];
    const DRAW_TYPES = ['draw-votes-public', 'draw-votes-hidden'];

    /**
     * @return array
     */
    public static function getVariants()
    {
        $variants = [];
        foreach(\Config::$variants as $variantID => $variantName)
        {
            if ($variantID == 57) continue;
            $v = \libVariant::loadFromVariantName($variantName);
            $variants[] = [
                'id' => $v->id,
                'name' => $v->name,
                'description' => $v->description,
                'mapId' => $v->mapID,
                'link' => $v->link(),
            ];
        }
        return $variants;
    }

    /**
     * @return array
     */
    public static function getVariantIds(): array
    {
        return array_keys(\Config::$variants);
    }

    /**
     * @return array
     */
    public static function getPhaseLengths(): array
    {
        $phaseLengths = [];
        $phaseLengthMinutes = static::PHASE_LENGTHS;
        foreach ($phaseLengthMinutes as $duration)
        {
            $phaseLengths[] = [
                'value' => $duration,
                'text' => \libTime::timeLengthText($duration * 60),
            ];
        }
        return $phaseLengths;
    }

    /**
     * @return array
     */
    public static function getSwitchPeriods(): array
    {
        $switchPeriods = [];
        $switchPeriodMinutes = static::SWITCH_PERIODS;
        foreach ($switchPeriodMinutes as $duration)
        {
            $switchPeriods[] = [
                'value' => $duration,
                'text' => $duration == -1 ? 'No phase switch' : \libTime::timeLengthText($duration * 60),
            ];
        }
        return $switchPeriods;
    }

    /**
     * @return array
     */
    public static function getNextPhaseLengths(): array
    {
        $nextPhaseLengths = [];
        $nextPhaseLengthMinutes = static::NEXT_PHASE_LENGTHS;
        foreach ($nextPhaseLengthMinutes as $duration)
        {
            $nextPhaseLengths[] = [
                'value' => $duration,
                'text' => \libTime::timeLengthText($duration * 60),
            ];
        }
        return $nextPhaseLengths;
    }

    /**
     * @return array
     */
    public static function getJoinPeriods(): array
    {
        $joinPeriods = [];
        $joinPeriodMinutes = static::JOIN_PERIODS;
        foreach ($joinPeriodMinutes as $duration)
        {
            $joinPeriods[] = [
                'value' => $duration,
                'text' => \libTime::timeLengthText($duration * 60),
            ];
        }
        return $joinPeriods;
    }

    /**
     * @param string $selected
     * @return array
     */
    public static function getPressTypes(string $selected = ''): array
    {
        $types = [
            ['value' => 'Regular', 'text' => 'All'],
            ['value' => 'PublicPressOnly', 'text' => 'Global only'],
            ['value' => 'NoPress', 'text' => 'None (No messaging)'],
            ['value' => 'RulebookPress', 'text' => 'Per rulebook'],
        ];
        foreach ($types as &$type) {
            $type['selected'] = $type == $selected;
        }
        return $types;
    }
}