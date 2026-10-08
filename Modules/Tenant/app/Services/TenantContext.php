<?php

namespace Modules\Tenant\Services;

use Modules\Tenant\Models\School;

class TenantContext
{
    protected ?School $school = null;

    /**
     * Set the current active school tenant.
     */
    public function setSchool(?School $school): void
    {
        $this->school = $school;
    }

    /**
     * Get the current active school tenant model.
     */
    public function getSchool(): ?School
    {
        return $this->school;
    }

    /**
     * Get the current active school tenant UUID.
     */
    public function getSchoolId(): ?string
    {
        return $this->school?->id;
    }

    /**
     * Check if an active tenant is registered in the context.
     */
    public function hasSchool(): bool
    {
        return !is_null($this->school);
    }
}
