<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class SppController extends BaseController
{
    public function index()
    {
        helper('auth');

        $db     = Database::connect();
        $userId = user_id();
        $search = trim((string) $this->request->getGet('search'));
        $selectedKelasTahunAjaranId = (int) $this->request->getGet('kelas_tahun_ajaran_id');

        $activeYear = $db->table('tahun_ajaran')
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        $kelasGuru = $db->table('kelas_tahun_ajaran kta')
            ->select('kta.id, k.nama_kelas, ta.nama_tahun_ajaran')
            ->join('kelas k', 'k.id = kta.kelas_id', 'left')
            ->join('tahun_ajaran ta', 'ta.id = kta.tahun_ajaran_id', 'left')
            ->where('kta.wali_kelas_user_id', $userId)
            ->where('kta.deleted_at', null)
            ->where('k.deleted_at', null)
            ->where('ta.deleted_at', null)
            ->where('ta.is_active', 1)
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $kelasGuruIds = array_map(static fn($row) => (int) ($row['id'] ?? 0), $kelasGuru);
        if ($selectedKelasTahunAjaranId > 0 && ! in_array($selectedKelasTahunAjaranId, $kelasGuruIds, true)) {
            $selectedKelasTahunAjaranId = 0;
        }

        $periodeList = $this->buildPeriodeList($activeYear);
        [$selectedBulan, $selectedTahun, $selectedPeriodeKey] = $this->resolveSelectedPeriode($periodeList);

        $siswaList = [];
        $totalSiswa = 0;
        $totalLunas = 0;
        $totalBelumLunas = 0;
        $totalSebagian = 0;
        $totalSisa = 0;
        $totalTagihanPeriode = 0;
        $totalTerbayarPeriode = 0;
        $pembayaranPeriodeGuru = 0;
        $saldoSppBulanan = null;

        if (! empty($activeYear) && $selectedBulan !== null && $selectedTahun !== null) {
            $siswaBuilder = $db->table('siswa s')
                ->select(
                    's.id,
                    s.nis,
                    s.nama_siswa,
                    s.nama_orang_tua,
                    s.nomor_hp_orang_tua,
                    kta.id as kelas_tahun_ajaran_id,
                    k.nama_kelas,
                    ta.id as tahun_ajaran_id,
                    ta.nama_tahun_ajaran,
                    ta.nominal_spp,
                    ts.id as tagihan_spp_id,
                    ts.nominal_tagihan,
                    ts.nominal_terbayar,
                    ts.status_pembayaran'
                )
                ->join('kelas_tahun_ajaran kta', 'kta.id = s.kelas_tahun_ajaran_id')
                ->join('kelas k', 'k.id = kta.kelas_id', 'left')
                ->join('tahun_ajaran ta', 'ta.id = kta.tahun_ajaran_id', 'left')
                ->join(
                    'tagihan_spp ts',
                    'ts.siswa_id = s.id
                    AND ts.kelas_tahun_ajaran_id = kta.id
                    AND ts.tahun_ajaran_id = ta.id
                    AND ts.bulan = ' . $db->escape($selectedBulan) . '
                    AND ts.tahun = ' . $db->escape($selectedTahun),
                    'left'
                )
                ->where('kta.wali_kelas_user_id', $userId)
                ->where('s.status_aktif', 1)
                ->where('s.deleted_at', null)
                ->where('kta.deleted_at', null)
                ->where('k.deleted_at', null)
                ->where('ta.deleted_at', null)
                ->where('ta.is_active', 1)
                ->where('ta.id', $activeYear['id']);

            if ($selectedKelasTahunAjaranId > 0) {
                $siswaBuilder->where('kta.id', $selectedKelasTahunAjaranId);
            }

            if ($search !== '') {
                $siswaBuilder->groupStart()
                    ->like('s.nama_siswa', $search)
                    ->orLike('s.nis', $search)
                    ->orLike('s.nama_orang_tua', $search)
                    ->groupEnd();
            }

            $siswaList = $siswaBuilder
                ->orderBy('k.nama_kelas', 'ASC')
                ->orderBy('s.nama_siswa', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($siswaList as &$row) {
                $nominalTagihan = (float) ($row['nominal_tagihan'] ?? $row['nominal_spp'] ?? 0);
                $nominalTerbayar = (float) ($row['nominal_terbayar'] ?? 0);
                $sisa = $nominalTagihan - $nominalTerbayar;

                if ($sisa < 0) {
                    $sisa = 0;
                }

                if (! empty($row['status_pembayaran'])) {
                    $status = (string) $row['status_pembayaran'];
                } elseif ($sisa <= 0 && $nominalTagihan > 0) {
                    $status = 'lunas';
                } else {
                    $status = 'belum_bayar';
                }

                $row['nominal_tagihan_final'] = $nominalTagihan;
                $row['nominal_terbayar_final'] = $nominalTerbayar;
                $row['sisa_tagihan'] = $sisa;
                $row['status_pembayaran_final'] = $status;
                $row['jumlah_bayar_default'] = $sisa > 0 ? $sisa : $nominalTagihan;

                $totalTagihanPeriode += $nominalTagihan;
                $totalTerbayarPeriode += $nominalTerbayar;
                $totalSisa += $sisa;

                if ($status === 'lunas') {
                    $totalLunas++;
                } elseif ($status === 'sebagian') {
                    $totalSebagian++;
                    $totalBelumLunas++;
                } else {
                    $totalBelumLunas++;
                }
            }
            unset($row);

            $totalSiswa = count($siswaList);

            $pembayaranPeriodeGuruBuilder = $db->table('pembayaran_spp ps')
                ->select('COALESCE(SUM(ps.jumlah_bayar), 0) as total_bayar')
                ->join('tagihan_spp ts', 'ts.id = ps.tagihan_spp_id')
                ->join('kelas_tahun_ajaran kta', 'kta.id = ts.kelas_tahun_ajaran_id')
                ->where('ps.dicatat_oleh_user_id', $userId)
                ->where('kta.wali_kelas_user_id', $userId)
                ->where('ts.tahun_ajaran_id', $activeYear['id'])
                ->where('ts.bulan', $selectedBulan)
                ->where('ts.tahun', $selectedTahun);

            if ($selectedKelasTahunAjaranId > 0) {
                $pembayaranPeriodeGuruBuilder->where('kta.id', $selectedKelasTahunAjaranId);
            }

            $pembayaranPeriodeGuruRow = $pembayaranPeriodeGuruBuilder
                ->get()
                ->getRowArray();

            $pembayaranPeriodeGuru = (float) ($pembayaranPeriodeGuruRow['total_bayar'] ?? 0);

            if ($this->tableExists($db, 'saldo_spp_bulanan')) {
                $saldoSppBulanan = $db->table('saldo_spp_bulanan')
                    ->where('tahun_ajaran_id', $activeYear['id'])
                    ->where('bulan', $selectedBulan)
                    ->where('tahun', $selectedTahun)
                    ->get()
                    ->getRowArray();
            }
        }

            $riwayatPembayaranBuilder = $db->table('pembayaran_spp ps')
                ->select(
                    'ps.id,
            ps.kode_pembayaran,
            ps.tanggal_bayar,
            ps.jumlah_bayar,
            ps.metode_pembayaran,
            ps.keterangan,
            ps.wa_notif_status,
            ps.wa_notif_opened_at,
            ps.wa_notif_sent_at,
            s.nama_siswa,
            s.nis,
            s.nama_orang_tua,
            s.nomor_hp_orang_tua,
            k.nama_kelas,
            ts.bulan,
            ts.tahun'
                )
            ->join('tagihan_spp ts', 'ts.id = ps.tagihan_spp_id')
            ->join('siswa s', 's.id = ts.siswa_id')
            ->join('kelas_tahun_ajaran kta', 'kta.id = ts.kelas_tahun_ajaran_id')
            ->join('kelas k', 'k.id = kta.kelas_id', 'left')
            ->where('kta.wali_kelas_user_id', $userId);

        if (! empty($activeYear) && $selectedBulan !== null && $selectedTahun !== null) {
            $riwayatPembayaranBuilder
                ->where('ts.tahun_ajaran_id', $activeYear['id'])
                ->where('ts.bulan', $selectedBulan)
                ->where('ts.tahun', $selectedTahun);
        }

        if ($selectedKelasTahunAjaranId > 0) {
            $riwayatPembayaranBuilder->where('kta.id', $selectedKelasTahunAjaranId);
        }

        $riwayatPembayaran = $riwayatPembayaranBuilder
            ->orderBy('ps.tanggal_bayar', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return view('guru/spp/index', [
            'menu'                 => 'spp',
            'title'                => 'Input SPP Siswa',
            'search'               => $search,
            'activeYear'           => $activeYear,
            'kelasGuru'            => $kelasGuru,
            'periodeList'          => $periodeList,
            'selectedBulan'        => $selectedBulan,
            'selectedTahun'        => $selectedTahun,
            'selectedPeriodeKey'   => $selectedPeriodeKey,
            'selectedKelasTahunAjaranId' => $selectedKelasTahunAjaranId,
            'siswaList'            => $siswaList,
            'totalSiswa'           => $totalSiswa,
            'totalTagihan'         => $totalTagihanPeriode,
            'totalTerbayar'        => $totalTerbayarPeriode,
            'totalLunas'           => $totalLunas,
            'totalSebagian'        => $totalSebagian,
            'totalBelumLunas'      => $totalBelumLunas,
            'totalSisa'            => $totalSisa,
            'pembayaranPeriodeGuru' => $pembayaranPeriodeGuru,
            'saldoSppBulanan'      => $saldoSppBulanan,
            'riwayatPembayaran'    => $riwayatPembayaran,
            'bulanMap'             => $this->bulanMap(),
        ]);
    }

    public function store()
    {
        helper('auth');

        $db     = Database::connect();
        $userId = user_id();

        $bulanTagihan = (int) $this->request->getPost('bulan_tagihan');
        $tahunTagihan = (int) $this->request->getPost('tahun_tagihan');
        $kelasFilterId = (int) $this->request->getPost('kelas_tahun_ajaran_filter_id');

        $redirectParams = [
            'bulan' => $bulanTagihan,
            'tahun' => $tahunTagihan,
        ];

        if ($kelasFilterId > 0) {
            $redirectParams['kelas_tahun_ajaran_id'] = $kelasFilterId;
        }

        $redirectUrl = site_url('guru/spp?' . http_build_query($redirectParams));

        $rules = [
            'bulan_tagihan'      => 'required|integer|greater_than[0]|less_than_equal_to[12]',
            'tahun_tagihan'      => 'required|integer|greater_than[2000]',
            'siswa_id'           => 'required|numeric',
            'tanggal_bayar'      => 'required',
            'jumlah_bayar'       => 'required|decimal|greater_than[0]',
            'metode_pembayaran'  => 'required|in_list[tunai,transfer,qris,lainnya]',
            'keterangan'         => 'permit_empty|string|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($redirectUrl)
                ->withInput()
                ->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $activeYear = $db->table('tahun_ajaran')
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (! $activeYear) {
            return redirect()->to(site_url('guru/spp'))
                ->withInput()
                ->with('error', 'Tahun ajaran aktif belum tersedia.');
        }

        if (! $this->isPeriodeInsideTahunAjaran($activeYear, $bulanTagihan, $tahunTagihan)) {
            return redirect()->to(site_url('guru/spp'))
                ->withInput()
                ->with('error', 'Periode pembayaran tidak termasuk ke dalam tahun ajaran aktif.');
        }

        $siswaId = (int) $this->request->getPost('siswa_id');
        $tanggalBayarRaw = (string) $this->request->getPost('tanggal_bayar');
        $jumlahBayarInput = (float) $this->request->getPost('jumlah_bayar');
        $metodePembayaran = (string) $this->request->getPost('metode_pembayaran');
        $keterangan = trim((string) $this->request->getPost('keterangan'));

        $siswa = $db->table('siswa s')
            ->select(
                's.*,
                kta.id as kelas_tahun_ajaran_id,
                kta.wali_kelas_user_id,
                ta.id as tahun_ajaran_id,
                ta.nama_tahun_ajaran,
                ta.nominal_spp'
            )
            ->join('kelas_tahun_ajaran kta', 'kta.id = s.kelas_tahun_ajaran_id')
            ->join('tahun_ajaran ta', 'ta.id = kta.tahun_ajaran_id')
            ->where('s.id', $siswaId)
            ->where('kta.wali_kelas_user_id', $userId)
            ->where('ta.is_active', 1)
            ->where('ta.id', $activeYear['id'])
            ->where('s.status_aktif', 1)
            ->where('s.deleted_at', null)
            ->get()
            ->getRowArray();

        if (! $siswa) {
            return redirect()->to($redirectUrl)
                ->withInput()
                ->with('error', 'Siswa tidak ditemukan atau tidak dapat diakses.');
        }

        if (! $this->tableExists($db, 'saldo_spp_bulanan') || ! $this->tableExists($db, 'transaksi_spp_bulanan')) {
            return redirect()->to($redirectUrl)
                ->withInput()
                ->with('error', 'Table saldo SPP bulanan belum tersedia. Jalankan file revisi SQL terlebih dahulu.');
        }

        $tagihan = $db->table('tagihan_spp')
            ->where('siswa_id', $siswa['id'])
            ->where('kelas_tahun_ajaran_id', $siswa['kelas_tahun_ajaran_id'])
            ->where('tahun_ajaran_id', $siswa['tahun_ajaran_id'])
            ->where('bulan', $bulanTagihan)
            ->where('tahun', $tahunTagihan)
            ->get()
            ->getRowArray();

        $nominalTagihanDefault = (float) ($siswa['nominal_spp'] ?? 0);
        $now = date('Y-m-d H:i:s');

        $db->transStart();

        if (! $tagihan) {
            $db->table('tagihan_spp')->insert([
                'siswa_id'              => $siswa['id'],
                'kelas_tahun_ajaran_id' => $siswa['kelas_tahun_ajaran_id'],
                'tahun_ajaran_id'       => $siswa['tahun_ajaran_id'],
                'bulan'                 => $bulanTagihan,
                'tahun'                 => $tahunTagihan,
                'nominal_tagihan'       => $nominalTagihanDefault,
                'nominal_terbayar'      => 0,
                'tanggal_jatuh_tempo'   => date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $tahunTagihan, $bulanTagihan))),
                'status_pembayaran'     => 'belum_bayar',
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);

            $tagihanId = $db->insertID();
            $tagihan = $db->table('tagihan_spp')->where('id', $tagihanId)->get()->getRowArray();
        }

        $sisaTagihan = (float) $tagihan['nominal_tagihan'] - (float) $tagihan['nominal_terbayar'];
        if ($sisaTagihan < 0) {
            $sisaTagihan = 0;
        }

        if ($sisaTagihan <= 0) {
            $db->transRollback();
            return redirect()->to($redirectUrl)
                ->with('error', 'Tagihan periode ini sudah lunas.');
        }

        $jumlahBayar = $sisaTagihan;
        if (abs($jumlahBayarInput - $jumlahBayar) > 0.01) {
            $db->transRollback();
            return redirect()->to($redirectUrl)
                ->withInput()
                ->with('error', 'Nominal pembayaran harus mengikuti saldo tagihan periode ini, yaitu Rp ' . number_format($jumlahBayar, 0, ',', '.'));
        }

        $tanggalBayar = date('Y-m-d H:i:s', strtotime($tanggalBayarRaw));
        $kodeBayar = $this->generateKodePembayaran();

        $db->table('pembayaran_spp')->insert([
            'tagihan_spp_id'       => $tagihan['id'],
            'kode_pembayaran'      => $kodeBayar,
            'tanggal_bayar'        => $tanggalBayar,
            'jumlah_bayar'         => $jumlahBayar,
            'metode_pembayaran'    => $metodePembayaran,
            'dicatat_oleh_user_id' => $userId,
            'keterangan'           => $keterangan !== '' ? $keterangan : null,
            'created_at'           => $now,
            'updated_at'           => $now,
        ]);

        $pembayaranId = $db->insertID();
        $nominalTerbayarBaru = (float) $tagihan['nominal_terbayar'] + $jumlahBayar;

        if ($nominalTerbayarBaru <= 0) {
            $statusPembayaran = 'belum_bayar';
        } elseif ($nominalTerbayarBaru < (float) $tagihan['nominal_tagihan']) {
            $statusPembayaran = 'sebagian';
        } else {
            $statusPembayaran = 'lunas';
        }

        $db->table('tagihan_spp')
            ->where('id', $tagihan['id'])
            ->update([
                'nominal_terbayar'  => $nominalTerbayarBaru,
                'status_pembayaran' => $statusPembayaran,
                'updated_at'        => $now,
            ]);

        $saldoSppBulanan = $this->firstOrCreateSaldoSppBulanan($db, (int) $activeYear['id'], $bulanTagihan, $tahunTagihan, $now);

        $saldoSppSebelum = (float) ($saldoSppBulanan['saldo_akhir'] ?? 0);
        $saldoSppSesudah = $saldoSppSebelum + $jumlahBayar;
        $totalMasukBaru = (float) ($saldoSppBulanan['total_masuk'] ?? 0) + $jumlahBayar;

        $db->table('transaksi_spp_bulanan')->insert([
            'saldo_spp_bulanan_id' => $saldoSppBulanan['id'],
            'pembayaran_spp_id'   => $pembayaranId,
            'tanggal_transaksi'   => $tanggalBayar,
            'jenis_transaksi'     => 'debit',
            'sumber_transaksi'    => 'pembayaran_spp',
            'kategori'            => 'Pembayaran SPP',
            'deskripsi'           => 'Pembayaran SPP siswa ' . $siswa['nama_siswa'] . ' (' . $siswa['nis'] . ')',
            'nominal'             => $jumlahBayar,
            'saldo_sebelum'       => $saldoSppSebelum,
            'saldo_sesudah'       => $saldoSppSesudah,
            'dibuat_oleh_user_id' => $userId,
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        $db->table('saldo_spp_bulanan')
            ->where('id', $saldoSppBulanan['id'])
            ->update([
                'total_masuk' => $totalMasukBaru,
                'saldo_akhir' => $saldoSppSesudah,
                'updated_at'  => $now,
            ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to($redirectUrl)
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pembayaran SPP.');
        }

        return redirect()->to($redirectUrl)
            ->with('success', 'Pembayaran SPP periode ' . $this->formatPeriode($bulanTagihan, $tahunTagihan) . ' berhasil disimpan.');
    }

    public function sendWhatsapp($id)
    {
        helper('auth');

        $db     = Database::connect();
        $userId = user_id();
        $id     = (int) $id;

        $pembayaran = $db->table('pembayaran_spp ps')
            ->select(
                'ps.id,
                ps.kode_pembayaran,
                ps.tanggal_bayar,
                ps.jumlah_bayar,
                ps.metode_pembayaran,
                ps.keterangan,
                s.nama_siswa,
                s.nis,
                s.nama_orang_tua,
                s.nomor_hp_orang_tua,
                k.nama_kelas,
                ts.bulan,
                ts.tahun,
                ts.nominal_tagihan,
                ts.nominal_terbayar,
                kta.id as kelas_tahun_ajaran_id'
            )
            ->join('tagihan_spp ts', 'ts.id = ps.tagihan_spp_id')
            ->join('siswa s', 's.id = ts.siswa_id')
            ->join('kelas_tahun_ajaran kta', 'kta.id = ts.kelas_tahun_ajaran_id')
            ->join('kelas k', 'k.id = kta.kelas_id', 'left')
            ->where('ps.id', $id)
            ->where('kta.wali_kelas_user_id', $userId)
            ->get()
            ->getRowArray();

        if (! $pembayaran) {
            return redirect()->to(site_url('guru/spp'))
                ->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $redirectUrl = site_url('guru/spp?' . http_build_query([
            'bulan' => $pembayaran['bulan'],
            'tahun' => $pembayaran['tahun'],
            'kelas_tahun_ajaran_id' => $pembayaran['kelas_tahun_ajaran_id'],
        ]));

        $nomorWa = $this->normalizeWhatsappNumber((string) ($pembayaran['nomor_hp_orang_tua'] ?? ''));

        if ($nomorWa === '') {
            return redirect()->to($redirectUrl)
                ->with('error', 'Nomor WhatsApp orang tua belum tersedia.');
        }

        if ($this->hasWhatsappNotificationColumns($db)) {
            $db->table('pembayaran_spp')
                ->where('id', $pembayaran['id'])
                ->update([
                    'wa_notif_status'    => 'dibuka',
                    'wa_notif_opened_at' => date('Y-m-d H:i:s'),
                    'wa_notif_phone'     => $nomorWa,
                    'updated_at'         => date('Y-m-d H:i:s'),
                ]);
        }

        $message = $this->buildWhatsappPaymentMessage($pembayaran);
        $waUrl   = 'https://wa.me/' . $nomorWa . '?text=' . rawurlencode($message);

        return redirect()->to($waUrl);
    }

    public function confirmWhatsappSent($id)
    {
        helper('auth');

        $db     = Database::connect();
        $userId = user_id();
        $id     = (int) $id;

        if (! $this->hasWhatsappNotificationColumns($db)) {
            return redirect()->to(site_url('guru/spp'))
                ->with('error', 'Kolom status WhatsApp belum tersedia. Jalankan SQL revisi WhatsApp terlebih dahulu.');
        }

        $pembayaran = $db->table('pembayaran_spp ps')
            ->select(
                'ps.id,
                s.nomor_hp_orang_tua,
                ts.bulan,
                ts.tahun,
                kta.id as kelas_tahun_ajaran_id'
            )
            ->join('tagihan_spp ts', 'ts.id = ps.tagihan_spp_id')
            ->join('siswa s', 's.id = ts.siswa_id')
            ->join('kelas_tahun_ajaran kta', 'kta.id = ts.kelas_tahun_ajaran_id')
            ->where('ps.id', $id)
            ->where('kta.wali_kelas_user_id', $userId)
            ->get()
            ->getRowArray();

        if (! $pembayaran) {
            return redirect()->to(site_url('guru/spp'))
                ->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $redirectUrl = site_url('guru/spp?' . http_build_query([
            'bulan' => $pembayaran['bulan'],
            'tahun' => $pembayaran['tahun'],
            'kelas_tahun_ajaran_id' => $pembayaran['kelas_tahun_ajaran_id'],
        ]));

        $nomorWa = $this->normalizeWhatsappNumber((string) ($pembayaran['nomor_hp_orang_tua'] ?? ''));
        $now = date('Y-m-d H:i:s');

        $db->table('pembayaran_spp')
            ->where('id', $pembayaran['id'])
            ->update([
                'wa_notif_status'         => 'terkirim',
                'wa_notif_opened_at'      => $now,
                'wa_notif_sent_at'        => $now,
                'wa_notif_sent_by_user_id'=> $userId,
                'wa_notif_phone'          => $nomorWa !== '' ? $nomorWa : null,
                'updated_at'              => $now,
            ]);

        return redirect()->to($redirectUrl)
            ->with('success', 'Status notifikasi WhatsApp berhasil diubah menjadi terkirim.');
    }

    private function buildPeriodeList(?array $activeYear): array
    {
        if (empty($activeYear['tanggal_mulai']) || empty($activeYear['tanggal_selesai'])) {
            return [];
        }

        $start = new \DateTime(date('Y-m-01', strtotime((string) $activeYear['tanggal_mulai'])));
        $end = new \DateTime(date('Y-m-01', strtotime((string) $activeYear['tanggal_selesai'])));
        $result = [];

        while ($start <= $end) {
            $bulan = (int) $start->format('n');
            $tahun = (int) $start->format('Y');
            $key = sprintf('%04d-%02d', $tahun, $bulan);

            $result[] = [
                'key'   => $key,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'label' => $this->formatPeriode($bulan, $tahun),
            ];

            $start->modify('+1 month');
        }

        return $result;
    }

    private function resolveSelectedPeriode(array $periodeList): array
    {
        if (empty($periodeList)) {
            return [null, null, null];
        }

        $requestedBulan = (int) $this->request->getGet('bulan');
        $requestedTahun = (int) $this->request->getGet('tahun');
        $requestedKey = ($requestedTahun > 0 && $requestedBulan > 0)
            ? sprintf('%04d-%02d', $requestedTahun, $requestedBulan)
            : '';

        // 1. Kalau user pilih bulan tertentu dan valid, pakai itu
        foreach ($periodeList as $periode) {
            if ($requestedKey !== '' && $periode['key'] === $requestedKey) {
                return [
                    (int) $periode['bulan'],
                    (int) $periode['tahun'],
                    (string) $periode['key'],
                ];
            }
        }

        // 2. Default ke bulan sekarang sesuai timezone aplikasi
        $timezoneName = config('App')->appTimezone ?: date_default_timezone_get();
        $now = new \DateTime('now', new \DateTimeZone($timezoneName));

        $bulanSekarang = (int) $now->format('n');
        $tahunSekarang = (int) $now->format('Y');
        $currentKey = sprintf('%04d-%02d', $tahunSekarang, $bulanSekarang);

        foreach ($periodeList as $periode) {
            if ($periode['key'] === $currentKey) {
                return [
                    (int) $periode['bulan'],
                    (int) $periode['tahun'],
                    (string) $periode['key'],
                ];
            }
        }

        // 3. Kalau bulan sekarang tidak ada / di luar periode, default ke bulan 1
        foreach ($periodeList as $periode) {
            if ((int) $periode['bulan'] === 1) {
                return [
                    (int) $periode['bulan'],
                    (int) $periode['tahun'],
                    (string) $periode['key'],
                ];
            }
        }

        // 4. Fallback terakhir kalau memang bulan 1 juga tidak ada
        $first = $periodeList[0];

        return [
            (int) $first['bulan'],
            (int) $first['tahun'],
            (string) $first['key'],
        ];
    }

    private function isPeriodeInsideTahunAjaran(array $activeYear, int $bulan, int $tahun): bool
    {
        if ($bulan < 1 || $bulan > 12 || $tahun < 2000) {
            return false;
        }

        $periode = sprintf('%04d-%02d-01', $tahun, $bulan);
        $start = date('Y-m-01', strtotime((string) $activeYear['tanggal_mulai']));
        $end = date('Y-m-01', strtotime((string) $activeYear['tanggal_selesai']));

        return $periode >= $start && $periode <= $end;
    }

    private function firstOrCreateSaldoSppBulanan($db, int $tahunAjaranId, int $bulan, int $tahun, string $now): array
    {
        $row = $db->table('saldo_spp_bulanan')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->getRowArray();

        if ($row) {
            return $row;
        }

        $currentDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $previous = $db->query(
            "SELECT saldo_akhir
            FROM saldo_spp_bulanan
            WHERE tahun_ajaran_id = ?
              AND STR_TO_DATE(CONCAT(tahun, '-', LPAD(bulan, 2, '0'), '-01'), '%Y-%m-%d') < ?
            ORDER BY tahun DESC, bulan DESC
            LIMIT 1",
            [$tahunAjaranId, $currentDate]
        )->getRowArray();

        $saldoAwal = (float) ($previous['saldo_akhir'] ?? 0);

        $db->table('saldo_spp_bulanan')->insert([
            'tahun_ajaran_id' => $tahunAjaranId,
            'bulan'           => $bulan,
            'tahun'           => $tahun,
            'nama_periode'    => $this->formatPeriode($bulan, $tahun),
            'saldo_awal'      => $saldoAwal,
            'total_masuk'     => 0,
            'total_keluar'    => 0,
            'saldo_akhir'     => $saldoAwal,
            'is_locked'       => 0,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        return $db->table('saldo_spp_bulanan')
            ->where('id', $db->insertID())
            ->get()
            ->getRowArray();
    }

    private function normalizeWhatsappNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if ($phone === null || $phone === '') {
            return '';
        }

        if (strpos($phone, '0') === 0) {
            return '62' . substr($phone, 1);
        }

        if (strpos($phone, '62') === 0) {
            return $phone;
        }

        return $phone;
    }

    private function buildWhatsappPaymentMessage(array $pembayaran): string
    {
        $namaOrangTua    = trim((string) ($pembayaran['nama_orang_tua'] ?? 'Bapak/Ibu'));
        $namaSiswa       = trim((string) ($pembayaran['nama_siswa'] ?? '-'));
        $nis             = trim((string) ($pembayaran['nis'] ?? '-'));
        $kelas           = trim((string) ($pembayaran['nama_kelas'] ?? '-'));
        $periode         = $this->formatPeriode((int) ($pembayaran['bulan'] ?? 0), (int) ($pembayaran['tahun'] ?? 0));
        $tanggalBayar    = ! empty($pembayaran['tanggal_bayar'])
            ? date('d-m-Y H:i', strtotime((string) $pembayaran['tanggal_bayar']))
            : '-';
        $jumlahBayar     = 'Rp ' . number_format((float) ($pembayaran['jumlah_bayar'] ?? 0), 0, ',', '.');
        $nominalTerbayar = (float) ($pembayaran['nominal_terbayar'] ?? 0);
        $nominalTagihan  = (float) ($pembayaran['nominal_tagihan'] ?? 0);
        $sisaTagihan     = max($nominalTagihan - $nominalTerbayar, 0);
        $sisaText        = 'Rp ' . number_format($sisaTagihan, 0, ',', '.');
        $metode          = strtoupper((string) ($pembayaran['metode_pembayaran'] ?? '-'));
        $kodePembayaran  = trim((string) ($pembayaran['kode_pembayaran'] ?? '-'));

        $lines = [
            'Assalamu\'alaikum / Halo ' . $namaOrangTua . ',',
            '',
            'Kami informasikan bahwa pembayaran SPP telah diterima dengan rincian:',
            '',
            'Nama Siswa: ' . $namaSiswa,
            'NIS: ' . $nis,
            'Kelas: ' . $kelas,
            'Periode: ' . $periode,
            'Tanggal Bayar: ' . $tanggalBayar,
            'Nominal Bayar: ' . $jumlahBayar,
            'Metode: ' . $metode,
            'Kode Pembayaran: ' . $kodePembayaran,
            'Sisa Tagihan: ' . $sisaText,
            '',
            'Terima kasih.',
        ];

        return implode("\n", $lines);
    }

    private function hasWhatsappNotificationColumns($db): bool
    {
        return $db->fieldExists('wa_notif_status', 'pembayaran_spp')
            && $db->fieldExists('wa_notif_opened_at', 'pembayaran_spp')
            && $db->fieldExists('wa_notif_sent_at', 'pembayaran_spp')
            && $db->fieldExists('wa_notif_sent_by_user_id', 'pembayaran_spp')
            && $db->fieldExists('wa_notif_phone', 'pembayaran_spp');
    }

    private function bulanMap(): array
    {
        return [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function formatPeriode(int $bulan, int $tahun): string
    {
        $bulanMap = $this->bulanMap();
        return ($bulanMap[$bulan] ?? (string) $bulan) . ' ' . $tahun;
    }

    private function tableExists($db, string $table): bool
    {
        return in_array($table, $db->listTables(), true);
    }

    private function generateKodePembayaran(): string
    {
        return 'SPP-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public function deletePayment($id)
    {
        helper('auth');

        $db     = Database::connect();
        $userId = user_id();
        $id     = (int) $id;
        $now    = date('Y-m-d H:i:s');

        $pembayaran = $db->table('pembayaran_spp ps')
            ->select(
                'ps.*,
            ts.id as tagihan_spp_id_ref,
            ts.siswa_id,
            ts.kelas_tahun_ajaran_id,
            ts.tahun_ajaran_id,
            ts.bulan,
            ts.tahun,
            ts.nominal_tagihan,
            kta.wali_kelas_user_id'
            )
            ->join('tagihan_spp ts', 'ts.id = ps.tagihan_spp_id')
            ->join('kelas_tahun_ajaran kta', 'kta.id = ts.kelas_tahun_ajaran_id')
            ->where('ps.id', $id)
            ->where('kta.wali_kelas_user_id', $userId)
            ->get()
            ->getRowArray();

        if (! $pembayaran) {
            return redirect()->to(site_url('guru/spp'))
                ->with('error', 'Transaksi pembayaran tidak ditemukan.');
        }

        $redirectUrl = site_url('guru/spp?' . http_build_query([
            'bulan' => $pembayaran['bulan'],
            'tahun' => $pembayaran['tahun'],
            'kelas_tahun_ajaran_id' => $pembayaran['kelas_tahun_ajaran_id'],
        ]));

        $db->transStart();

        // Hapus transaksi pembayaran
        $db->table('pembayaran_spp')
            ->where('id', $pembayaran['id'])
            ->delete();

        // Hitung ulang total pembayaran pada tagihan terkait
        $sumPembayaran = $db->table('pembayaran_spp')
            ->select('COALESCE(SUM(jumlah_bayar), 0) as total')
            ->where('tagihan_spp_id', $pembayaran['tagihan_spp_id_ref'])
            ->get()
            ->getRowArray();

        $totalTerbayarBaru = (float) ($sumPembayaran['total'] ?? 0);
        $nominalTagihan    = (float) ($pembayaran['nominal_tagihan'] ?? 0);

        if ($totalTerbayarBaru <= 0) {
            $statusPembayaran = 'belum_bayar';
        } elseif ($totalTerbayarBaru < $nominalTagihan) {
            $statusPembayaran = 'sebagian';
        } else {
            $statusPembayaran = 'lunas';
        }

        $db->table('tagihan_spp')
            ->where('id', $pembayaran['tagihan_spp_id_ref'])
            ->update([
                'nominal_terbayar'  => $totalTerbayarBaru,
                'status_pembayaran' => $statusPembayaran,
                'updated_at'        => $now,
            ]);

        // Hitung ulang saldo SPP bulanan
        $saldoBulanan = $db->table('saldo_spp_bulanan')
            ->where('tahun_ajaran_id', $pembayaran['tahun_ajaran_id'])
            ->where('bulan', $pembayaran['bulan'])
            ->where('tahun', $pembayaran['tahun'])
            ->get()
            ->getRowArray();

        if ($saldoBulanan) {
            $sumPeriode = $db->table('pembayaran_spp ps')
                ->select('COALESCE(SUM(ps.jumlah_bayar), 0) as total')
                ->join('tagihan_spp ts', 'ts.id = ps.tagihan_spp_id')
                ->where('ts.tahun_ajaran_id', $pembayaran['tahun_ajaran_id'])
                ->where('ts.bulan', $pembayaran['bulan'])
                ->where('ts.tahun', $pembayaran['tahun'])
                ->get()
                ->getRowArray();

            $totalMasukBaru = (float) ($sumPeriode['total'] ?? 0);
            $saldoAwal      = (float) ($saldoBulanan['saldo_awal'] ?? 0);
            $totalKeluar    = (float) ($saldoBulanan['total_keluar'] ?? 0);
            $saldoAkhirBaru = $saldoAwal + $totalMasukBaru - $totalKeluar;

            $db->table('saldo_spp_bulanan')
                ->where('id', $saldoBulanan['id'])
                ->update([
                    'total_masuk' => $totalMasukBaru,
                    'saldo_akhir' => $saldoAkhirBaru,
                    'updated_at'  => $now,
                ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to($redirectUrl)
                ->with('error', 'Gagal menghapus transaksi pembayaran.');
        }

        return redirect()->to($redirectUrl)
            ->with('success', 'Transaksi pembayaran berhasil dihapus dan status tagihan sudah dikembalikan.');
    }
}
