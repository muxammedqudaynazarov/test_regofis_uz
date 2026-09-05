<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('users.view')) abort(404);
        $search = $request->input('search', '');
        $query = User::with(['roles', 'workplaces.department'])->where(function ($q) {
            $q->where('hemis_roles', '!=', '"[\"teacher\"]"');
        });
        if (!empty($search)) {
            $search = strtoupper($search);
            $query->where('hemis_id', 'LIKE', "%{$search}%")
                ->orWhere('id', $search)
                ->orWhere('name', 'LIKE', "%{$search}%");
        }
        $users = $query->orderBy('id', 'desc')->paginate(20)->appends($request->all());
        //dd($users, $search);
        return view('pages.web.users.index', compact(['users', 'search']));
    }

    public function show($id)
    {
        if (!auth()->user()->can('users.view')) abort(404);
        $user = User::with(['roles', 'permissions', 'workplaces.department'])->findOrFail($id);
        $allRoles = Role::orderBy('name')->get();
        $allPerms = Permission::orderBy('name')->get();
        $directPermNames = $user->getDirectPermissions()->pluck('name')->toArray();
        $rolePermNames = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('pages.web.users.show', compact(['user', 'allRoles', 'allPerms', 'directPermNames', 'rolePermNames', 'userRoles']));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('users.update')) abort(404);

        $request->validate([
            'roles.*' => 'exists:roles,name',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::findOrFail($id);
        $roles = $request->input('roles', []);
        $perms = $request->input('permissions', []);

        // Rollarni yangilash
        $user->syncRoles($roles);

        // To'g'ridan-to'g'ri permissionlarni yangilash (roldan kelganlar saqlanadi)
        $user->syncPermissions($perms);

        // Joriy rolni yangilash
        if (!empty($roles)) {
            $user->current_role = end($roles);
            $user->save();
        }

        $name = json_decode($user->name)?->short_name ?? "ID:{$id}";
        return redirect()->back()->with('success', "{$name} foydalanuvchisining huquqlari yangilandi.");
    }
}
