<?php

namespace Diplomacy\Controllers\Admin;

use Diplomacy\Models\AdminLog;
use Diplomacy\Models\User;
use Diplomacy\Services\Admin\LogsService;
use Diplomacy\Services\Request;

class LogsController extends BaseController
{
    public string $template = 'admin/logs/index.twig';
    public int $perPage = 200;
    protected LogsService $logsService;

    public function setUp(): void
    {
        $this->logsService = new LogsService();
    }

    public function call(): array
    {
        $logs = $this->logsService->search($this->perPage);
        return [
            'logs' => $logs,
            'pagination' => $this->getPagination($logs->getTotal()),
        ];
    }
}