<?php

namespace Diplomacy\Services\Admin;

use Diplomacy\Models\AdminLog;
use Diplomacy\Models\Collection;

/**
 * For interacting with admin logs on the platform
 *
 * @package Diplomacy\Services\Admin
 */
class LogsService
{
    /**
     * @param int $perPage
     * @return Collection
     */
    public function search(int $perPage = 20): Collection
    {
        $query = AdminLog::with('user')->orderBy('time', 'desc');
        $total = $query->count();
        $query->paginate($perPage);
        $logs = [];
        /** @var AdminLog $log */
        foreach ($query->get() as $log) {
            $logs[] = $log->toEntity();
        }
        return new Collection($logs, $total);
    }
}