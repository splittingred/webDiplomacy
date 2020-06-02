<?php

namespace Diplomacy\Services\Authorization;

use Diplomacy\Models\User;
use Diplomacy\Services\Monads\Failure;
use Diplomacy\Services\Monads\Result;
use Diplomacy\Services\Monads\Success;
use Diplomacy\Views\Renderer;

class Service
{
    /** @var SessionHandler */
    protected $sessionHandler;
    /** @var \Mailer $mailer */
    protected $mailer;
    /** @var Renderer */
    protected $renderer;

    public function __construct()
    {
        global $app;
        $this->sessionHandler = new SessionHandler();
        $this->mailer = $app->make('mailer');
        $this->renderer = $app->make('renderer');
    }

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

        if (!$this->sessionHandler->touch($user->id)) {
            return Failure::withError('internal', 'Failed to login user. Please try again.');
        }

        return new Success($user);
    }

    /**
     * @param string $username
     * @return Result
     */
    public function sendForgotPasswordConfirmation(string $username) : Result
    {
        /** @var User $user */
        $user = User::withUsername($username)->first();
        if (!$user) return Failure::withError('user_not_found', 'No such user found with that username.');

        $url = \Config::$url . '/users/forgot-confirmation?token=' . $user->forgotPasswordToken();
        $message = $this->renderer->render('emails/users/forgot-password-link.twig', [
            'moderator_email' => \Config::$modEMail,
            'url' => $url,
        ]);
        try {
            $this->mailer->Send([$user->email => $user->username], 'webDiplomacy forgotten password verification link', $message);
        } catch (\Exception $e) {
            return Failure::withError('internal', 'Failed to send forgot password email. Please try again.' . $e->getMessage());
        }

        return new Success();
    }

    /**
     * @param string $token
     * @return Result<User>
     */
    public function verifyForgotPasswordToken(string $token) : Result
    {
        $parts = explode('%7C', base64_decode($token));
        if (empty($parts) || count($parts) < 3) {
            return Failure::withError('invalid_code', 'Invalid code');
        }
        $email = urldecode($parts[2]);

        $user = User::withEmail($email)->first();
        if (!$user) {
            return Failure::withError('user_not_found', 'Invalid code');
        }
        $hash = substr(md5(\Config::$secret . $user->email), 0, 8);

        if ($hash != $parts[0]) {
            return Failure::withError('invalid_code', 'Invalid code');
        }
        $timestamp = intval($parts[1]);

        // tokens expire in 1h
        if (time() - $timestamp > 360) {
            return Failure::withError('expired_code', 'Code has expired');
        }

        return new Success($user);
    }

    /**
     * Change a user's password
     *
     * @param int $userId
     * @param string $oldPassword
     * @param string $newPassword
     * @param string $confirmPassword
     * @return Result
     */
    public function changeUserPassword(int $userId, string $oldPassword, string $newPassword, string $confirmPassword) : Result
    {
        /** @var User $user */
        $user = User::find($userId);
        if (empty($user)) {
            return Failure::withError('user_not_found', 'Invalid code. Please try again.');
        }

        if (!$user->passwordMatches($oldPassword)) {
            return Failure::withError('invalid_old_password', 'Invalid old password. Please check your spelling and try again.');
        }

        if (empty($newPassword) || strlen($newPassword) < 8) {
            return Failure::withError('invalid_new_password', 'Your new password must be at least 8 characters long.');
        } elseif (!preg_match("#[0-9]+#", $newPassword)) {
            return Failure::withError('invalid_new_password', 'Your new password must contain at least 1 number.');
        } elseif (!preg_match("#[A-Z]+#", $newPassword)) {
            return Failure::withError('invalid_new_password', 'Your new password must contain at least 1 capital letter.');
        } elseif (!preg_match("#[a-z]+#", $newPassword)) {
            return Failure::withError('invalid_new_password', 'Your new password must contain at least 1 lowercase letter.');
        }

        if ($newPassword != $confirmPassword) {
            return Failure::withError('invalid_new_password_confirm', 'Passwords do not match');
        }
        $user->setPassword($newPassword);
        $user->save();

        // wipe the current session on password regen
        $this->sessionHandler->destroy();

        return new Success($user);
    }

    /**
     * @return \User
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getCurrentLegacyUser() : \User
    {
        if ($this->sessionHandler->isActive()) {
            $userId = $this->sessionHandler->getUserId();
            $user = new \User($userId);
        } else {
            $user = new \User(GUESTID);
        }
        return $user;
    }
}