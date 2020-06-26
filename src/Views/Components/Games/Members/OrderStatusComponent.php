<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

class OrderStatusComponent extends BaseComponent
{
    protected string $template = 'games/members/orderStatus.twig';
    protected Member $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function attributes(): array
    {
        return [
            'member' => $this->member,
        ];
    }
}