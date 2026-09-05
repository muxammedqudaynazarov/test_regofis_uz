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
        if (!auth()->user()->can('system.view')) abort(404);

        $query = User::with(['roles', 'workplaces.department']);

        if ($search = $request->input('search')) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('hemis_id', 'LIKE', "%{$search}%");
        }

        $users = $query->orderBy('id', 'desc')->paginate(20)->appends($request->all());

        return view('pages.web.users.index', compact('users'));
    }

    public function show($id)
    {
        if (!auth()->user()->can('system.view')) abort(404);

        $user      = User::with(['roles', 'permissions', 'workplaces.department'])->findOrFail($id);
        $allRoles  = Role::orderBy('name')->get();
        $allPerms  = Permission::orderBy('name')->get();
        $userPerms = $user->getDirectPermissions()->pluck('name')->toArray();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('pages.web.users.show', compact('user', 'allRoles', 'allPerms', 'userPerms', 'userRoles'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('system.update')) abort(404);

        $request->validate([
            'roles.*'      => 'exists:roles,name',
            'permissions.*'=> 'exists:permissions,name',
        ]);

        $user  = User::findOrFail($id);
        $roles = $request->input('roles', []);
        $perms = $request->input('permissions', []);

        $user->syncRoles($roles);
        $user->syncPermissions($perms);

        if (!empty($roles)) {
            $user->current_role = end($roles);
            $user->save();
        }

        $name = json_decode($user->name)?->short_name ?? "ID:{$id}";
        return redirect()->back()->with('success', "{$name} foydalanuvchisining huquqlari yangilandi.");
    }
}
