<?php

namespace Diplomacy\Models;

use Diplomacy\Models\Entities\AdminLog as AdminLogEntity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property int $userId
 * @property int $time
 * @property string $details
 * @property string $params
 * @property User $user
 * @package Diplomacy\Models
 */
class AdminLog extends EloquentBase
{
    protected $table = 'wD_AdminLog';
    protected $hidden = ['params'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userID', null, 'user');
    }

    /**
     * @return AdminLogEntity
     */
    public function toEntity(): AdminLogEntity
    {
        $adminLog = new AdminLogEntity();
        $adminLog->id = $this->id;
        $adminLog->name = $this->name;
        $adminLog->user = $this->user->toEntity();
        $adminLog->time = $this->time;
        $adminLog->details = $this->details;
        $adminLog->params = isset($this->params) && !empty($this->params) ? unserialize($this->params) : [];
        return $adminLog;
    }
}