<?php

namespace Unit\Diplomacy\Models\Entities\Games;

use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\UnassignedMember;
use Diplomacy\Models\Entities\User;
use Support\TestCase;

class MemberTest extends TestCase
{
    /**
     * @param string $type
     * @param array $attributes
     * @return Member|object
     */
    private function buildMember($type = '', array $attributes = [])
    {
        $type = !empty($type) ? $type : Member::class;
        return $this->factories()->instance($type, $attributes);
    }

    public function testIsAssigned()
    {
        /** @var Member $member */
        $member = $this->buildMember();
        $this->assertTrue($member->isAssigned());
    }
    public function testIsAssignedWhenUnassigned()
    {
        /** @var UnassignedMember $member */
        $member = $this->buildMember(UnassignedMember::class);
        $this->assertFalse($member->isAssigned());
    }

    public function testHasUnsubmittedOrders()
    {
        // With unsubmitted orders
        $member = $this->buildMember('unsubmitted-orders:'.Member::class);
        $this->assertTrue($member->hasUnsubmittedOrders());

        // Regular member
        $member = $this->buildMember(Member::class);
        $this->assertFalse($member->hasUnsubmittedOrders());
    }

    public function testIsCountry()
    {
        /** @var Country $england */
        $england = $this->factories()->instance(Country::class, ['id' => 1, 'name' => 'England']);
        /** @var Country $france */
        $france = $this->factories()->instance(Country::class, ['id' => 2, 'name' => 'France']);
        /** @var Country $global */
        $global = $this->factories()->instance(Country::class, ['id' => 0, 'name' => 'Global']);

        $member = $this->buildMember(Member::class);
        $this->assertTrue($member->isCountry($england));
        $this->assertFalse($member->isCountry($france));
        $this->assertFalse($member->isCountry($global));
    }

    public function testUnitsEqualToSupplyCenters()
    {
        $memberWithEqualSCs = $this->buildMember(Member::class, [
            'unitCount' => 3,
            'supplyCenterCount' => 3,
        ]);
        $this->assertTrue($memberWithEqualSCs->unitsEqualToSupplyCenters());

        $memberWithMoreUnits = $this->buildMember(Member::class, [
            'unitCount' => 4,
            'supplyCenterCount' => 3,
        ]);
        $this->assertFalse($memberWithMoreUnits->unitsEqualToSupplyCenters());

        $memberWithLessUnits = $this->buildMember(Member::class, [
            'unitCount' => 2,
            'supplyCenterCount' => 3,
        ]);
        $this->assertFalse($memberWithLessUnits->unitsEqualToSupplyCenters());
    }

    public function testHasUnitDeficit()
    {
        $memberWithEqualSCs = $this->buildMember(Member::class, [
            'unitCount' => 3,
            'supplyCenterCount' => 3,
        ]);
        $this->assertFalse($memberWithEqualSCs->hasUnitDeficit());

        $memberWithMoreUnits = $this->buildMember(Member::class, [
            'unitCount' => 4,
            'supplyCenterCount' => 3,
        ]);
        $this->assertFalse($memberWithMoreUnits->hasUnitDeficit());

        $memberWithLessUnits = $this->buildMember(Member::class, [
            'unitCount' => 2,
            'supplyCenterCount' => 3,
        ]);
        $this->assertTrue($memberWithLessUnits->hasUnitDeficit());
    }

    public function testHasUnitSurplus()
    {
        $memberWithEqualSCs = $this->buildMember(Member::class, [
            'unitCount' => 3,
            'supplyCenterCount' => 3,
        ]);
        $this->assertFalse($memberWithEqualSCs->hasUnitSurplus());

        $memberWithMoreUnits = $this->buildMember(Member::class, [
            'unitCount' => 4,
            'supplyCenterCount' => 3,
        ]);
        $this->assertTrue($memberWithMoreUnits->hasUnitSurplus());

        $memberWithLessUnits = $this->buildMember(Member::class, [
            'unitCount' => 2,
            'supplyCenterCount' => 3,
        ]);
        $this->assertFalse($memberWithLessUnits->hasUnitSurplus());
    }

    public function testHasNoPieces()
    {
        $memberWithNoPieces = $this->buildMember(Member::class, [
            'unitCount' => 0,
            'supplyCenterCount' => 0,
        ]);
        $this->assertTrue($memberWithNoPieces->hasNoPieces());

        $memberWithSCs = $this->buildMember(Member::class, [
            'unitCount' => 0,
            'supplyCenterCount' => 1,
        ]);
        $this->assertFalse($memberWithSCs->hasNoPieces());

        $memberWithUnits = $this->buildMember(Member::class, [
            'unitCount' => 1,
            'supplyCenterCount' => 0,
        ]);
        $this->assertFalse($memberWithUnits->hasNoPieces());

        $memberWithBoth = $this->buildMember(Member::class, [
            'unitCount' => 1,
            'supplyCenterCount' => 1,
        ]);
        $this->assertFalse($memberWithBoth->hasNoPieces());
    }

    /**
     * @param int $units
     * @param int $supplyCenters
     * @param int $expectedTotal
     * @dataProvider providerPiecesCount
     */
    public function testPiecesCount(int $units, int $supplyCenters, int $expectedTotal)
    {
        $member = $this->buildMember(Member::class, [
            'unitCount' => $units,
            'supplyCenterCount' => $supplyCenters,
        ]);
        $this->assertEquals($expectedTotal, $member->piecesCount());
    }
    public function providerPiecesCount()
    {
        return [
            [0,3,3],
            [4,0,4],
            [2,5,7],
            [0,0,0],
        ];
    }

    public function testIsUser()
    {
        $member = $this->buildMember();
        // with entity
        $this->assertTrue($member->isUser($member->user));
        // with int
        $this->assertTrue($member->isUser($member->user->id));

        /** @var User $user */
        $user = $this->factories()->instance(User::class, ['id' => 9999]);
        $this->assertFalse($member->isUser($user));
    }
}
