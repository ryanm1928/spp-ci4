<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

class SppDummySeeder extends Seeder
{
    public function run()
    {
        $db        = $this->db;
        $userModel = new UserModel();
        $now       = date('Y-m-d H:i:s');

        /*
        |--------------------------------------------------------------------------
        | 1. Tahun Ajaran
        |--------------------------------------------------------------------------
        */
        $tahunAjaranNama = '2025/2026';

        $tahunAjaran = $db->table('tahun_ajaran')
            ->where('nama_tahun_ajaran', $tahunAjaranNama)
            ->get()
            ->getRow();

        if (! $tahunAjaran) {
            $db->table('tahun_ajaran')->insert([
                'nama_tahun_ajaran' => $tahunAjaranNama,
                'tanggal_mulai'     => '2025-07-01',
                'tanggal_selesai'   => '2026-06-30',
                'nominal_spp'       => 80000,
                'is_active'         => 1,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            $tahunAjaranId = $db->insertID();
        } else {
            $tahunAjaranId = $tahunAjaran->id;

            $db->table('tahun_ajaran')
                ->where('id', $tahunAjaranId)
                ->update([
                    'tanggal_mulai'   => '2025-07-01',
                    'tanggal_selesai' => '2026-06-30',
                    'nominal_spp'     => 80000,
                    'is_active'       => 1,
                    'updated_at'      => $now,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Nonaktifkan tahun ajaran lain (opsional tapi biasanya dibutuhkan)
        |--------------------------------------------------------------------------
        */
        $db->table('tahun_ajaran')
            ->where('id !=', $tahunAjaranId)
            ->update([
                'is_active'  => 0,
                'updated_at' => $now,
            ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Kelas TK A dan TK B
        |--------------------------------------------------------------------------
        */
        $kelasData = [
            [
                'nama_kelas' => 'TK A',
                'deskripsi'  => 'Kelas TK A',
            ],
            [
                'nama_kelas' => 'TK B',
                'deskripsi'  => 'Kelas TK B',
            ],
        ];

        $kelasIds = [];

        foreach ($kelasData as $kelas) {
            $existingKelas = $db->table('kelas')
                ->where('nama_kelas', $kelas['nama_kelas'])
                ->get()
                ->getRow();

            if ($existingKelas) {
                $kelasIds[$kelas['nama_kelas']] = $existingKelas->id;

                $db->table('kelas')
                    ->where('id', $existingKelas->id)
                    ->update([
                        'deskripsi'  => $kelas['deskripsi'],
                        'deleted_at' => null,
                        'updated_at' => $now,
                    ]);
            } else {
                $db->table('kelas')->insert([
                    'nama_kelas' => $kelas['nama_kelas'],
                    'deskripsi'  => $kelas['deskripsi'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $kelasIds[$kelas['nama_kelas']] = $db->insertID();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Data Guru
        |--------------------------------------------------------------------------
        */
        $guruData = [
            [
                'email'    => 'guru1@sekolah.com',
                'username' => 'guru1',
                'password' => 'guru12345',
            ],
            [
                'email'    => 'guru2@sekolah.com',
                'username' => 'guru2',
                'password' => 'guru12345',
            ],
            [
                'email'    => 'guru3@sekolah.com',
                'username' => 'guru3',
                'password' => 'guru12345',
            ],
        ];

        $guruIds = [];

        foreach ($guruData as $row) {
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
            }

            $guruIds[$row['username']] = $existingUser->id;
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Masukkan guru ke group "guru"
        |--------------------------------------------------------------------------
        */
        $guruGroup = $db->table('auth_groups')
            ->where('name', 'guru')
            ->get()
            ->getRow();

        if (! $guruGroup) {
            echo "Group guru tidak ditemukan di tabel auth_groups.\n";
            return;
        }

        foreach ($guruIds as $userId) {
            $existsGroupUser = $db->table('auth_groups_users')
                ->where('group_id', $guruGroup->id)
                ->where('user_id', $userId)
                ->countAllResults();

            if (! $existsGroupUser) {
                $db->table('auth_groups_users')->insert([
                    'group_id' => $guruGroup->id,
                    'user_id'  => $userId,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Kelas Tahun Ajaran
        |--------------------------------------------------------------------------
        */
        $kelasTahunAjaranMap = [
            'TK A' => $guruIds['guru1'],
            'TK B' => $guruIds['guru2'],
        ];

        $kelasTahunAjaranIds = [];

        foreach ($kelasTahunAjaranMap as $namaKelas => $waliKelasUserId) {
            $existingKta = $db->table('kelas_tahun_ajaran')
                ->where('kelas_id', $kelasIds[$namaKelas])
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->get()
                ->getRow();

            if ($existingKta) {
                $kelasTahunAjaranIds[$namaKelas] = $existingKta->id;

                $db->table('kelas_tahun_ajaran')
                    ->where('id', $existingKta->id)
                    ->update([
                        'wali_kelas_user_id' => $waliKelasUserId,
                        'kuota_siswa'        => 25,
                        'deleted_at'         => null,
                        'updated_at'         => $now,
                    ]);
            } else {
                $db->table('kelas_tahun_ajaran')->insert([
                    'kelas_id'           => $kelasIds[$namaKelas],
                    'tahun_ajaran_id'    => $tahunAjaranId,
                    'wali_kelas_user_id' => $waliKelasUserId,
                    'kuota_siswa'        => 25,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);

                $kelasTahunAjaranIds[$namaKelas] = $db->insertID();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Data Siswa Dummy
        |--------------------------------------------------------------------------
        */
        $siswaData = [
            // TK A
            [
                'nis'                   => 'SIS001',
                'nama_siswa'            => 'Ahmad Fauzan',
                'jenis_kelamin'         => 'L',
                'kelas_tahun_ajaran_id' => $kelasTahunAjaranIds['TK A'],
                'nama_orang_tua'        => 'Bapak Rahmat',
                'nomor_hp_orang_tua'    => '081234567801',
                'alamat'                => 'Jl. Melati No. 1',
            ],
            [
                'nis'                   => 'SIS002',
                'nama_siswa'            => 'Siti Aisyah',
                'jenis_kelamin'         => 'P',
                'kelas_tahun_ajaran_id' => $kelasTahunAjaranIds['TK A'],
                'nama_orang_tua'        => 'Ibu Nurhayati',
                'nomor_hp_orang_tua'    => '081234567802',
                'alamat'                => 'Jl. Kenanga No. 2',
            ],
            [
                'nis'                   => 'SIS003',
                'nama_siswa'            => 'Dewi Kartika',
                'jenis_kelamin'         => 'P',
                'kelas_tahun_ajaran_id' => $kelasTahunAjaranIds['TK A'],
                'nama_orang_tua'        => 'Bapak Hendra',
                'nomor_hp_orang_tua'    => '081234567803',
                'alamat'                => 'Jl. Flamboyan No. 3',
            ],

            // TK B
            [
                'nis'                   => 'SIS004',
                'nama_siswa'            => 'Budi Santoso',
                'jenis_kelamin'         => 'L',
                'kelas_tahun_ajaran_id' => $kelasTahunAjaranIds['TK B'],
                'nama_orang_tua'        => 'Bapak Santoso',
                'nomor_hp_orang_tua'    => '081234567804',
                'alamat'                => 'Jl. Mawar No. 4',
            ],
            [
                'nis'                   => 'SIS005',
                'nama_siswa'            => 'Citra Lestari',
                'jenis_kelamin'         => 'P',
                'kelas_tahun_ajaran_id' => $kelasTahunAjaranIds['TK B'],
                'nama_orang_tua'        => 'Ibu Lestari',
                'nomor_hp_orang_tua'    => '081234567805',
                'alamat'                => 'Jl. Anggrek No. 5',
            ],
            [
                'nis'                   => 'SIS006',
                'nama_siswa'            => 'Eko Prasetyo',
                'jenis_kelamin'         => 'L',
                'kelas_tahun_ajaran_id' => $kelasTahunAjaranIds['TK B'],
                'nama_orang_tua'        => 'Ibu Rina',
                'nomor_hp_orang_tua'    => '081234567806',
                'alamat'                => 'Jl. Dahlia No. 6',
            ],
        ];

        foreach ($siswaData as $siswa) {
            $existingSiswa = $db->table('siswa')
                ->where('nis', $siswa['nis'])
                ->get()
                ->getRow();

            $payload = [
                'nama_siswa'            => $siswa['nama_siswa'],
                'jenis_kelamin'         => $siswa['jenis_kelamin'],
                'kelas_tahun_ajaran_id' => $siswa['kelas_tahun_ajaran_id'],
                'nama_orang_tua'        => $siswa['nama_orang_tua'],
                'nomor_hp_orang_tua'    => $siswa['nomor_hp_orang_tua'],
                'alamat'                => $siswa['alamat'],
                'status_aktif'          => 1,
                'updated_at'            => $now,
                'deleted_at'            => null,
            ];

            if ($existingSiswa) {
                $db->table('siswa')
                    ->where('id', $existingSiswa->id)
                    ->update($payload);
            } else {
                $payload['nis']        = $siswa['nis'];
                $payload['created_at'] = $now;

                $db->table('siswa')->insert($payload);
            }
        }

        echo "Seeder dummy SPP berhasil dijalankan.\n";
        echo "- Tahun ajaran aktif: {$tahunAjaranNama}\n";
        echo "- Kelas: TK A, TK B\n";
        echo "- Guru: guru1, guru2, guru3\n";
        echo "- Siswa: 6 data dummy\n";
    }
}
