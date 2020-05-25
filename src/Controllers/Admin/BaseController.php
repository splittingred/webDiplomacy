<?php

namespace Diplomacy\Controllers\Admin;

use Diplomacy\Controllers\BaseController as Base;

abstract class BaseController extends Base {
    /**
     * Handle auth check
     */
    public function setDefaultPlaceholders(): void
    {
        if (!$this->currentUser->isModerator()) {
            $this->redirectRelative('/');
        }
        parent::setDefaultPlaceholders();
    }

}