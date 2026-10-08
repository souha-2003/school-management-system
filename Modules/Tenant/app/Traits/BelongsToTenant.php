<?php

namespace Modules\Tenant\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Tenant\Models\School;
use Modules\Tenant\Scopes\TenantScope;
use Modules\Tenant\Services\TenantContext;

trait BelongsToTenant
{
    /**
     * Boot the BelongsToTenant trait for a model.
     */
    protected static function bootBelongsToTenant(): void
    {
        // 1. Automatically apply global tenant query filter
        static::addGlobalScope(new TenantScope());

        // 2. Automatically assign current school_id upon creation if not provided
        static::creating(function ($model) {
            $tenantContext = app(TenantContext::class);

            if (!$model->school_id && $tenantContext->hasSchool()) {
                $model->school_id = $tenantContext->getSchoolId();
            }
        });
    }

    /**
     * School that this tenant record belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
}
