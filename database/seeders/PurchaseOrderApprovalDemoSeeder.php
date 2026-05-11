<?php

namespace Database\Seeders;

use App\Models\Sppg;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PurchaseOrderApprovalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['employee', 'akuntan', 'verval', 'head'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $defaultPassword = Hash::make('password');

        $demoUsers = [
            [
                'name' => 'Employee Demo PO',
                'email' => 'employee.po.demo@gmail.com',
                'role' => 'employee',
            ],
            [
                'name' => 'Akuntan Demo PO',
                'email' => 'akuntan.po.demo@gmail.com',
                'role' => 'akuntan',
            ],
            [
                'name' => 'Verval Demo PO',
                'email' => 'verval.po.demo@gmail.com',
                'role' => 'verval',
            ],
            [
                'name' => 'Head Demo PO',
                'email' => 'head.po.demo@gmail.com',
                'role' => 'head',
            ],
        ];

        $employeeDemo = null;
        $approverUsers = collect();

        foreach ($demoUsers as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $defaultPassword,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$data['role']]);
            $approverUsers->push($user);

            if ($data['role'] === 'employee') {
                $employeeDemo = $user;
            }
        }

        if ($employeeDemo) {
            $sppg = Sppg::find(1);
            if ($sppg) {
                if (empty($sppg->user_id)) {
                    $sppg->update(['user_id' => $employeeDemo->id]);
                }

                $sppg->users()->syncWithoutDetaching(
                    $approverUsers->pluck('id')->map(fn ($id) => (int) $id)->all()
                );
            }
        }
    }
}

