<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\TahunAjaranModel;
use Config\Database;

class AdminKelasTahunAjaranController extends BaseController
{
    protected $kelasModel;
    protected $tahunAjaranModel;
    protected $db;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->db = Database::connect();
    }

    private function currentTab(): string
    {
        $tab = strtolower((string) ($this->request->getPost('current_tab')
            ?? $this->request->getGet('tab')
            ?? 'kelas'));

        return in_array($tab, ['kelas', 'tahun'], true) ? $tab : 'kelas';
    }

    private function manageClassUrl(string $tab = 'kelas', array $params = []): string
    {
        $tab = in_array($tab, ['kelas', 'tahun'], true) ? $tab : 'kelas';

        $query = array_merge(['tab' => $tab], $params);
        $query = array_filter($query, static fn($value) => $value !== null && $value !== '');

        return site_url('admin/manage-class') . '?' . http_build_query($query);
    }

    public function index()
    {
        $editKelasId = (int) ($this->request->getGet('edit_kelas') ?? 0);
        $editTahunId = (int) ($this->request->getGet('edit_tahun') ?? 0);

        $data = [
            'menu'        => 'manage-class',
            'title'       => 'Kelas dan Tahun Ajaran',
            'kelas'       => $this->kelasModel->orderBy('id', 'DESC')->findAll(),
            'tahunAjaran' => $this->tahunAjaranModel->orderBy('id', 'DESC')->findAll(),
            'kelasEdit'   => $editKelasId ? $this->kelasModel->find($editKelasId) : null,
            'tahunEdit'   => $editTahunId ? $this->tahunAjaranModel->find($editTahunId) : null,
        ];

        return view('admin/KelasDanTahunAjaran/index', $data);
    }

    public function storeKelas()
    {
        $tab = 'kelas';

        $rules = [
            'nama_kelas' => 'required|min_length[2]|max_length[50]|is_unique[kelas.nama_kelas]',
            'deskripsi'  => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($this->manageClassUrl($tab))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->kelasModel->insert([
            'nama_kelas' => trim($this->request->getPost('nama_kelas')),
            'deskripsi'  => trim((string) $this->request->getPost('deskripsi')),
        ]);

        return redirect()->to($this->manageClassUrl($tab))
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function updateKelas($id)
    {
        $tab = 'kelas';
        $kelas = $this->kelasModel->find($id);

        if (! $kelas) {
            return redirect()->to($this->manageClassUrl($tab))
                ->with('error', 'Data kelas tidak ditemukan.');
        }

        $rules = [
            'nama_kelas' => "required|min_length[2]|max_length[50]|is_unique[kelas.nama_kelas,id,{$id}]",
            'deskripsi'  => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($this->manageClassUrl($tab, ['edit_kelas' => $id]))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->kelasModel->update($id, [
            'nama_kelas' => trim($this->request->getPost('nama_kelas')),
            'deskripsi'  => trim((string) $this->request->getPost('deskripsi')),
        ]);

        return redirect()->to($this->manageClassUrl($tab))
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function deleteKelas($id)
    {
        $tab = $this->currentTab();
        $kelas = $this->kelasModel->find($id);

        if (! $kelas) {
            return redirect()->to($this->manageClassUrl($tab))
                ->with('error', 'Data kelas tidak ditemukan.');
        }

        $relasi = $this->db->table('kelas_tahun_ajaran')
            ->where('kelas_id', $id)
            ->where('deleted_at', null)
            ->countAllResults();

        if ($relasi > 0) {
            return redirect()->to($this->manageClassUrl($tab))
                ->with('error', 'Kelas tidak bisa dihapus karena sudah dipakai pada data kelas tahun ajaran.');
        }

        $this->kelasModel->delete($id, true);

        return redirect()->to($this->manageClassUrl($tab))
            ->with('success', 'Data kelas berhasil dihapus permanen.');
    }

    public function storeTahunAjaran()
    {
        $tab = 'tahun';

        $rules = [
            'nama_tahun_ajaran' => 'required|min_length[4]|max_length[20]|is_unique[tahun_ajaran.nama_tahun_ajaran]',
            'tanggal_mulai'     => 'required',
            'tanggal_selesai'   => 'required',
            'nominal_spp'       => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($this->manageClassUrl($tab))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $isActive = $this->request->getPost('is_active') ? 1 : 0;
        $nominalSpp = $this->normalizeNominalSpp($this->request->getPost('nominal_spp'));

        if ($isActive) {
            $this->tahunAjaranModel->builder()->update(['is_active' => 0]);
        }

        $this->tahunAjaranModel->insert([
            'nama_tahun_ajaran' => trim($this->request->getPost('nama_tahun_ajaran')),
            'tanggal_mulai'     => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai'   => $this->request->getPost('tanggal_selesai'),
            'nominal_spp'       => $nominalSpp,
            'is_active'         => $isActive,
        ]);

        return redirect()->to($this->manageClassUrl($tab))
            ->with('success', 'Data tahun ajaran berhasil ditambahkan.');
    }

    public function updateTahunAjaran($id)
    {
        $tab = 'tahun';
        $tahun = $this->tahunAjaranModel->find($id);

        if (! $tahun) {
            return redirect()->to($this->manageClassUrl($tab))
                ->with('error', 'Data tahun ajaran tidak ditemukan.');
        }

        $rules = [
            'nama_tahun_ajaran' => "required|min_length[4]|max_length[20]|is_unique[tahun_ajaran.nama_tahun_ajaran,id,{$id}]",
            'tanggal_mulai'     => 'required',
            'tanggal_selesai'   => 'required',
            'nominal_spp'       => 'required|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($this->manageClassUrl($tab, ['edit_tahun' => $id]))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if ($isActive) {
            $this->tahunAjaranModel->builder()->update(['is_active' => 0]);
        }

        $this->tahunAjaranModel->update($id, [
            'nama_tahun_ajaran' => trim($this->request->getPost('nama_tahun_ajaran')),
            'tanggal_mulai'     => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai'   => $this->request->getPost('tanggal_selesai'),
            'nominal_spp'       => $this->request->getPost('nominal_spp'),
            'is_active'         => $isActive,
        ]);

        return redirect()->to($this->manageClassUrl($tab))
            ->with('success', 'Data tahun ajaran berhasil diperbarui.');
    }

    public function deleteTahunAjaran($id)
    {
        $tab = $this->currentTab();
        $tahun = $this->tahunAjaranModel->find($id);

        if (! $tahun) {
            return redirect()->to($this->manageClassUrl($tab))
                ->with('error', 'Data tahun ajaran tidak ditemukan.');
        }

        $relasi = $this->db->table('kelas_tahun_ajaran')
            ->where('tahun_ajaran_id', $id)
            ->where('deleted_at', null)
            ->countAllResults();

        if ($relasi > 0) {
            return redirect()->to($this->manageClassUrl($tab))
                ->with('error', 'Tahun ajaran tidak bisa dihapus karena sudah dipakai pada data kelas tahun ajaran.');
        }

        $this->tahunAjaranModel->delete($id, true);

        return redirect()->to($this->manageClassUrl($tab))
            ->with('success', 'Data tahun ajaran berhasil dihapus permanen.');
    }

    private function normalizeNominalSpp($value): int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return 0;
        }

        // kalau format dari DB / request seperti 80000.00
        if (is_numeric($value)) {
            return (int) round((float) $value);
        }

        // fallback: ambil digit saja
        return (int) preg_replace('/\D+/', '', $value);
    }
}
