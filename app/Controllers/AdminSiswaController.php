<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class AdminSiswaController extends BaseController
{
    protected $db;
    protected SiswaModel $siswaModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->db = db_connect();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $q                  = trim((string) $this->request->getGet('q'));
        $status             = (string) $this->request->getGet('status');
        $kelasTahunAjaranId = (string) $this->request->getGet('kelas_tahun_ajaran_id');
        $waliKelasUserId    = (string) $this->request->getGet('wali_kelas_user_id');
        $perPage            = (int) ($this->request->getGet('per_page') ?: 10);

        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $builder = $this->siswaModel
            ->select('
                siswa.*,
                k.nama_kelas,
                ta.nama_tahun_ajaran,
                COALESCE(u.username, "-") as wali_kelas
            ')
            ->join('kelas_tahun_ajaran kta', 'kta.id = siswa.kelas_tahun_ajaran_id', 'left')
            ->join('kelas k', 'k.id = kta.kelas_id', 'left')
            ->join('tahun_ajaran ta', 'ta.id = kta.tahun_ajaran_id', 'left')
            ->join('users u', 'u.id = kta.wali_kelas_user_id', 'left')
            ->orderBy('siswa.id', 'DESC');

        if ($q !== '') {
            $builder->groupStart()
                ->like('siswa.nis', $q)
                ->orLike('siswa.nama_siswa', $q)
                ->orLike('siswa.nama_orang_tua', $q)
                ->orLike('siswa.nomor_hp_orang_tua', $q)
                ->orLike('k.nama_kelas', $q)
                ->orLike('ta.nama_tahun_ajaran', $q)
                ->orLike('u.username', $q)
                ->groupEnd();
        }

        if ($status !== '' && in_array($status, ['0', '1'], true)) {
            $builder->where('siswa.status_aktif', (int) $status);
        }

        if ($kelasTahunAjaranId !== '') {
            $builder->where('siswa.kelas_tahun_ajaran_id', (int) $kelasTahunAjaranId);
        }

        if ($waliKelasUserId !== '') {
            $builder->where('kta.wali_kelas_user_id', (int) $waliKelasUserId);
        }

        $data = [
            'title'                   => 'Data Siswa',
            'menu'                    => 'siswa',
            'siswaList'               => $builder->paginate($perPage),
            'pager'                   => $this->siswaModel->pager,
            'filters'                 => [
                'q'                     => $q,
                'status'                => $status,
                'kelas_tahun_ajaran_id' => $kelasTahunAjaranId,
                'wali_kelas_user_id'    => $waliKelasUserId,
                'per_page'              => $perPage,
            ],
            'kelasTahunAjaranOptions' => $this->getKelasTahunAjaranOptions(),
            'guruOptions'             => $this->getGuruOptions(),
        ];

        return view('admin/siswa/index', $data);
    }

    public function create()
    {
        $data = [
            'title'                   => 'Tambah Siswa',
            'menu'                    => 'siswa',
            'mode'                    => 'create',
            'siswa'                   => null,
            'kelasOptions'            => $this->getKelasOptions(),
            'tahunAjaranOptions'      => $this->getTahunAjaranOptions(),
            'guruOptions'             => $this->getGuruOptions(),
            'kelasTahunAjaranOptions' => $this->getKelasTahunAjaranOptions(),
            'selectedKelasId'         => old('kelas_id', ''),
            'selectedTahunId'         => old('tahun_ajaran_id', ''),
            'selectedWaliKelasId'     => old('wali_kelas_user_id', ''),
        ];

        return view('admin/siswa/form', $data);
    }

    public function store()
    {
        $rules = $this->getValidationRules();

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kelasId         = (int) $this->request->getPost('kelas_id');
        $tahunAjaranId   = (int) $this->request->getPost('tahun_ajaran_id');
        $waliKelasUserId = (int) $this->request->getPost('wali_kelas_user_id');

        if (! $this->isGuruUser($waliKelasUserId)) {
            return redirect()->back()->withInput()->with('error', 'Wali kelas harus dipilih dari user dengan role guru.');
        }

        $kelasTahunAjaranId = $this->resolveKelasTahunAjaranId($kelasId, $tahunAjaranId, $waliKelasUserId);

        if (! $kelasTahunAjaranId) {
            return redirect()->back()->withInput()->with('error', 'Kombinasi kelas, tahun ajaran, dan wali kelas tidak valid.');
        }

        $imagePath = $this->uploadImage($this->request->getFile('gambar_siswa'));

        $data = [
            'nis'                   => trim((string) $this->request->getPost('nis')),
            'nama_siswa'            => trim((string) $this->request->getPost('nama_siswa')),
            'jenis_kelamin'         => trim((string) $this->request->getPost('jenis_kelamin')),
            'kelas_tahun_ajaran_id' => $kelasTahunAjaranId,
            'nama_orang_tua'        => trim((string) $this->request->getPost('nama_orang_tua')),
            'nomor_hp_orang_tua'    => trim((string) $this->request->getPost('nomor_hp_orang_tua')),
            'alamat'                => trim((string) $this->request->getPost('alamat')),
            'gambar_siswa'          => $imagePath,
            'status_aktif'          => (int) ($this->request->getPost('status_aktif') ?? 0),
        ];

        $this->siswaModel->insert($data);

        return redirect()->to(site_url('admin/siswa'))->with('success', 'Data siswa berhasil disimpan.');
    }

    public function edit($id = null)
    {
        $siswa = $this->siswaModel->find($id);

        if (! $siswa) {
            throw PageNotFoundException::forPageNotFound('Data siswa tidak ditemukan.');
        }

        $ktaDetail = $this->getSelectedKtaDetail((int) ($siswa['kelas_tahun_ajaran_id'] ?? 0));

        $data = [
            'title'                   => 'Edit Siswa',
            'menu'                    => 'siswa',
            'mode'                    => 'edit',
            'siswa'                   => $siswa,
            'kelasOptions'            => $this->getKelasOptions(),
            'tahunAjaranOptions'      => $this->getTahunAjaranOptions(),
            'guruOptions'             => $this->getGuruOptions(),
            'kelasTahunAjaranOptions' => $this->getKelasTahunAjaranOptions(),
            'selectedKelasId'         => old('kelas_id', $ktaDetail['kelas_id'] ?? ''),
            'selectedTahunId'         => old('tahun_ajaran_id', $ktaDetail['tahun_ajaran_id'] ?? ''),
            'selectedWaliKelasId'     => old('wali_kelas_user_id', $ktaDetail['wali_kelas_user_id'] ?? ''),
        ];

        return view('admin/siswa/form', $data);
    }

    public function update($id = null)
    {
        $siswa = $this->siswaModel->find($id);

        if (! $siswa) {
            throw PageNotFoundException::forPageNotFound('Data siswa tidak ditemukan.');
        }

        $rules = $this->getValidationRules((int) $id);

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kelasId         = (int) $this->request->getPost('kelas_id');
        $tahunAjaranId   = (int) $this->request->getPost('tahun_ajaran_id');
        $waliKelasUserId = (int) $this->request->getPost('wali_kelas_user_id');

        if (! $this->isGuruUser($waliKelasUserId)) {
            return redirect()->back()->withInput()->with('error', 'Wali kelas harus dipilih dari user dengan role guru.');
        }

        $kelasTahunAjaranId = $this->resolveKelasTahunAjaranId($kelasId, $tahunAjaranId, $waliKelasUserId);

        if (! $kelasTahunAjaranId) {
            return redirect()->back()->withInput()->with('error', 'Kombinasi kelas, tahun ajaran, dan wali kelas tidak valid.');
        }

        $imagePath = $siswa['gambar_siswa'] ?? null;
        $newFile   = $this->request->getFile('gambar_siswa');

        if ($newFile && $newFile->isValid() && ! $newFile->hasMoved()) {
            $uploaded = $this->uploadImage($newFile);

            if ($uploaded) {
                $this->deleteImageIfExists($siswa['gambar_siswa'] ?? null);
                $imagePath = $uploaded;
            }
        }

        $data = [
            'nis'                   => trim((string) $this->request->getPost('nis')),
            'nama_siswa'            => trim((string) $this->request->getPost('nama_siswa')),
            'jenis_kelamin'         => trim((string) $this->request->getPost('jenis_kelamin')),
            'kelas_tahun_ajaran_id' => $kelasTahunAjaranId,
            'nama_orang_tua'        => trim((string) $this->request->getPost('nama_orang_tua')),
            'nomor_hp_orang_tua'    => trim((string) $this->request->getPost('nomor_hp_orang_tua')),
            'alamat'                => trim((string) $this->request->getPost('alamat')),
            'gambar_siswa'          => $imagePath,
            'status_aktif'          => (int) ($this->request->getPost('status_aktif') ?? 0),
        ];

        $this->siswaModel->update($id, $data);

        return redirect()->to(site_url('admin/siswa'))->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $siswa = $this->siswaModel->find($id);

        if (! $siswa) {
            return redirect()->to(site_url('admin/siswa'))->with('error', 'Data siswa tidak ditemukan.');
        }

        $this->deleteImageIfExists($siswa['gambar_siswa'] ?? null);
        $this->siswaModel->delete($id);

        return redirect()->to(site_url('admin/siswa'))->with('success', 'Data siswa berhasil dihapus.');
    }

    public function bulkActivate()
    {
        return $this->bulkUpdateStatus(1, 'Data siswa terpilih berhasil diaktifkan.');
    }

    public function bulkDeactivate()
    {
        return $this->bulkUpdateStatus(0, 'Data siswa terpilih berhasil dinonaktifkan.');
    }

    private function bulkUpdateStatus(int $status, string $successMessage)
    {
        $selectedIds = $this->request->getPost('selected_ids');

        if (! is_array($selectedIds) || empty($selectedIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu data siswa.');
        }

        $selectedIds = array_map('intval', $selectedIds);
        $selectedIds = array_filter($selectedIds);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Data pilihan tidak valid.');
        }

        $this->db->table('siswa')
            ->whereIn('id', $selectedIds)
            ->update([
                'status_aktif' => $status,
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to(site_url('admin/siswa'))->with('success', $successMessage);
    }

    public function bulkDelete()
    {
        $selectedIds = $this->request->getPost('selected_ids');

        if (! is_array($selectedIds) || empty($selectedIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu data untuk dihapus.');
        }

        $selectedIds = array_map('intval', $selectedIds);
        $selectedIds = array_filter($selectedIds);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Data pilihan tidak valid.');
        }

        foreach ($selectedIds as $id) {
            $siswa = $this->siswaModel->find($id);

            if ($siswa) {
                $this->deleteImageIfExists($siswa['gambar_siswa'] ?? null);
                $this->siswaModel->delete($id);
            }
        }

        return redirect()->to(site_url('admin/siswa'))->with('success', 'Data siswa terpilih berhasil dihapus.');
    }

    private function getValidationRules(?int $id = null): array
    {
        $nisRule = 'required|min_length[3]|max_length[30]';
        $nisRule .= $id
            ? '|is_unique[siswa.nis,id,' . $id . ']'
            : '|is_unique[siswa.nis]';

        return [
            'nis' => [
                'rules'  => $nisRule,
                'errors' => [
                    'required'  => 'NIS wajib diisi.',
                    'is_unique' => 'NIS sudah digunakan.',
                ],
            ],
            'nama_siswa' => [
                'rules'  => 'required|min_length[3]|max_length[150]',
                'errors' => [
                    'required' => 'Nama siswa wajib diisi.',
                ],
            ],
            'jenis_kelamin' => [
                'rules'  => 'required|in_list[L,P]',
                'errors' => [
                    'required' => 'Jenis kelamin wajib dipilih.',
                ],
            ],
            'kelas_id' => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required' => 'Kelas wajib dipilih.',
                ],
            ],
            'tahun_ajaran_id' => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required' => 'Tahun ajaran wajib dipilih.',
                ],
            ],
            'wali_kelas_user_id' => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required' => 'Wali kelas wajib dipilih.',
                ],
            ],
            'nama_orang_tua' => [
                'rules'  => 'required|min_length[3]|max_length[150]',
                'errors' => [
                    'required' => 'Nama orang tua wajib diisi.',
                ],
            ],
            'nomor_hp_orang_tua' => [
                'rules' => 'permit_empty|max_length[25]',
            ],
            'alamat' => [
                'rules' => 'permit_empty|max_length[1000]',
            ],
            'gambar_siswa' => [
                'rules' => 'if_exist|is_image[gambar_siswa]|mime_in[gambar_siswa,image/jpg,image/jpeg,image/png,image/webp]|max_size[gambar_siswa,2048]',
            ],
        ];
    }

    private function getKelasOptions(): array
    {
        return $this->db->table('kelas')
            ->select('id, nama_kelas')
            ->where('deleted_at', null)
            ->orderBy('nama_kelas', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getTahunAjaranOptions(): array
    {
        return $this->db->table('tahun_ajaran')
            ->select('id, nama_tahun_ajaran, is_active, tanggal_mulai')
            ->where('deleted_at', null)
            ->orderBy('is_active', 'DESC')
            ->orderBy('tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function getGuruOptions(): array
    {
        return $this->db->table('users u')
            ->select('u.id, u.username, u.email')
            ->join('auth_groups_users agu', 'agu.user_id = u.id')
            ->join('auth_groups ag', 'ag.id = agu.group_id')
            ->where('ag.name', 'guru')
            ->where('u.deleted_at', null)
            ->where('u.active', 1)
            ->orderBy('u.username', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function isGuruUser(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $row = $this->db->table('users u')
            ->select('u.id')
            ->join('auth_groups_users agu', 'agu.user_id = u.id')
            ->join('auth_groups ag', 'ag.id = agu.group_id')
            ->where('u.id', $userId)
            ->where('ag.name', 'guru')
            ->where('u.deleted_at', null)
            ->where('u.active', 1)
            ->get()
            ->getRowArray();

        return ! empty($row);
    }

    private function getKelasTahunAjaranOptions(): array
    {
        return $this->db->table('kelas_tahun_ajaran kta')
            ->select('
                kta.id,
                k.nama_kelas,
                ta.nama_tahun_ajaran,
                ta.is_active,
                COALESCE(u.username, "-") as wali_kelas,
                COALESCE(kta.kuota_siswa, 0) as kuota_siswa
            ')
            ->join('kelas k', 'k.id = kta.kelas_id')
            ->join('tahun_ajaran ta', 'ta.id = kta.tahun_ajaran_id')
            ->join('users u', 'u.id = kta.wali_kelas_user_id', 'left')
            ->where('kta.deleted_at', null)
            ->orderBy('ta.is_active', 'DESC')
            ->orderBy('ta.tanggal_mulai', 'DESC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function resolveKelasTahunAjaranId(int $kelasId, int $tahunAjaranId, int $waliKelasUserId): ?int
    {
        $existing = $this->db->table('kelas_tahun_ajaran')
            ->select('id, wali_kelas_user_id, deleted_at')
            ->where('kelas_id', $kelasId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->get()
            ->getRowArray();

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $updateData = [
                'wali_kelas_user_id' => $waliKelasUserId,
                'updated_at'         => $now,
            ];

            if (! empty($existing['deleted_at'])) {
                $updateData['deleted_at'] = null;
            }

            $this->db->table('kelas_tahun_ajaran')
                ->where('id', $existing['id'])
                ->update($updateData);

            return (int) $existing['id'];
        }

        $this->db->table('kelas_tahun_ajaran')->insert([
            'kelas_id'            => $kelasId,
            'tahun_ajaran_id'     => $tahunAjaranId,
            'wali_kelas_user_id'  => $waliKelasUserId,
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        $insertId = $this->db->insertID();

        return $insertId ? (int) $insertId : null;
    }

    private function getSelectedKtaDetail(?int $ktaId): ?array
    {
        if (! $ktaId) {
            return null;
        }

        return $this->db->table('kelas_tahun_ajaran')
            ->select('id, kelas_id, tahun_ajaran_id, wali_kelas_user_id')
            ->where('id', $ktaId)
            ->get()
            ->getRowArray();
    }

    private function uploadImage($file): ?string
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $targetPath = FCPATH . 'uploads/siswa';
        if (! is_dir($targetPath)) {
            mkdir($targetPath, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($targetPath, $newName);

        return 'uploads/siswa/' . $newName;
    }

    private function deleteImageIfExists(?string $path = null): void
    {
        if (! $path) {
            return;
        }

        $fullPath = FCPATH . ltrim($path, '/');
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
