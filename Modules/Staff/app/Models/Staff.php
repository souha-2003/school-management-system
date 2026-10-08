<?php

namespace Modules\Staff\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Tenant\Models\School;
use Modules\Tenant\Traits\BelongsToTenant;

class Staff extends Model
{
    use BelongsToTenant, HasFactory, HasUuids;

    protected $table = 'staff';

    protected $fillable = [
        'id',
        'school_id',
        'user_id',
        'employee_number',
        'full_name',
        'phone_number',
        'email',
        'national_id',
        'job_title',
        'specialization',
        'hire_date',
        'status',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'is_archived' => 'boolean',
            'hire_date' => 'date',
        ];
    }

    /**
     * School that the staff member belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    /**
     * Login account associated with this staff member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
