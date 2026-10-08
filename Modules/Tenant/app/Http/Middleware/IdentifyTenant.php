<?php

namespace Modules\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Tenant\Models\School;
use Modules\Tenant\Services\TenantContext;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IdentifyTenant
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    /**
     * Handle an incoming request and identify the school tenant.
     */
    public function handle(Request $request, Closure $next)
    {
        $school = null;
        $user = $request->user();

        // 1. Identify from Authenticated User (if tenant user)
        if ($user && $user->school_id) {
            $school = $user->school ?? School::find($user->school_id);
        }

        // 2. Identify from Request Header (X-School-ID or X-Tenant-Subdomain)
        if (!$school) {
            if ($schoolIdHeader = $request->header('X-School-ID')) {
                $school = School::find($schoolIdHeader);
            } elseif ($subdomainHeader = $request->header('X-Tenant-Subdomain')) {
                $school = School::where('subdomain', $subdomainHeader)->first();
            }
        }

        // 3. Identify from Subdomain in Host URL (e.g. alrowad.school.com)
        if (!$school) {
            $host = $request->getHost();
            $parts = explode('.', $host);
            if (count($parts) > 2) {
                $subdomain = $parts[0];
                if (!in_array($subdomain, ['www', 'admin', 'api', 'app', 'localhost', '127'])) {
                    $school = School::where('subdomain', $subdomain)->first();
                }
            }
        }

        // 4. Verify School Status & Subscription Expiry if a school is identified
        if ($school) {
            if ($school->status === 'suspended') {
                throw new AccessDeniedHttpException('تم تعليق خدمات هذه المدرسة مؤقتاً، يرجى مراجعة إدارة المنصة.');
            }

            // Block access if school subscription has expired (except for platform super admin)
            if (app(\Modules\Tenant\Services\SubscriptionService::class)->isExpired($school) && !$user?->hasRole('super_admin')) {
                throw new AccessDeniedHttpException('انتهت فترة اشتراك هذه المدرسة، يرجى مراجعة إدارة المنصة لتجديد الاشتراك.');
            }

            // Register school in current tenant context
            $this->tenantContext->setSchool($school);
        } else {
            // If the route strictly requires a tenant and none was found
            if (!$user?->hasRole('super_admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على المنشأة التعليمية المستهدفة أو لم يتم تحديدها.',
                    'errors' => null,
                ], 400);
            }
        }

        return $next($request);
    }
}
