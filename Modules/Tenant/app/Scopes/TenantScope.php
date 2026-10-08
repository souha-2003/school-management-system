<?php

namespace Modules\Tenant\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\Tenant\Services\TenantContext;

class TenantScope implements Scope
{
    /**
     * Apply the tenant scope to automatically filter by current school.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $tenantContext = app(TenantContext::class);

        if ($tenantContext->hasSchool()) {
            $builder->where($model->getTable() . '.school_id', $tenantContext->getSchoolId());
        }
    }
}
