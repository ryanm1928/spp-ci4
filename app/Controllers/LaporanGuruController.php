<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use DateTime;

class LaporanGuruController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $filters = $this->getFilters();

        if (empty($filters['tahun_ajaran_id'])) {
            $filters['tahun_ajaran_id'] = $this->getActiveTahunAjaranId();
        }

        $periodeOptions = $this->getPeriodeOptions($filters['tahun_ajaran_id']);
        $viewMode       = empty($filters['periode']) ? 'rekap' : 'detail';

        if ($viewMode === 'rekap') {
            $rows    = $this->getLaporanRekapData($filters, $periodeOptions);
            $summary = $this->buildRekapSummary($rows, $periodeOptions);
        } else {
            $rows    = $this->getLaporanDetailData($filters);
            $summary = $this->buildDetailSummary($rows);
        }

        return view('guru/laporan/index', [
            'title'               => 'Laporan SPP Siswa',
            'filters'             => $filters,
            'tahunAjaranOptions'  => $this->getTahunAjaranOptions(),
            'periodeOptions'      => $periodeOptions,
            'rows'                => $rows,
            'summary'             => $summary,
            'viewMode'            => $viewMode,
            'selectedTahunAjaran' => $this->getTahunAjaranById($filters['tahun_ajaran_id']),
            'menu'              => 'laporan',
        ]);
    }

    public function export()
    {
        $filters = $this->getFilters();

        if (empty($filters['tahun_ajaran_id'])) {
            $filters['tahun_ajaran_id'] = $this->getActiveTahunAjaranId();
        }

        $periodeOptions = $this->getPeriodeOptions($filters['tahun_ajaran_id']);
        $tahunAjaran    = $this->getTahunAjaranById($filters['tahun_ajaran_id']);
        $viewMode       = empty($filters['periode']) ? 'rekap' : 'detail';

        if ($viewMode === 'rekap') {
            $rows    = $this->getLaporanRekapData($filters, $periodeOptions);
            $summary = $this->buildRekapSummary($rows, $periodeOptions);
        } else {
            $rows    = $this->getLaporanDetailData($filters);
            $summary = $this->buildDetailSummary($rows);
        }

        $periodeLabel = 'Semua Periode';
        if (!empty($filters['periode']) && isset($periodeOptions[$filters['periode']])) {
            $periodeLabel = $periodeOptions[$filters['periode']];
        }

        $namaTA = $tahunAjaran['nama_tahun_ajaran'] ?? 'semua_tahun_ajaran';
        $prefix = $viewMode === 'rekap' ? 'rekap_spp_per_siswa_' : 'detail_pembayaran_spp_';

        $filename = $prefix .
            str_replace(['/', ' '], ['-', '_'], $namaTA) . '_' .
            str_replace([' ', '/'], ['_', '-'], $periodeLabel) . '.xls';

        $html = view('guru/laporan/export_excel', [
            'rows'                => $rows,
            'filters'             => $filters,
            'summary'             => $summary,
            'viewMode'            => $viewMode,
            'periodeOptions'      => $periodeOptions,
            'selectedTahunAjaran' => $tahunAjaran,
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($html);
    }

    private function getFilters(): array
    {
        return [
            'tahun_ajaran_id' => $this->request->getGet('tahun_ajaran_id'),
            'periode'         => $this->request->getGet('periode'),
            'status'          => $this->request->getGet('status'),
        ];
    }

    private function getLaporanDetailData(array $filters): array
    {
        $selectedYearId = !empty($filters['tahun_ajaran_id']) ? (int) $filters['tahun_ajaran_id'] : 0;
        $selectedMonth  = 0;
        $selectedYear   = 0;

        if (!empty($filters['periode'])) {
            $periode = explode('-', $filters['periode']);
            if (count($periode) === 2) {
                $selectedYear  = (int) $periode[0];
                $selectedMonth = (int) $periode[1];
            }
        }

        $tagihanJoin = 't.siswa_id = s.id AND t.kelas_tahun_ajaran_id = s.kelas_tahun_ajaran_id';

        if ($selectedYearId > 0) {
            $tagihanJoin .= ' AND t.tahun_ajaran_id = ' . $selectedYearId;
        }

        if ($selectedYear > 0 && $selectedMonth > 0) {
            $tagihanJoin .= ' AND t.tahun = ' . $selectedYear;
            $tagihanJoin .= ' AND t.bulan = ' . $selectedMonth;
        }

        $selectedBulanSql = $selectedMonth > 0 ? (string) $selectedMonth : 't.bulan';
        $selectedTahunSql = $selectedYear > 0 ? (string) $selectedYear : 't.tahun';

        $builder = $this->db->table('siswa s');
        $builder->select("
            COALESCE(t.id, 0) AS id,
            {$selectedBulanSql} AS bulan,
            {$selectedTahunSql} AS tahun,
            COALESCE(t.nominal_tagihan, ta.nominal_spp, 0) AS nominal_tagihan,
            COALESCE(t.nominal_terbayar, 0) AS nominal_terbayar,
            CASE
                WHEN t.id IS NULL THEN 'belum_bayar'
                ELSE t.status_pembayaran
            END AS status_pembayaran,
            t.tanggal_jatuh_tempo,

            s.id AS siswa_id,
            s.nis,
            s.nama_siswa,
            s.nama_orang_tua,

            k.id AS kelas_id,
            k.nama_kelas,

            ta.id AS tahun_ajaran_id,
            ta.nama_tahun_ajaran,

            MAX(CASE
                WHEN p.status_pembayaran_record = 'active' THEN p.tanggal_bayar
                ELSE NULL
            END) AS tanggal_bayar_terakhir,

            SUM(CASE
                WHEN p.status_pembayaran_record = 'active' THEN p.jumlah_bayar
                ELSE 0
            END) AS total_pembayaran_aktif
        ", false);

        $builder->join('kelas_tahun_ajaran kta', 'kta.id = s.kelas_tahun_ajaran_id');
        $builder->join('kelas k', 'k.id = kta.kelas_id');
        $builder->join('tahun_ajaran ta', 'ta.id = kta.tahun_ajaran_id');
        $builder->join('tagihan_spp t', $tagihanJoin, 'left', false);
        $builder->join('pembayaran_spp p', 'p.tagihan_spp_id = t.id', 'left');

        $builder->where('s.deleted_at IS NULL', null, false);
        $builder->where('kta.deleted_at IS NULL', null, false);
        $builder->where('k.deleted_at IS NULL', null, false);
        $builder->where('ta.deleted_at IS NULL', null, false);
        $builder->where('kta.wali_kelas_user_id', user_id());

        if ($selectedYearId > 0) {
            $builder->where('kta.tahun_ajaran_id', $selectedYearId);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'sudah_bayar') {
                $builder->where('t.status_pembayaran', 'lunas');
            } elseif ($filters['status'] === 'sebagian') {
                $builder->where('t.status_pembayaran', 'sebagian');
            } elseif ($filters['status'] === 'belum_bayar') {
                $builder->groupStart();
                $builder->where('t.status_pembayaran', 'belum_bayar');
                $builder->orWhere('t.id IS NULL', null, false);
                $builder->groupEnd();
            }
        }

        $builder->groupBy([
            't.id',
            't.tanggal_jatuh_tempo',
            't.status_pembayaran',
            't.nominal_tagihan',
            't.nominal_terbayar',
            's.id',
            's.nis',
            's.nama_siswa',
            's.nama_orang_tua',
            'k.id',
            'k.nama_kelas',
            'ta.id',
            'ta.nama_tahun_ajaran',
        ]);

        $builder->orderBy('k.nama_kelas', 'ASC');
        $builder->orderBy('s.nama_siswa', 'ASC');

        return $builder->get()->getResultArray();
    }

    private function getLaporanRekapData(array $filters, array $periodeOptions): array
    {
        $students = $this->getSiswaLaporanBase($filters);
        if (empty($students)) {
            return [];
        }

        $studentIds = array_map('intval', array_column($students, 'siswa_id'));
        $matchedStudentIds = $this->getMatchedStudentIdsForStatusFilter($filters, $studentIds);

        if ($matchedStudentIds !== null) {
            if (empty($matchedStudentIds)) {
                return [];
            }

            $students = array_values(array_filter($students, static function ($student) use ($matchedStudentIds) {
                return in_array((int) $student['siswa_id'], $matchedStudentIds, true);
            }));

            $studentIds = array_map('intval', array_column($students, 'siswa_id'));
        }

        $rowsByStudent = [];
        foreach ($students as $student) {
            $student['periode_statuses'] = [];

            foreach ($periodeOptions as $periodeKey => $periodeLabel) {
                $student['periode_statuses'][$periodeKey] = [
                    'status' => 'tidak_ada',
                    'symbol' => 'X',
                    'label'  => $periodeLabel,
                ];
            }

            $rowsByStudent[(int) $student['siswa_id']] = $student;
        }

        $tagihanRows = $this->getTagihanRekapRows($filters, $studentIds);
        foreach ($tagihanRows as $tagihan) {
            $siswaId = (int) $tagihan['siswa_id'];
            $periodeKey = $tagihan['tahun'] . '-' . str_pad((string) $tagihan['bulan'], 2, '0', STR_PAD_LEFT);

            if (!isset($rowsByStudent[$siswaId])) {
                continue;
            }

            $rowsByStudent[$siswaId]['periode_statuses'][$periodeKey] = [
                'status' => $tagihan['status_pembayaran'],
                'symbol' => $this->getStatusSymbol($tagihan['status_pembayaran']),
                'label'  => $periodeOptions[$periodeKey] ?? $periodeKey,
            ];
        }

        return array_values($rowsByStudent);
    }

    private function getSiswaLaporanBase(array $filters): array
    {
        $builder = $this->db->table('siswa s');
        $builder->select('
            s.id AS siswa_id,
            s.nis,
            s.nama_siswa,
            s.nama_orang_tua,
            k.id AS kelas_id,
            k.nama_kelas,
            ta.id AS tahun_ajaran_id,
            ta.nama_tahun_ajaran
        ');

        $builder->join('kelas_tahun_ajaran kta', 'kta.id = s.kelas_tahun_ajaran_id');
        $builder->join('kelas k', 'k.id = kta.kelas_id');
        $builder->join('tahun_ajaran ta', 'ta.id = kta.tahun_ajaran_id');

        $builder->where('s.deleted_at IS NULL', null, false);
        $builder->where('kta.deleted_at IS NULL', null, false);
        $builder->where('k.deleted_at IS NULL', null, false);
        $builder->where('ta.deleted_at IS NULL', null, false);
        $builder->where('kta.wali_kelas_user_id', user_id());

        if (!empty($filters['tahun_ajaran_id'])) {
            $builder->where('kta.tahun_ajaran_id', (int) $filters['tahun_ajaran_id']);
        }

        $builder->orderBy('k.nama_kelas', 'ASC');
        $builder->orderBy('s.nama_siswa', 'ASC');

        return $builder->get()->getResultArray();
    }

    private function getMatchedStudentIdsForStatusFilter(array $filters, array $studentIds): ?array
    {
        if (empty($filters['status']) || empty($studentIds)) {
            return null;
        }

        $builder = $this->db->table('tagihan_spp t');
        $builder->distinct();
        $builder->select('t.siswa_id');
        $builder->whereIn('t.siswa_id', $studentIds);

        if (!empty($filters['tahun_ajaran_id'])) {
            $builder->where('t.tahun_ajaran_id', (int) $filters['tahun_ajaran_id']);
        }

        if ($filters['status'] === 'sudah_bayar') {
            $builder->where('t.status_pembayaran', 'lunas');
        } elseif (in_array($filters['status'], ['belum_bayar', 'sebagian'], true)) {
            $builder->where('t.status_pembayaran', $filters['status']);
        }

        $result = $builder->get()->getResultArray();

        return array_map('intval', array_column($result, 'siswa_id'));
    }

    private function getTagihanRekapRows(array $filters, array $studentIds): array
    {
        if (empty($studentIds)) {
            return [];
        }

        $builder = $this->db->table('tagihan_spp t');
        $builder->select('t.siswa_id, t.bulan, t.tahun, t.status_pembayaran');
        $builder->whereIn('t.siswa_id', $studentIds);

        if (!empty($filters['tahun_ajaran_id'])) {
            $builder->where('t.tahun_ajaran_id', (int) $filters['tahun_ajaran_id']);
        }

        $builder->orderBy('t.tahun', 'ASC');
        $builder->orderBy('t.bulan', 'ASC');

        return $builder->get()->getResultArray();
    }

    private function buildDetailSummary(array $rows): array
    {
        $summary = [
            'total_data'      => 0,
            'sudah_bayar'     => 0,
            'belum_bayar'     => 0,
            'sebagian'        => 0,
            'total_tagihan'   => 0,
            'total_terbayar'  => 0,
            'total_tunggakan' => 0,
        ];

        foreach ($rows as $row) {
            $tagihan  = (float) $row['nominal_tagihan'];
            $terbayar = (float) $row['nominal_terbayar'];
            $sisa     = $tagihan - $terbayar;
            $status   = $row['status_pembayaran'];

            $summary['total_data']++;
            $summary['total_tagihan'] += $tagihan;
            $summary['total_terbayar'] += $terbayar;
            $summary['total_tunggakan'] += $sisa;

            if ($status === 'lunas') {
                $summary['sudah_bayar']++;
            } elseif ($status === 'belum_bayar') {
                $summary['belum_bayar']++;
            } elseif ($status === 'sebagian') {
                $summary['sebagian']++;
            }
        }

        return $summary;
    }

    private function buildRekapSummary(array $rows, array $periodeOptions): array
    {
        $summary = [
            'total_siswa'    => count($rows),
            'total_periode'  => count($periodeOptions),
            'sudah_bayar'    => 0,
            'belum_bayar'    => 0,
            'sebagian'       => 0,
            'tidak_ada_data' => 0,
        ];

        foreach ($rows as $row) {
            foreach ($periodeOptions as $periodeKey => $periodeLabel) {
                $status = $row['periode_statuses'][$periodeKey]['status'] ?? 'tidak_ada';

                if ($status === 'lunas') {
                    $summary['sudah_bayar']++;
                } elseif ($status === 'belum_bayar') {
                    $summary['belum_bayar']++;
                } elseif ($status === 'sebagian') {
                    $summary['sebagian']++;
                } else {
                    $summary['tidak_ada_data']++;
                }
            }
        }

        return $summary;
    }

    private function getStatusSymbol(string $status): string
    {
        return $status === 'lunas' ? '✓' : 'X';
    }

    private function getActiveTahunAjaranId(): ?int
    {
        $row = $this->db->table('tahun_ajaran')
            ->select('id')
            ->where('is_active', 1)
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        return $row ? (int) $row['id'] : null;
    }

    private function getTahunAjaranOptions(): array
    {
        return $this->db->table('tahun_ajaran')
            ->select('id, nama_tahun_ajaran, tanggal_mulai, tanggal_selesai, is_active')
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function getTahunAjaranById($id): ?array
    {
        if (empty($id)) {
            return null;
        }

        $row = $this->db->table('tahun_ajaran')
            ->select('id, nama_tahun_ajaran, tanggal_mulai, tanggal_selesai, is_active')
            ->where('id', (int) $id)
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    private function getPeriodeOptions($tahunAjaranId): array
    {
        $tahunAjaran = $this->getTahunAjaranById($tahunAjaranId);
        if (!$tahunAjaran) {
            return [];
        }

        $start = new DateTime($tahunAjaran['tanggal_mulai']);
        $end   = new DateTime($tahunAjaran['tanggal_selesai']);
        $end->modify('first day of next month');

        $options = [];
        $bulanNama = [
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

        $current = clone $start;
        $current->modify('first day of this month');

        while ($current < $end) {
            $tahun = $current->format('Y');
            $bulan = (int) $current->format('n');
            $key   = $tahun . '-' . str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

            $options[$key] = $bulanNama[$bulan] . ' ' . $tahun;
            $current->modify('+1 month');
        }

        return $options;
    }

    private function getKelasOptions($tahunAjaranId = null): array
    {
        $builder = $this->db->table('kelas_tahun_ajaran kta');
        $builder->select('k.id, k.nama_kelas');
        $builder->join('kelas k', 'k.id = kta.kelas_id');
        $builder->where('kta.deleted_at IS NULL', null, false);
        $builder->where('k.deleted_at IS NULL', null, false);
        $builder->where('kta.wali_kelas_user_id', user_id());

        if (!empty($tahunAjaranId)) {
            $builder->where('kta.tahun_ajaran_id', (int) $tahunAjaranId);
        }

        $builder->groupBy(['k.id', 'k.nama_kelas']);
        $builder->orderBy('k.nama_kelas', 'ASC');

        return $builder->get()->getResultArray();
    }
}
