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
        'subject_type', // added
        'subject_id', // added
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
     * Get the subject of the activity
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Log an activity
     */
    public static function log(array $data): void
    {
        $request = request();
        
        static::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'action' => $data['action'],
            'module' => $data['module'],
            'description' => $data['description'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'url' => $request->fullUrl(),
            'subject_id' => $data['subject_id'] ?? null,
            'subject_type' => $data['subject_type'] ?? null,
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
        };
    }

    /**
     * Get meaningful changes (diff) between old and new values
     */
    public function getChangesAttribute(): array
    {
        $old = $this->old_values ?: [];
        $new = $this->new_values ?: [];
        
        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
        $changes = [];
        
        $ignore = [
            'id', 'created_at', 'updated_at', 'user_id', 'deleted_at', 
            'email_verified_at', 'remember_token', 'slug', 'password'
        ];
        
        foreach ($allKeys as $key) {
            if (in_array($key, $ignore)) continue;
            
            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;
            
            // Only add if value actually changed
            if ($oldVal != $newVal) {
                $changes[$key] = [
                    'from' => $oldVal,
                    'to' => $newVal
                ];
            }
        }
        
        return $changes;
    }
}
