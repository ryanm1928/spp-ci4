<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuthGroupSeeder extends Seeder
{
    public function run()
    {
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
            $exists = $this->db->table('auth_groups')
                ->where('name', $group['name'])
                ->get()
                ->getRow();

            if (! $exists) {
                $this->db->table('auth_groups')->insert($group);
            }
        }
    }
}
