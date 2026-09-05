<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    // ─── Barcha permission va rol ta'riflari ─────────────────────────────
    private static array $permissions = [
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
        'applications.show',
        'applications.reload',
        'lessons.view',
        'lessons.request.view',
        'lessons.create.teachers',
        'lessons.status',
        'retrains.view',
        'retrains.create',
        'retrains.update',
        'subjects.view',
        'subjects.resource.view',
        'subjects.resource.create',
        'subjects.resource.delete',
        'results.delete',
        'exam.view',
        'exam.upload',
        'exam.archive',
        'exam.upload.all',
        'exam.download',
        'statistics.view',
        'statistics.view.sv',
        'statistics.export',
        'log.view',
        'log.clean',
        'system.view',
        'system.update',
        'users.view',      // Foydalanuvchilar ro'yxatini ko'rish
        'users.update',    // Foydalanuvchi rol/permission tahrirlash
    ];

    private static array $roleDescriptions = [
        'super_admin'       => 'Super admin',
        'registrator_office'=> 'Registrator ofisi',
        'department'        => 'Kafedra mudiri',
        'teacher'           => "O'qituvchi",
    ];

    private static array $rolePermissions = [
        'super_admin' => [
            'enter.home',
            'department.faculties.view', 'department.faculties.access',
            'department.faculties.export',
            'department.view', 'department.export',
            'curriculum.view', 'curriculum.delete',
            'languages.view', 'languages.status',
            'applications.view', 'applications.show', 'applications.reload',
            'lessons.view', 'lessons.request.view', 'lessons.create.teachers', 'lessons.status',
            'retrains.view', 'retrains.create', 'retrains.update',
            'results.delete',
            'exam.view', 'exam.upload', 'exam.archive', 'exam.upload.all', 'exam.download',
            'statistics.view', 'statistics.view.sv', 'statistics.export',
            'log.view', 'log.clean',
            'system.view', 'system.update',
            'users.view', 'users.update',
        ],
        'registrator_office' => [
            'enter.home',
            'department.faculties.view', 'department.faculties.access',
            'department.faculties.export',
            'department.view', 'department.export',
            'curriculum.view', 'curriculum.delete',
            'languages.view', 'languages.status',
            'applications.view', 'applications.show', 'applications.reload',
            'lessons.view', 'lessons.request.view', 'lessons.create.teachers', 'lessons.status',
            'retrains.view', 'retrains.create', 'retrains.update',
            'exam.view', 'exam.upload', 'exam.archive', 'exam.upload.all', 'exam.download',
            'statistics.view', 'statistics.view.sv', 'statistics.export',
            'log.view', 'log.clean',
            'system.view', 'system.update',
            'users.view', 'users.update',
        ],
        'department' => [
            'enter.home',
            'lessons.view', 'lessons.create.teachers',
            'subjects.view', 'subjects.resource.view',
            'subjects.resource.create', 'subjects.resource.delete',
            'statistics.view', 'statistics.export',
        ],
        'teacher' => [
            'enter.home',
            'subjects.view', 'subjects.resource.view',
            'subjects.resource.create', 'subjects.resource.delete',
        ],
    ];

    // ─── To'liq qayta yuklash (birinchi o'rnatish uchun) ─────────────────
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

        foreach (self::$permissions as $perm) {
            Permission::create(['name' => $perm]);
        }

        foreach (self::$rolePermissions as $roleName => $perms) {
            $role = Role::create([
                'name' => $roleName,
                'desc' => self::$roleDescriptions[$roleName] ?? $roleName,
            ]);
            $role->syncPermissions($perms);
        }

        // Mavjud foydalanuvchilarga joriy rolini biriktirish
        User::all()->each(function ($user) {
            if ($user->current_role) {
                $user->assignRole($user->current_role);
            }
        });

        $this->command?->info('RoleSeeder: to\'liq qayta yuklandi.');
    }

    // ─── Xavfsiz yangilash (production uchun) ────────────────────────────
    // Mavjud user role/permission biriktirishlarini O'CHIRMAYDI.
    // Faqat yangi permission va rollarni QO'SHADI,
    // rol permissionlarini YANGILAYDI (syncPermissions).
    //
    // Ishlatish: php artisan db:seed --class=RoleSeeder --method=safeRun
    // Yoki: php artisan tinker --execute="(new \Database\Seeders\RoleSeeder)->safeRun()"
    public function safeRun(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Yangi permissionlarni qo'shish (mavjudlarini qoldirish)
        $added = 0;
        foreach (self::$permissions as $perm) {
            if (!Permission::where('name', $perm)->exists()) {
                Permission::create(['name' => $perm]);
                $added++;
                $this->command?->info("  + Permission qo'shildi: {$perm}");
            }
        }
        $this->command?->info("Jami {$added} ta yangi permission qo'shildi.");

        // 2. Rollarni qo'shish/yangilash (user biriktirishlarini saqlab)
        foreach (self::$rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['desc' => self::$roleDescriptions[$roleName] ?? $roleName]
            );
            // desc ni yangilash
            if (isset(self::$roleDescriptions[$roleName])) {
                $role->desc = self::$roleDescriptions[$roleName];
                $role->save();
            }
            // Rol permissionlarini yangilash
            $role->syncPermissions($perms);
            $this->command?->info("  ~ Rol yangilandi: {$roleName} (" . count($perms) . " ta permission)");
        }

        // 3. Spatie cache tozalash
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command?->info('RoleSeeder::safeRun() muvaffaqiyatli yakunlandi.');
    }
}
