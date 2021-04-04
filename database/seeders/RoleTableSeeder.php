<?php

namespace Database\Seeders;

use App\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       /* $role=new Role();
        $role->name='admin';
        $role->description='Administrador';
        $role->save();

        $role=new Role();
        $role->name='escolares';
        $role->description='Escolares';
        $role->save();

        $role=new Role();
        $role->name='division';
        $role->description='Division de Estudios';
        $role->save();

        $role=new Role();
        $role->name='acad';
        $role->description='Jefatura Académica';
        $role->save();

        $role=new Role();
        $role->name='docente';
        $role->description='Docente';
        $role->save();

        $role=new Role();
        $role->name='alumno';
        $role->description='Estudiante';
        $role->save();

        $role=new Role();
        $role->name='verano';
        $role->description='Coord Verano';
        $role->save();*/

        $role=new Role();
        $role->name='planeacion';
        $role->description='Planeacion';
        $role->save();

        $role=new Role();
        $role->name='direccion';
        $role->description='Direccion';
        $role->save();

    }
}
