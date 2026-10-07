<?php

namespace Modules\Tenant\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Tenant\Http\Requests\UpdateSchoolSettingRequest;
use Modules\Tenant\Http\Resources\SchoolSettingResource;
use Modules\Tenant\Services\SchoolService;

class SchoolSettingController extends Controller
{
    public function __construct(
        protected SchoolService $schoolService
    ) {}

    /**
     * Display settings of the specified school.
     */
    public function show(string $schoolId)
    {
        $school = $this->schoolService->getSchoolById($schoolId);

        return new SchoolSettingResource($school->settings);
    }

    /**
     * Update settings of the specified school.
     */
    public function update(UpdateSchoolSettingRequest $request, string $schoolId)
    {
        $school = $this->schoolService->getSchoolById($schoolId);
        $settings = $this->schoolService->updateSchoolSettings($school, $request->validated());

        return new SchoolSettingResource($settings);
    }
}
