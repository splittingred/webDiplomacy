<?php

namespace Diplomacy\Services\Authorization;

use Diplomacy\Models\Session;
use Diplomacy\Models\User;
use Diplomacy\Services\Monads\Failure;
use Diplomacy\Services\Monads\Result;
use Diplomacy\Services\Monads\Success;

class LoginService
{
    /**
     * @param string $username
     * @param string $password
     * @return Result
     */
    public function login(string $username, string $password) : Result
    {
        $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $password = filter_var($password, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        /** @var User $user */
        $user = User::withUsername($username)->first();
        if (!$user) {
            return Failure::withError('user_not_found', 'Invalid login credentials. Please try again.');
        }

        if (!$user->passwordMatches($password)) {
            return Failure::withError('invalid_password', 'Invalid login credentials. Please try again.');
        }

        if (!Session::startForUser($user)) {
            return Failure::withError('internal', 'Failed to login user. Please try again.');
        }

        return new Success($user);
    }
}