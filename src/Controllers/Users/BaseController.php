<?php

namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as Base;
use Diplomacy\Models\User;
use Diplomacy\Models\Entities\User as UserEntity;

abstract class BaseController extends Base
{
    /** @var string  */
    protected $template = 'pages/users/profile.twig';

    /** @var UserEntity */
    protected $user;

    public function beforeRender(): void
    {
        $this->loadUser();
    }

    public function call()
    {
        return [];
    }

    protected function loadUser()
    {
        $userId = $this->request->get('id');
        $this->user = User::where('id', $userId)->first()->toEntity();
        $this->setPlaceholder('user', $this->user);
    }
}