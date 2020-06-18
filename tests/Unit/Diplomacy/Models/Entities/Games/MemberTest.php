<?php

namespace Unit\Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\UnassignedMember;
use Support\TestCase;

class MemberTest extends TestCase
{
    public function testIsAssigned()
    {
        /** @var Member $member */
        $member = $this->factories()->instance(Member::class);
        $this->assertTrue($member->isAssigned());
    }
    public function testIsAssignedWhenUnassigned()
    {
        /** @var UnassignedMember $member */
        $member = $this->factories()->instance(UnassignedMember::class);
        $this->assertFalse($member->isAssigned());
    }
}
