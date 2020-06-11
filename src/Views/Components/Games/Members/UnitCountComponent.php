<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class UnitCountComponent extends BaseComponent
{
    protected $template = 'games/members/unitCount.twig';
    /** @var Member $member */
    protected $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function attributes(): array
    {
        return [
            'supplyCenterCount' => $this->member->supplyCenterCount,
            'unitCount' => $this->member->unitCount,
            'unitCountCssClass' => $this->getUnitCountCssClass(),
        ];
    }

    /**
     * @return string
     */
    public function getUnitCountCssClass(): string
    {
        if ($this->member->unitCount < $this->member->supplyCenterCount)
            $unitStyle = 'good';
        elseif ($this->member->unitCount > $this->member->supplyCenterCount)
            $unitStyle = 'bad';
        else
            $unitStyle = 'neutral';
        return $unitStyle;
    }
}