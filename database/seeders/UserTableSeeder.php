<?php

namespace Database\Seeders;

use App\User;
use App\Role;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = Role::where('name', 'admin')->first();
        $role_escolares = Role::where('name', 'escolares')->first();
        $role_division = Role::where('name', 'division')->first();
        $role_acad = Role::where('name', 'acad')->first();
        $role_docente = Role::where('name', 'docente')->first();
        $role_alumno = Role::where('name', 'alumno')->first();
        $role_verano = Role::where('name', 'verano')->first();
        $role_planeacion = Role::where('name', 'planeacion')->first();
        $role_direccion = Role::where('name', 'direccion')->first();

        $user = new User();
        $user->name = 'Cómputo Escolares';
        $user->email = 'computo_e@ite.edu.mx';
        $user->password =bcrypt('admin');
        $user->save();
        $user->roles()->attach($role_escolares);



    }
}
