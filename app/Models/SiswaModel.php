<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'nis',
        'nama_siswa',
        'jenis_kelamin',
        'kelas_tahun_ajaran_id',
        'nama_orang_tua',
        'nomor_hp_orang_tua',
        'alamat',
        'gambar_siswa',
        'status_aktif',
    ];

    protected $useTimestamps = true;
    // protected $useSoftDeletes = true;

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}