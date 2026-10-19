<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Permisiunile sunt numite "verb resursa" - cu spatiu, asa cum
     * recomanda Spatie. Pe FE le poti afisa direct sau le poti mapa.
     */
    public const PERMISSIONS = [
        // listings
        'view listings',
        'create listings',
        'edit own listings',
        'edit any listing',
        'delete own listings',
        'delete any listing',
        'feature listings',

        // users
        'view users',
        'edit users',
        'delete users',

        // roles
        'view roles',
        'manage roles',

        // settings
        'manage settings',
    ];

    public const ROLE_SUPER_ADMIN = 'super admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_AGENT = 'agent';

    public const ROLE_USER = 'user';

    public function run(): void
    {
        App::make(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::firstOrCreate(['name' => self::ROLE_SUPER_ADMIN, 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => self::ROLE_ADMIN, 'guard_name' => 'web']);
        $admin->syncPermissions([
            'view listings',
            'create listings',
            'edit any listing',
            'delete any listing',
            'feature listings',
            'view users',
            'edit users',
            'view roles',
        ]);

        $agent = Role::firstOrCreate(['name' => self::ROLE_AGENT, 'guard_name' => 'web']);
        $agent->syncPermissions([
            'view listings',
            'create listings',
            'edit own listings',
            'delete own listings',
        ]);

        $user = Role::firstOrCreate(['name' => self::ROLE_USER, 'guard_name' => 'web']);
        $user->syncPermissions([
            'view listings',
        ]);

        App::make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
