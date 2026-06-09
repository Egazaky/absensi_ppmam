<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RbacPermission;
use Illuminate\Http\Request;

class RbacController extends Controller
{
    public function index()
    {
        RbacPermission::syncDefaults();

        $roles = RbacPermission::roles();
        $permissions = RbacPermission::definitions();
        $matrix = RbacPermission::all()
            ->groupBy('permission')
            ->map(function ($items) {
                return $items->keyBy('role');
            });

        return view('rbac.index', compact('roles', 'permissions', 'matrix'));
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'permission' => 'required|string',
            'role' => 'required|string',
        ]);

        $roles = RbacPermission::roles();
        $permissions = RbacPermission::definitions();
        $permission = $validated['permission'];
        $role = $validated['role'];

        if (! array_key_exists($role, $roles) || ! array_key_exists($permission, $permissions)) {
            abort(404);
        }

        if (RbacPermission::locked($permission, $role)) {
            return response()->json([
                'message' => 'Izin ini dikunci agar SuperAdmin tetap bisa mengelola RBAC.',
            ], 422);
        }

        $rbacPermission = RbacPermission::firstOrCreate(
            ['permission' => $permission, 'role' => $role],
            ['allowed' => in_array($role, $permissions[$permission]['roles'], true)]
        );
        $rbacPermission->update(['allowed' => ! $rbacPermission->allowed]);

        return response()->json([
            'allowed' => $rbacPermission->allowed,
            'message' => 'Hak akses berhasil diperbarui.',
        ]);
    }
}
