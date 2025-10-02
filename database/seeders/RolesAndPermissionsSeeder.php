<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // auth
        Permission::create(['name' => 'login']);
        Permission::create(['name' => 'logout']);
        Permission::create(['name' => 'update own profile']);
        Permission::create(['name' => 'delete own account']);

        // events
        Permission::create(['name' => 'create events']);
        Permission::create(['name' => 'update events']);
        Permission::create(['name' => 'delete events']);
        Permission::create(['name' => 'toggle event status']);
        Permission::create(['name' => 'view all events']);
        Permission::create(['name' => 'view live events']);

        // users
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'manage roles']);

        // event registrations participation
        Permission::create(['name' => 'join event']);
        Permission::create(['name' => 'view my registrations']);
        Permission::create(['name' => 'cancel event registration']);

        // event registrations management
        Permission::create(['name' => 'create event registrations']);
        Permission::create(['name' => 'view event registrations']);
        Permission::create(['name' => 'update event registrations']);
        Permission::create(['name' => 'delete event registrations']);
        

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'login',
            'logout', 
            'update own profile',
            'delete own account',
            'create events',
            'update events',
            'delete events',
            'toggle event status',
            'view all events',
            'view live events',
            'create users',
            'view users',
            'update users',
            'delete users',
            'manage roles',
            'join event',
            'view my registrations',
            'cancel event registration',
            'create event registrations',
            'view event registrations',
            'update event registrations',
            'delete event registrations'
        ]);

        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'login',
            'logout',
            'update own profile',
            'delete own account',
            'view live events',
            'join event',
            'view my registrations',
            'cancel event registration'
        ]);
    }
}
