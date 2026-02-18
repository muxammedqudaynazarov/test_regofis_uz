<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        Permission::truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $permissions = [
            'enter.home',
            'enter.student',
            'department.faculties.view',
            'department.faculties.access',
            'department.faculties.export',
            'department.view',
            'department.export',
            'curriculum.view',
            'curriculum.delete',
            'languages.view',
            'languages.status',
            'applications.view',
            'applications.reload',
            'lessons.view',
            'lessons.teachers',
            'lessons.delete',
            'lessons.request.show',
            'subjects.view',
            'subjects.resource.view',
            'subjects.resource.create',
            'subjects.resource.delete',
            'exam.view',
            'exam.upload',
            'exam.export',
            'statistics.view',
            'statistics.export',
            'system.view',
            'system.update',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        $pos = [
            'super_admin' => 'Super admin',
            'registrator_office' => 'Registrator ofisi',
            'department' => 'Kafedra mudiri',
            'teacher' => 'O‘qituvchi',
        ];
        $roles = [
            'super_admin' => [
                'enter.home',
                'department.faculties.view',
                'department.faculties.access',
                'department.faculties.export',
                'department.view',
                'department.export',
                'curriculum.view',
                'curriculum.delete',
                'languages.view',
                'languages.status',
                'applications.view',
                'applications.reload',
                'lessons.view',
                'lessons.teachers',
                'lessons.delete',
                'lessons.request.show',
                'subjects.view',
                'subjects.resource.view',
                'subjects.resource.create',
                'subjects.resource.delete',
                'exam.view',
                'exam.upload',
                'exam.export',
                'statistics.view',
                'statistics.export',
                'system.view',
                'system.update',
            ],
            'registrator_office' => [
                'enter.home',
                'department.faculties.view',
                'department.faculties.access',
                'department.faculties.export',
                'department.view',
                'department.export',
                'curriculum.view',
                'curriculum.delete',
                'languages.view',
                'languages.status',
                'applications.view',
                'applications.reload',
                'lessons.view',
                'lessons.teachers',
                'lessons.delete',
                'lessons.request.show',
                'subjects.view',
                'subjects.resource.view',
                'subjects.resource.create',
                'subjects.resource.delete',
                'exam.view',
                'exam.upload',
                'exam.export',
                'statistics.view',
                'statistics.export',
                'system.view',
                'system.update',
            ],
            'department' => [
                'enter.home',
                'lessons.view',
                'lessons.teachers',
                'subjects.view',
                'subjects.resource.view',
                'subjects.resource.create',
                'subjects.resource.delete',
                'statistics.view',
                'statistics.export',
            ],
            'teacher' => [
                'enter.home',
                'subjects.view',
                'subjects.resource.view',
                'subjects.resource.create',
                'subjects.resource.delete',
            ],
        ];
        foreach ($roles as $name => $value) {
            $role = Role::create([
                'name' => $name,
                'desc' => $pos[$name],
            ]);
            $role->syncPermissions($value);
        }
        $users = User::all();
        foreach ($users as $user) {
            $user->assignRole($user->current_role);
        }
    }
}
