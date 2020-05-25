<?php

namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as Base;
use Diplomacy\Models\User;

abstract class BaseController extends Base
{
    /** @var string  */
    protected $template = 'pages/users/profile.twig';

    /** @var User */
    protected $user;

    public function beforeRender(): void
    {
        $this->loadUser();
    }

    public function call()
    {
        return [];
    }

    /**
     * @return User
     */
    protected function loadUser() : User
    {
        $userId = $this->request->get('id');
        $this->user = User::where('id', $userId)->first();
        $this->setPlaceholder('user', $this->user);
        return $this->user;
    }
}