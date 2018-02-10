<?php

use App\Role;
use App\Permission;
use Illuminate\Database\Seeder;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminPermissions = [
            [
                'name' => 'configure-system',
                'display_name' => 'Configure System',
                'description' => 'Allow user to configure the system settings'
            ],
            [
                'name' => 'manage-user',
                'display_name' => 'Manage User',
                'description' => 'Allow user to manager user'
            ],
            [
                'name' => 'manage-order',
                'display_name' => 'Manager Order',
                'description' => 'Allow user to manager order'
            ]
        ];

        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Role has access to all system functionality'
        ]);

        foreach ($adminPermissions as $key => $value) {
            $adminRole->attachPermission(Permission::create($value));
        }

        $admin = \App\User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('P@ssw0rd')
        ]);

        $admin->attachRole($adminRole);

        $userPermissions = Permission::create([
            'name' => 'purchase-order',
            'display_name' => 'Purchase Order',
            'description' => 'Allow user to purchase order'
        ]);

        $userRole = Role::create([
            'name' => 'user',
            'display_name' => 'User',
            'description' => 'Role has access to non-critical system functionality'
        ]);

        $userRole->attachPermission($userPermissions);
    }
}
