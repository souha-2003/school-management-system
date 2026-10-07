<?php

namespace Modules\Tenant\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tenant\Http\Requests\StoreSchoolRequest;
use Modules\Tenant\Http\Requests\UpdateSchoolRequest;
use Modules\Tenant\Http\Resources\SchoolResource;
use Modules\Tenant\Services\SchoolService;

class SchoolController extends Controller
{
    public function __construct(
        protected SchoolService $schoolService
    ) {}

    /**
     * Display a listing of schools.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $schools = $this->schoolService->listSchools($request->all(), $perPage);

        return SchoolResource::collection($schools);
    }

    /**
     * Store a newly created school.
     */
    public function store(StoreSchoolRequest $request)
    {
        $school = $this->schoolService->createSchool($request->validated());

        return new SchoolResource($school);
    }

    /**
     * Display the specified school.
     */
    public function show(string $id)
    {
        $school = $this->schoolService->getSchoolById($id);

        return new SchoolResource($school);
    }

    /**
     * Update the specified school.
     */
    public function update(UpdateSchoolRequest $request, string $id)
    {
        $school = $this->schoolService->getSchoolById($id);
        $updatedSchool = $this->schoolService->updateSchool($school, $request->validated());

        return new SchoolResource($updatedSchool);
    }

    /**
     * Change operational status of the school.
     */
    public function changeStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,suspended,pending_setup'],
        ]);

        $school = $this->schoolService->getSchoolById($id);
        $updatedSchool = $this->schoolService->changeStatus($school, $validated['status']);

        return new SchoolResource($updatedSchool);
    }

    /**
     * Remove the specified school (Soft Delete).
     */
    public function destroy(string $id)
    {
        $school = $this->schoolService->getSchoolById($id);
        $this->schoolService->deleteSchool($school);

        return response()->json([
            'message' => 'School deleted successfully.',
        ]);
    }
}
