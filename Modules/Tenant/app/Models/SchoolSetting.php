<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolSetting extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'school_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'school_id',
        'subscription_plan',
        'subscription_start_date',
        'subscription_end_date',
        'primary_color',
        'secondary_color',
        'theme_mode',
        'favicon_url',
        'timezone',
        'school_start_time',
        'school_end_time',
        'weekend_days',
        'extra_config',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subscription_start_date' => 'date',
            'subscription_end_date' => 'date',
            'extra_config' => 'array',
        ];
    }

    /**
     * Get the school that owns the settings.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
}
