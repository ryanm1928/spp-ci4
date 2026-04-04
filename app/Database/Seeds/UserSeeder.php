<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $db        = db_connect();
        $userModel = new UserModel();
        $now       = date('Y-m-d H:i:s');

        /*
        |--------------------------------------------------------------------------
        | 1. Seeder group auth
        |--------------------------------------------------------------------------
        */
        $groups = [
            [
                'name'        => 'admin',
                'description' => 'Administrator sistem',
            ],
            [
                'name'        => 'kepala_sekolah',
                'description' => 'Kepala sekolah',
            ],
            [
                'name'        => 'guru',
                'description' => 'Guru',
            ],
        ];

        foreach ($groups as $group) {
            $existingGroup = $db->table('auth_groups')
                ->where('name', $group['name'])
                ->get()
                ->getRow();

            if ($existingGroup) {
                $db->table('auth_groups')
                    ->where('id', $existingGroup->id)
                    ->update([
                        'description' => $group['description'],
                    ]);
            } else {
                $db->table('auth_groups')->insert($group);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Seeder user
        |--------------------------------------------------------------------------
        */
        $users = [
            [
                'email'    => 'admin@sekolah.com',
                'username' => 'admin',
                'password' => 'admin12345',
                'group'    => 'admin',
            ],
            [
                'email'    => 'kepsek@sekolah.com',
                'username' => 'kepsek',
                'password' => 'kepsek12345',
                'group'    => 'kepala_sekolah',
            ],
            [
                'email'    => 'guru@sekolah.com',
                'username' => 'guru',
                'password' => 'guru12345',
                'group'    => 'guru',
            ],
        ];

        foreach ($users as $row) {
            $existingUser = $userModel->where('email', $row['email'])->first();

            if (! $existingUser) {
                $user = new User([
                    'email'    => $row['email'],
                    'username' => $row['username'],
                    'password' => $row['password'],
                    'active'   => 1,
                ]);

                $userModel->save($user);
                $existingUser = $userModel->where('email', $row['email'])->first();
            } else {
                $userModel->save([
                    'id'       => $existingUser->id,
                    'username' => $row['username'],
                    'active'   => 1,
                ]);

                $existingUser = $userModel->where('email', $row['email'])->first();
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Assign group ke user
            |--------------------------------------------------------------------------
            */
            $group = $db->table('auth_groups')
                ->where('name', $row['group'])
                ->get()
                ->getRow();

            if ($group && $existingUser) {
                $existsGroupUser = $db->table('auth_groups_users')
                    ->where('group_id', $group->id)
                    ->where('user_id', $existingUser->id)
                    ->countAllResults();

                if (! $existsGroupUser) {
                    $db->table('auth_groups_users')->insert([
                        'group_id' => $group->id,
                        'user_id'  => $existingUser->id,
                    ]);
                }
            }
        }

        echo "Seeder group dan user berhasil dijalankan.\n";
        echo "Admin  : admin@sekolah.com / admin12345\n";
        echo "Kepsek : kepsek@sekolah.com / kepsek12345\n";
        echo "Guru   : guru@sekolah.com / guru12345\n";
    }
}
