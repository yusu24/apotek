<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get multiple settings as array
     */
    public static function getMultiple(array $keys)
    {
        $settings = self::whereIn('key', $keys)->get();
        $result = [];
        
        foreach ($keys as $key) {
            $setting = $settings->firstWhere('key', $key);
            $result[$key] = $setting ? $setting->value : null;
        }
        
        return $result;
    }
}
