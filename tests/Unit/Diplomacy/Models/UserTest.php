<?php

namespace Unit\Diplomacy\Models;

use Diplomacy\Models\User;
use Support\TestCase;

class UserTest extends TestCase
{
    public function testPasswordMatches()
    {
        /** @var User $user */
        $user = $this->factories()->instance(User::class);
        $user->setPassword('test1234');
        $this->assertTrue($user->passwordMatches('test1234'));
    }

    public function testSetPassword()
    {
        /** @var User $user */
        $user = $this->factories()->instance(User::class);
        $user->setPassword('test1111');
        $this->assertEquals(hex2bin(User::hashPassword('test1111')), $user->password);
    }

    /**
     * @param bool $expected
     * @param array $attributes
     * @dataProvider providerTestCanDoEmergencyPauses
     */
    public function testCanDoEmergencyPauses(bool $expected, array $attributes = [])
    {
        /** @var User $user */
        $user = $this->factories()->instance(User::class, $attributes);
        $this->assertEquals($expected, $user->canDoEmergencyPauses());
    }
    public function providerTestCanDoEmergencyPauses(): array
    {
        return [
            [false, ['emergencyPauseDate' => 1]],
            [true, ['emergencyPauseDate' => time() + 3600]],
        ];
    }
}