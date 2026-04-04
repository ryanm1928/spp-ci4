<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class AdminController extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        // Total siswa aktif
        $totalSiswa = $db->table('siswa')
            ->where('status_aktif', 1)
            ->where('deleted_at', null)
            ->countAllResults();

        // Total kelas
        $totalKelas = $db->table('kelas')
            ->where('deleted_at', null)
            ->countAllResults();

        // Tahun ajaran aktif
        $tahunAjaranAktif = $db->table('tahun_ajaran')
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->get()
            ->getRow();

        // Total guru
        $totalGuru = $db->table('auth_groups_users agu')
            ->join('auth_groups ag', 'ag.id = agu.group_id')
            ->join('users u', 'u.id = agu.user_id')
            ->where('ag.name', 'guru')
            ->where('u.deleted_at', null)
            ->countAllResults();

        // Total kepala sekolah
        $totalKepalaSekolah = $db->table('auth_groups_users agu')
            ->join('auth_groups ag', 'ag.id = agu.group_id')
            ->join('users u', 'u.id = agu.user_id')
            ->where('ag.name', 'kepala_sekolah')
            ->where('u.deleted_at', null)
            ->countAllResults();

        // Total admin
        $totalAdmin = $db->table('auth_groups_users agu')
            ->join('auth_groups ag', 'ag.id = agu.group_id')
            ->join('users u', 'u.id = agu.user_id')
            ->where('ag.name', 'admin')
            ->where('u.deleted_at', null)
            ->countAllResults();

        // Ringkasan tagihan
        $totalBelumBayar = $db->table('tagihan_spp')
            ->where('status_pembayaran', 'belum_bayar')
            ->countAllResults();

        $totalSebagian = $db->table('tagihan_spp')
            ->where('status_pembayaran', 'sebagian')
            ->countAllResults();

        $totalLunas = $db->table('tagihan_spp')
            ->where('status_pembayaran', 'lunas')
            ->countAllResults();

        // Total pembayaran aktif
        $totalPembayaran = $db->table('pembayaran_spp')
            ->where('status_pembayaran_record', 'active')
            ->countAllResults();

        // Total nominal pembayaran aktif
        $totalNominalPembayaran = $db->table('pembayaran_spp')
            ->selectSum('jumlah_bayar')
            ->where('status_pembayaran_record', 'active')
            ->get()
            ->getRow()
            ->jumlah_bayar ?? 0;

        // Saldo kas aktif
        $kasAktif = $db->table('kas_sekolah')
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();

        // Pembayaran terbaru
        $pembayaranTerbaru = $db->table('pembayaran_spp p')
            ->select('
                p.id,
                p.kode_pembayaran,
                p.tanggal_bayar,
                p.jumlah_bayar,
                p.metode_pembayaran,
                s.nama_siswa,
                s.nis
            ')
            ->join('tagihan_spp t', 't.id = p.tagihan_spp_id')
            ->join('siswa s', 's.id = t.siswa_id')
            ->where('p.status_pembayaran_record', 'active')
            ->orderBy('p.tanggal_bayar', 'DESC')
            ->limit(5)
            ->get()
            ->getResult();

        return view('admin/dashboard', [
            'title'                 => 'Dashboard Admin',
            'menu'                  => 'dashboard',
            'totalSiswa'            => $totalSiswa,
            'totalKelas'            => $totalKelas,
            'tahunAjaranAktif'      => $tahunAjaranAktif,
            'totalGuru'             => $totalGuru,
            'totalKepalaSekolah'    => $totalKepalaSekolah,
            'totalAdmin'            => $totalAdmin,
            'totalBelumBayar'       => $totalBelumBayar,
            'totalSebagian'         => $totalSebagian,
            'totalLunas'            => $totalLunas,
            'totalPembayaran'       => $totalPembayaran,
            'totalNominalPembayaran' => $totalNominalPembayaran,
            'kasAktif'              => $kasAktif,
            'pembayaranTerbaru'     => $pembayaranTerbaru,
        ]);
    }
}
