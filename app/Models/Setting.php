<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Get a setting value with caching
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->getCastedValue() : $default;
        });
    }

    /**
     * Set a setting value and clear cache
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @return Setting
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general')
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );
        
        Cache::forget("setting_{$key}");
        
        return $setting;
    }

    /**
     * Get value casted to appropriate type
     *
     * @return mixed
     */
    public function getCastedValue()
    {
        return match($this->type) {
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            'float' => (float) $this->value,
            default => $this->value
        };
    }

    /**
     * Get all settings grouped by category
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllGrouped()
    {
        return Cache::remember('all_settings_grouped', 3600, function () {
            return static::all()->groupBy('group');
        });
    }

    /**
     * Clear all settings cache
     *
     * @return void
     */
    public static function clearCache()
    {
        Cache::flush();
    }
}
