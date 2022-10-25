<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Model\User;


class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //creamos roles
        $admin = Role::create(['name' => 'admin']);
        $driver = Role::create(['name' => 'driver']);
        $storer = Role::create(['name' => 'storer']);
        $null = Role::create(['name' => 'null']);

        $admin = User::find(1); 
        $admin->assignRole('admin');

        $storer = User::find(2); 
        $storer->assignRole('storer');

        $driver = User::find(3); 
        $driver->assignRole('driver');
    
    }
}