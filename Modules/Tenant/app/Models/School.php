<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Staff\Models\Staff;

class School extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schools';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'subdomain',
        'email',
        'phone',
        'address',
        'logo_url',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the settings associated with the school.
     */
    public function settings(): HasOne
    {
        return $this->hasOne(SchoolSetting::class, 'school_id', 'id');
    }

    /**
     * Staff members employed by this school.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class, 'school_id', 'id');
    }
}
