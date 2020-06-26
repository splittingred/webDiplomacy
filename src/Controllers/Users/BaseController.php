<?php

namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController as Base;
use Diplomacy\Models\User;
use Diplomacy\Models\Entities\User as UserEntity;

abstract class BaseController extends Base
{
    protected string $template = 'pages/users/profile.twig';
    protected UserEntity $user;

    public function beforeRender(): void
    {
        $this->loadUser();
    }

    public function call(): array
    {
        return [];
    }

    protected function loadUser(): BaseController
    {
        $userId = $this->request->get('id');
        $this->user = User::where('id', $userId)->first()->toEntity();
        $this->setPlaceholder('user', $this->user);
        return $this;
    }
}