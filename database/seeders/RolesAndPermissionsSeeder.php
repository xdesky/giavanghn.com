<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            ['name' => 'users.view', 'display_name' => 'Xem danh sách người dùng', 'group' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Tạo người dùng mới', 'group' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Chỉnh sửa người dùng', 'group' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Xóa người dùng', 'group' => 'users'],
            
            // Article management
            ['name' => 'articles.view', 'display_name' => 'Xem bài viết', 'group' => 'articles'],
            ['name' => 'articles.create', 'display_name' => 'Tạo bài viết', 'group' => 'articles'],
            ['name' => 'articles.edit', 'display_name' => 'Chỉnh sửa bài viết', 'group' => 'articles'],
            ['name' => 'articles.delete', 'display_name' => 'Xóa bài viết', 'group' => 'articles'],
            ['name' => 'articles.publish', 'display_name' => 'Xuất bản bài viết', 'group' => 'articles'],
            
            // Role management
            ['name' => 'roles.view', 'display_name' => 'Xem vai trò', 'group' => 'roles'],
            ['name' => 'roles.manage', 'display_name' => 'Quản lý vai trò', 'group' => 'roles'],
            
            // Settings
            ['name' => 'settings.view', 'display_name' => 'Xem cài đặt', 'group' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'Chỉnh sửa cài đặt', 'group' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Quản trị viên',
                'description' => 'Có toàn quyền truy cập hệ thống'
            ]
        );

        $editorRole = Role::firstOrCreate(
            ['name' => 'editor'],
            [
                'display_name' => 'Biên tập viên',
                'description' => 'Có quyền quản lý bài viết'
            ]
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'Người dùng',
                'description' => 'Người dùng thông thường'
            ]
        );

        // Assign all permissions to admin
        $adminRole->permissions()->sync(Permission::all());

        // Assign article permissions to editor
        $editorRole->permissions()->sync(
            Permission::whereIn('name', [
                'articles.view',
                'articles.create',
                'articles.edit',
                'articles.publish'
            ])->pluck('id')
        );

        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@giavang.vn'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin@123456'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $this->command->info('✓ Roles and permissions created successfully!');
        $this->command->info('✓ Admin user: admin@giavang.vn / Admin@123456');
    }
}
