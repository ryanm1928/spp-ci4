<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use DateInterval;
use DatePeriod;
use DateTime;

class KepalaSekolahController extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        $tahunAjaranList = $db->table('tahun_ajaran')
            ->where('deleted_at', null)
            ->orderBy('tanggal_mulai', 'DESC')
            ->get()
            ->getResult();

        $activeYear = $db->table('tahun_ajaran')
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->get()
            ->getRow();

        $selectedYearId = (int) ($this->request->getGet('tahun_ajaran_id') ?? 0);
        if ($selectedYearId <= 0 && $activeYear) {
            $selectedYearId = (int) $activeYear->id;
        }

        $selectedYear = null;
        if ($selectedYearId > 0) {
            $selectedYear = $db->table('tahun_ajaran')
                ->where('id', $selectedYearId)
                ->where('deleted_at', null)
                ->get()
                ->getRow();
        }

        if (! $selectedYear && $activeYear) {
            $selectedYear = $activeYear;
            $selectedYearId = (int) $activeYear->id;
        }

        $kelasBuilder = $db->table('kelas_tahun_ajaran kta')
            ->select('kta.id, k.nama_kelas')
            ->join('kelas k', 'k.id = kta.kelas_id', 'left')
            ->where('kta.deleted_at', null)
            ->where('k.deleted_at', null)
            ->orderBy('k.nama_kelas', 'ASC');

        if ($selectedYearId > 0) {
            $kelasBuilder->where('kta.tahun_ajaran_id', $selectedYearId);
        }

        $kelasList = $kelasBuilder->get()->getResult();

        $selectedClassId = (int) ($this->request->getGet('kelas_tahun_ajaran_id') ?? 0);

        if ($selectedClassId > 0) {
            $validClassBuilder = $db->table('kelas_tahun_ajaran')
                ->where('id', $selectedClassId)
                ->where('deleted_at', null);

            if ($selectedYearId > 0) {
                $validClassBuilder->where('tahun_ajaran_id', $selectedYearId);
            }

            $validClass = $validClassBuilder->get()->getRow();
            if (! $validClass) {
                $selectedClassId = 0;
            }
        }

        $monthOptions = $this->buildMonthOptions($selectedYear);

        $selectedPeriod = (string) ($this->request->getGet('periode_bulan') ?? '');
        if (! isset($monthOptions[$selectedPeriod])) {
            $selectedPeriod = $this->getDefaultPeriod($selectedYear, $monthOptions);
        }

        $selectedMonth = 0;
        $selectedMonthYear = 0;

        if ($selectedPeriod) {
            [$selectedMonthYear, $selectedMonth] = array_map('intval', explode('-', $selectedPeriod));
        }

        // Card 1: jumlah siswa aktif
        $jumlahSiswaBuilder = $db->table('siswa s')
            ->select('COUNT(DISTINCT s.id) AS total')
            ->join('kelas_tahun_ajaran kta', 'kta.id = s.kelas_tahun_ajaran_id', 'left')
            ->where('s.status_aktif', 1)
            ->where('s.deleted_at', null);

        if ($selectedYearId > 0) {
            $jumlahSiswaBuilder->where('kta.tahun_ajaran_id', $selectedYearId);
        }

        if ($selectedClassId > 0) {
            $jumlahSiswaBuilder->where('s.kelas_tahun_ajaran_id', $selectedClassId);
        }

        $jumlahSiswa = (int) ($jumlahSiswaBuilder->get()->getRow()->total ?? 0);

        // Card 2: total pembayaran bulan tagihan terpilih
        $totalPembayaranBuilder = $db->table('tagihan_spp ts')
            ->select('COALESCE(SUM(ts.nominal_terbayar), 0) AS total')
            ->where('ts.tahun_ajaran_id', $selectedYearId)
            ->where('ts.bulan', $selectedMonth)
            ->where('ts.tahun', $selectedMonthYear);

        if ($selectedClassId > 0) {
            $totalPembayaranBuilder->where('ts.kelas_tahun_ajaran_id', $selectedClassId);
        }

        $totalPembayaran = (float) ($totalPembayaranBuilder->get()->getRow()->total ?? 0);

        // Card 3: siswa yang sudah bayar bulan ini (minimal ada pembayaran)
        $sudahBayarBuilder = $db->table('tagihan_spp ts')
            ->select('COUNT(DISTINCT ts.siswa_id) AS total')
            ->where('ts.tahun_ajaran_id', $selectedYearId)
            ->where('ts.bulan', $selectedMonth)
            ->where('ts.tahun', $selectedMonthYear)
            ->where('ts.nominal_terbayar >', 0);

        if ($selectedClassId > 0) {
            $sudahBayarBuilder->where('ts.kelas_tahun_ajaran_id', $selectedClassId);
        }

        $jumlahSiswaSudahBayar = (int) ($sudahBayarBuilder->get()->getRow()->total ?? 0);

        // Card 4: siswa belum bayar bulan ini
        $jumlahSiswaBelumBayar = max($jumlahSiswa - $jumlahSiswaSudahBayar, 0);

        // Grafik pemasukan per bulan dalam tahun ajaran
        [$chartLabels, $chartData] = $this->getChartData($db, $selectedYearId, $selectedClassId, $monthOptions);

        // Detail siswa per bulan terpilih
        $tagihanJoin = sprintf(
            'ts.siswa_id = s.id AND ts.kelas_tahun_ajaran_id = s.kelas_tahun_ajaran_id AND ts.tahun_ajaran_id = %d AND ts.bulan = %d AND ts.tahun = %d',
            (int) $selectedYearId,
            (int) $selectedMonth,
            (int) $selectedMonthYear
        );

        $detailSiswaBuilder = $db->table('siswa s')
            ->select("
                s.id,
                s.nis,
                s.nama_siswa,
                k.nama_kelas,
                COALESCE(ts.nominal_tagihan, 0) AS nominal_tagihan,
                COALESCE(ts.nominal_terbayar, 0) AS nominal_terbayar,
                ts.status_pembayaran,
                ts.tanggal_jatuh_tempo
            ", false)
            ->join('kelas_tahun_ajaran kta', 'kta.id = s.kelas_tahun_ajaran_id', 'left')
            ->join('kelas k', 'k.id = kta.kelas_id', 'left')
            ->join('tagihan_spp ts', $tagihanJoin, 'left', false)
            ->where('s.status_aktif', 1)
            ->where('s.deleted_at', null);

        if ($selectedYearId > 0) {
            $detailSiswaBuilder->where('kta.tahun_ajaran_id', $selectedYearId);
        }

        if ($selectedClassId > 0) {
            $detailSiswaBuilder->where('s.kelas_tahun_ajaran_id', $selectedClassId);
        }

        $detailSiswa = $detailSiswaBuilder
            ->orderBy('s.nama_siswa', 'ASC')
            ->get()
            ->getResult();

        return view('kepsek/dashboard', [
            'title'                   => 'Dashboard',
            'menu'                    => 'dashboard',
            'tahunAjaranList'         => $tahunAjaranList,
            'kelasList'               => $kelasList,
            'selectedYear'            => $selectedYear,
            'selectedYearId'          => $selectedYearId,
            'selectedClassId'         => $selectedClassId,
            'monthOptions'            => $monthOptions,
            'selectedPeriod'          => $selectedPeriod,
            'selectedMonth'           => $selectedMonth,
            'selectedMonthYear'       => $selectedMonthYear,
            'jumlahSiswa'             => $jumlahSiswa,
            'totalPembayaran'         => $totalPembayaran,
            'jumlahSiswaSudahBayar'   => $jumlahSiswaSudahBayar,
            'jumlahSiswaBelumBayar'   => $jumlahSiswaBelumBayar,
            'chartLabels'             => $chartLabels,
            'chartData'               => $chartData,
            'detailSiswa'             => $detailSiswa,
        ]);
    }

    private function buildMonthOptions($selectedYear): array
    {
        if (! $selectedYear) {
            return [];
        }

        $bulanNama = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $start = new DateTime(date('Y-m-01', strtotime($selectedYear->tanggal_mulai)));
        $end   = new DateTime(date('Y-m-01', strtotime($selectedYear->tanggal_selesai)));
        $end->modify('+1 month');

        $period = new DatePeriod($start, new DateInterval('P1M'), $end);

        $options = [];
        foreach ($period as $month) {
            $key = $month->format('Y-m');
            $options[$key] = $bulanNama[(int) $month->format('n')] . ' ' . $month->format('Y');
        }

        return $options;
    }

    private function getDefaultPeriod($selectedYear, array $monthOptions): string
    {
        if (! $selectedYear || empty($monthOptions)) {
            return '';
        }

        $now = date('Y-m');
        if (isset($monthOptions[$now])) {
            return $now;
        }

        return array_key_first($monthOptions);
    }

    private function getChartData($db, int $selectedYearId, int $selectedClassId, array $monthOptions): array
    {
        $chartMap = [];

        foreach ($monthOptions as $key => $label) {
            $chartMap[$key] = [
                'label' => $label,
                'total' => 0,
            ];
        }

        if ($selectedYearId <= 0) {
            return [array_column($chartMap, 'label'), array_column($chartMap, 'total')];
        }

        $rowsBuilder = $db->table('tagihan_spp ts')
            ->select("CONCAT(ts.tahun, '-', LPAD(ts.bulan, 2, '0')) AS periode, COALESCE(SUM(ts.nominal_terbayar), 0) AS total", false)
            ->where('ts.tahun_ajaran_id', $selectedYearId)
            ->groupBy("ts.tahun, ts.bulan", false)
            ->orderBy('ts.tahun', 'ASC')
            ->orderBy('ts.bulan', 'ASC');

        if ($selectedClassId > 0) {
            $rowsBuilder->where('ts.kelas_tahun_ajaran_id', $selectedClassId);
        }

        $rows = $rowsBuilder->get()->getResultArray();

        foreach ($rows as $row) {
            if (isset($chartMap[$row['periode']])) {
                $chartMap[$row['periode']]['total'] = (float) $row['total'];
            }
        }

        return [
            array_column($chartMap, 'label'),
            array_column($chartMap, 'total'),
        ];
    }
}
