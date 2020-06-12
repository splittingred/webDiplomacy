<?php
namespace Diplomacy\Services\Games;

class OptionsService
{
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
    public static function getPhaseLengths(): array
    {
        $phaseLengths = [];
        $phaseLengthMinutes = [5, 7, 10, 15, 20, 30, 60, 120, 240, 360, 480, 600, 720, 840, 960, 1080, 1200, 1320,
            1440, 1440+60, 2160, 2880, 2880+60*2, 4320, 5760, 7200, 8640, 10080, 14400];
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
        $switchPeriodMinutes = [-1, 10, 15, 20, 30, 60, 90, 120, 150, 180, 210, 240, 270, 300, 330, 360];
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
        $nextPhaseLengthMinutes = [1440, 1440+60, 2160, 2880, 2880+60*2, 4320, 5760, 7200, 8640, 10080, 14400];
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
        $joinPeriodMinutes = [5,7, 10, 15, 20, 30, 60, 120, 240, 360, 480, 600, 720, 840, 960, 1080, 1200, 1320,
            1440, 1440+60, 2160, 2880, 2880+60*2, 4320, 5760, 7200, 8640, 10080, 14400, 20160];
        foreach ($joinPeriodMinutes as $duration)
        {
            $joinPeriods[] = [
                'value' => $duration,
                'text' => \libTime::timeLengthText($duration * 60),
            ];
        }
        return $joinPeriods;
    }
}