<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $default_user_value = [
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ];

            $employee = User::create(array_merge([
                'email' => 'employee1@gmail.com',
                'name' => 'Employee1',
            ],$default_user_value));

            $hr = User::create(array_merge([
                'email' => 'hr1@gmail.com',
                'name' => 'HR1',
            ],$default_user_value));

            $role_employee = Role::create(['name'=>'employee']);
            $role_hr = Role::create(['name'=>'hr']);

            $permission = Permission::create(['name' => 'read role']);
            $permission = Permission::create(['name' => 'create role']);
            $permission = Permission::create(['name' => 'update role']);
            $permission = Permission::create(['name' => 'delete role']);

            $employee->assignRole('employee');
            $hr->assignRole('hr');
            DB::commit();
        } catch (\Throwable $th) {
            echo $th;
            DB::rollback();
        }

    }
}
