<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class ProgressBarComponent extends BaseComponent
{
    protected $template = 'games/members/progressBar.twig';
    /** @var Member $member */
    protected $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    /**
     * $Remaining
     * $SCEqual, ($Remaining)
     * $SCEqual, $UnitDeficit, ($Remaining)
     * $SCEqual, $UnitSurplus, ($Remaining)
     * @return array
     */
    public function attributes(): array
    {
        \libHTML::$first = true;

        $hasNoPieces = $this->member->hasNoPieces();
        $attributes = [
            'hasNoPieces' => $hasNoPieces,
            'supplyCenterTarget' => $this->member->supplyCenterTarget,
        ];
        if (!$hasNoPieces) {
            $dividers = [];
            if ($this->member->hasUnitDeficit()) {
                $dividers[$this->member->unitCount] = 'SCs';
                $dividers[$this->member->supplyCenterCount] = 'UnitDeficit';
            } else {
                $dividers[$this->member->supplyCenterCount] = 'SCs';
                if ($this->member->hasUnitSurplus()) $dividers[$this->member->unitCount] = 'UnitSurplus';
            }

            $lastNumber = 0;
            $number = 0;
            $segments = [];
            foreach ($dividers as $number => $type) {
                if (($number - $lastNumber) == 0) continue;
                if ($lastNumber == $this->member->supplyCenterTarget) break;
                if ($number > $this->member->supplyCenterTarget) $number = $this->member->supplyCenterTarget;

                $width = round(($number - $lastNumber) / $this->member->supplyCenterTarget * 100);

                $segments[] = [
                    'type' => $type,
                    'width' => $width,
                    'first' => \libHTML::first(),
                ];
                $lastNumber = $number;
            }
            $attributes['segments'] = $segments;
            $belowSupplyCenterTarget = $number < $this->member->supplyCenterTarget;
            $attributes['belowSupplyCenterTarget'] = $belowSupplyCenterTarget;
            if ($belowSupplyCenterTarget) {
                $width = round(($this->member->supplyCenterTarget - $number) / $this->member->supplyCenterTarget * 100);
                $attributes['remainingTargetWidth'] = $width;
            }
        }

        return $attributes;
    }
}