<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Model\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name'     => 'Administrador',            
            'lastname'     => 'Usuario',            
            'dni'     => '0412151T',            
            'email'     => 'Admin@gmail.com',
            'password'     => bcrypt('1234567'),
        ]);

        $admin = User::create([
            'name'     => 'Storer',  
            'lastname'     => 'Usuario',            
            'dni'     => '9855744f',            
            'email'     => 'Storer@gmail.com',
            'password'     => bcrypt('1234567'),
        ]);

        $admin = User::create([
            'name'     => 'Chofer',   
            'lastname'     => 'Usuario',            
            'dni'     => '63547871A',            
            'email'     => 'Driver@gmail.com',
            'password'     => bcrypt('1234567'),
        ]);

        $admin = User::create([
            'name'     => 'Sin Permiso',     
            'lastname'     => 'Usuario',            
            'dni'     => '16657845A',            
            'email'     => 'null@gmail.com',
            'password'     => bcrypt('1234567'),
        ]);

    }
}
