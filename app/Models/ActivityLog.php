<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity
     */
    public static function log(array $data): void
    {
        $request = request();
        
        static::create([
            'user_id' => auth()->id(),
            'action' => $data['action'],
            'module' => $data['module'],
            'description' => $data['description'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'url' => $request->fullUrl(),
        ]);
    }

    /**
     * Get action badge color
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created', 'login' => 'green',
            'updated' => 'yellow',
            'deleted', 'logout' => 'red',
            'viewed', 'accessed' => 'blue',
            'exported', 'printed' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get action icon
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'created' => '➕',
            'updated' => '✏️',
            'deleted' => '🗑️',
            'viewed', 'accessed' => '👁️',
            'exported' => '📤',
            'printed' => '🖨️',
            'login' => '🔓',
            'logout' => '🔒',
            default => '📝',
        };
    }
}
