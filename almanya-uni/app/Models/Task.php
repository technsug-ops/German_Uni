<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Oyun kitabı görevi — panelden işaretlenen basit checklist kaydı.
 * İlk kaynak: doc/BACKLINK-PLAYBOOK.md (playbook = 'backlink').
 */
class Task extends Model
{
    protected $fillable = [
        'playbook',
        'group',
        'title',
        'details',
        'target_url',
        'status',
        'priority',
        'due_date',
        'sort_order',
        'notes',
        'completed_at',
        'is_done',      // sanal alan — tabloda checkbox'tan gelir
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
        'sort_order'   => 'integer',
    ];

    public const PLAYBOOKS = [
        'backlink' => 'Backlink & Otorite',
        'genel'    => 'Genel',
    ];

    public const STATUSES = [
        'todo'    => 'Yapılacak',
        'doing'   => 'Devam ediyor',
        'done'    => 'Tamam',
        'skipped' => 'Atlandı',
    ];

    public const PRIORITIES = [
        'high'   => 'Yüksek',
        'normal' => 'Normal',
        'low'    => 'Düşük',
    ];

    /**
     * Checkbox sütunu bu iki erişimciyle çalışır: işaretlenince 'done' +
     * completed_at yazılır, kaldırılınca 'todo'ya döner ve tarih temizlenir.
     */
    public function getIsDoneAttribute(): bool
    {
        return $this->status === 'done';
    }

    public function setIsDoneAttribute($value): void
    {
        $done = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $this->attributes['status']       = $done ? 'done' : 'todo';
        $this->attributes['completed_at'] = $done ? now() : null;
    }

    protected static function booted(): void
    {
        // Durum elle 'done' yapıldığında da tarih tutulsun (form üzerinden düzenleme).
        static::saving(function (self $task) {
            if ($task->status === 'done' && ! $task->completed_at) {
                $task->completed_at = now();
            }
            if ($task->status !== 'done' && $task->completed_at) {
                $task->completed_at = null;
            }
        });
    }

    /** Açık iş: tamamlanmamış ve atlanmamış. */
    public function scopeOpen(Builder $q): Builder
    {
        return $q->whereNotIn('status', ['done', 'skipped']);
    }

    /** Tarihi geçmiş açık iş. */
    public function scopeOverdue(Builder $q): Builder
    {
        return $q->open()->whereNotNull('due_date')->whereDate('due_date', '<', now());
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && ! in_array($this->status, ['done', 'skipped'], true)
            && $this->due_date->isPast();
    }
}
