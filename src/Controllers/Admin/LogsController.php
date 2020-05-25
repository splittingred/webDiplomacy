<?php

namespace Diplomacy\Controllers\Admin;

use Diplomacy\Models\AdminLog;
use Diplomacy\Models\User;
use Diplomacy\Services\Request;

class LogsController extends BaseController
{
    public $template = 'admin/logs/index.twig';
    public $perPage = 200;

    public function call()
    {
        $query = AdminLog::with('user');
        $total = $query->count();
        $query->paginate($this->perPage);
        return [
            'logs' => $query->get(),
            'pagination' => $this->getPagination($total),
        ];
    }
}