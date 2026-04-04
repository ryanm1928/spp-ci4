<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use DateInterval;
use DatePeriod;
use DateTime;

class GuruController extends BaseController
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

        $auth = service('authentication');
        $currentUserId = (int) ($auth->id() ?? 0);

        $assignedClass = null;
        if ($currentUserId > 0 && $selectedYearId > 0) {
            $assignedClass = $db->table('kelas_tahun_ajaran kta')
                ->select('kta.id, k.nama_kelas, kta.tahun_ajaran_id')
                ->join('kelas k', 'k.id = kta.kelas_id', 'left')
                ->where('kta.wali_kelas_user_id', $currentUserId)
                ->where('kta.tahun_ajaran_id', $selectedYearId)
                ->where('kta.deleted_at', null)
                ->where('k.deleted_at', null)
                ->orderBy('k.nama_kelas', 'ASC')
                ->get()
                ->getRow();
        }

        $selectedClassId = (int) ($assignedClass->id ?? 0);
        $selectedClassName = $assignedClass->nama_kelas ?? '-';
        $hasAssignedClass = $selectedClassId > 0;

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

        $jumlahSiswa = 0;
        $totalPembayaran = 0;
        $jumlahSiswaSudahBayar = 0;
        $jumlahSiswaBelumBayar = 0;
        $detailSiswa = [];

        if ($hasAssignedClass) {
            $jumlahSiswaBuilder = $db->table('siswa s')
                ->select('COUNT(DISTINCT s.id) AS total')
                ->join('kelas_tahun_ajaran kta', 'kta.id = s.kelas_tahun_ajaran_id', 'left')
                ->where('s.status_aktif', 1)
                ->where('s.deleted_at', null)
                ->where('kta.tahun_ajaran_id', $selectedYearId)
                ->where('s.kelas_tahun_ajaran_id', $selectedClassId);

            $jumlahSiswa = (int) ($jumlahSiswaBuilder->get()->getRow()->total ?? 0);

            $totalPembayaranBuilder = $db->table('tagihan_spp ts')
                ->select('COALESCE(SUM(ts.nominal_terbayar), 0) AS total')
                ->where('ts.tahun_ajaran_id', $selectedYearId)
                ->where('ts.kelas_tahun_ajaran_id', $selectedClassId)
                ->where('ts.bulan', $selectedMonth)
                ->where('ts.tahun', $selectedMonthYear);

            $totalPembayaran = (float) ($totalPembayaranBuilder->get()->getRow()->total ?? 0);

            $sudahBayarBuilder = $db->table('tagihan_spp ts')
                ->select('COUNT(DISTINCT ts.siswa_id) AS total')
                ->where('ts.tahun_ajaran_id', $selectedYearId)
                ->where('ts.kelas_tahun_ajaran_id', $selectedClassId)
                ->where('ts.bulan', $selectedMonth)
                ->where('ts.tahun', $selectedMonthYear)
                ->where('ts.nominal_terbayar >', 0);

            $jumlahSiswaSudahBayar = (int) ($sudahBayarBuilder->get()->getRow()->total ?? 0);
            $jumlahSiswaBelumBayar = max($jumlahSiswa - $jumlahSiswaSudahBayar, 0);

            $tagihanJoin = sprintf(
                'ts.siswa_id = s.id AND ts.kelas_tahun_ajaran_id = s.kelas_tahun_ajaran_id AND ts.tahun_ajaran_id = %d AND ts.bulan = %d AND ts.tahun = %d',
                (int) $selectedYearId,
                (int) $selectedMonth,
                (int) $selectedMonthYear
            );

            $detailSiswaBuilder = $db->table('siswa s')
                ->select("\n                    s.id,\n                    s.nis,\n                    s.nama_siswa,\n                    k.nama_kelas,\n                    COALESCE(ts.nominal_tagihan, 0) AS nominal_tagihan,\n                    COALESCE(ts.nominal_terbayar, 0) AS nominal_terbayar,\n                    ts.status_pembayaran,\n                    ts.tanggal_jatuh_tempo\n                ", false)
                ->join('kelas_tahun_ajaran kta', 'kta.id = s.kelas_tahun_ajaran_id', 'left')
                ->join('kelas k', 'k.id = kta.kelas_id', 'left')
                ->join('tagihan_spp ts', $tagihanJoin, 'left', false)
                ->where('s.status_aktif', 1)
                ->where('s.deleted_at', null)
                ->where('kta.tahun_ajaran_id', $selectedYearId)
                ->where('s.kelas_tahun_ajaran_id', $selectedClassId);

            $detailSiswa = $detailSiswaBuilder
                ->orderBy('s.nama_siswa', 'ASC')
                ->get()
                ->getResult();
        }

        [$chartLabels, $chartData] = $this->getChartData($db, $selectedYearId, $selectedClassId, $monthOptions);

        return view('guru/dashboard', [
            'title'                 => 'Dashboard',
            'menu'                  => 'dashboard',
            'tahunAjaranList'       => $tahunAjaranList,
            'selectedYear'          => $selectedYear,
            'selectedYearId'        => $selectedYearId,
            'selectedClassId'       => $selectedClassId,
            'selectedClassName'     => $selectedClassName,
            'hasAssignedClass'      => $hasAssignedClass,
            'monthOptions'          => $monthOptions,
            'selectedPeriod'        => $selectedPeriod,
            'selectedMonth'         => $selectedMonth,
            'selectedMonthYear'     => $selectedMonthYear,
            'jumlahSiswa'           => $jumlahSiswa,
            'totalPembayaran'       => $totalPembayaran,
            'jumlahSiswaSudahBayar' => $jumlahSiswaSudahBayar,
            'jumlahSiswaBelumBayar' => $jumlahSiswaBelumBayar,
            'chartLabels'           => $chartLabels,
            'chartData'             => $chartData,
            'detailSiswa'           => $detailSiswa,
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

        if ($selectedYearId <= 0 || $selectedClassId <= 0) {
            return [array_column($chartMap, 'label'), array_column($chartMap, 'total')];
        }

        $rowsBuilder = $db->table('tagihan_spp ts')
            ->select("CONCAT(ts.tahun, '-', LPAD(ts.bulan, 2, '0')) AS periode, COALESCE(SUM(ts.nominal_terbayar), 0) AS total", false)
            ->where('ts.tahun_ajaran_id', $selectedYearId)
            ->where('ts.kelas_tahun_ajaran_id', $selectedClassId)
            ->groupBy("ts.tahun, ts.bulan", false)
            ->orderBy('ts.tahun', 'ASC')
            ->orderBy('ts.bulan', 'ASC');

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
