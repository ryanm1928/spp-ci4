<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserAccountModel;

class AdminUserController extends BaseController
{
    protected string $profileUploadPath = FCPATH . 'uploads/profiles/';

    public function index()
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));

        $builder = $db->table('users u')
            ->select('u.id, u.username, u.email, u.profile_photo, u.active, u.created_at, g.name as role_name')
            ->join('auth_groups_users agu', 'agu.user_id = u.id', 'left')
            ->join('auth_groups g', 'g.id = agu.group_id', 'left');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('u.username', $keyword)
                ->orLike('u.email', $keyword)
                ->orLike('g.name', $keyword)
                ->groupEnd();
        }

        $users = $builder
            ->orderBy('u.id', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/users/index', [
            'title'   => 'Manajemen User',
            'menu'    => 'users',
            'users'   => $users,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        $roles = db_connect()
            ->table('auth_groups')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/users/create', [
            'title' => 'Tambah User',
            'menu'  => 'users',
            'roles' => $roles,
        ]);
    }

    public function store()
    {
        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'role'             => 'required|in_list[admin,kepala_sekolah,guru]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $photoPath = $this->uploadProfilePhoto();
        if ($photoPath === false) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();

        $roleName = trim((string) $this->request->getPost('role'));

        $group = $db->table('auth_groups')
            ->where('name', $roleName)
            ->get()
            ->getRowArray();

        if (! $group) {
            return redirect()->back()->withInput()->with('error', 'Role tidak ditemukan.');
        }

        $users = model(\Myth\Auth\Models\UserModel::class);
        $user  = new \Myth\Auth\Entities\User([
            'username'         => trim((string) $this->request->getPost('username')),
            'email'            => strtolower(trim((string) $this->request->getPost('email'))),
            'password'         => (string) $this->request->getPost('password'),
            'profile_photo'    => $photoPath,
            'active'           => 1,
            'force_pass_reset' => 0,
        ]);

        $db->transStart();

        if (! $users->save($user)) {
            $db->transRollback();

            if ($photoPath) {
                $this->deleteProfilePhotoFile($photoPath);
            }

            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        $userId = $users->getInsertID();

        $db->table('auth_groups_users')->insert([
            'group_id' => (int) $group['id'],
            'user_id'  => (int) $userId,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            if ($photoPath) {
                $this->deleteProfilePhotoFile($photoPath);
            }

            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan user.');
        }

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil dibuat.');
    }

    public function edit($id)
    {
        $db = db_connect();

        $user = $db->table('users u')
            ->select('u.id, u.username, u.email, u.profile_photo, u.active, g.name as role_name')
            ->join('auth_groups_users agu', 'agu.user_id = u.id', 'left')
            ->join('auth_groups g', 'g.id = agu.group_id', 'left')
            ->where('u.id', (int) $id)
            ->get()
            ->getRowArray();

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User tidak ditemukan.');
        }

        $roles = $db->table('auth_groups')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/users/edit', [
            'title' => 'Edit User',
            'menu'  => 'users',
            'user'  => $user,
            'roles' => $roles,
        ]);
    }

    public function update($id)
    {
        $id = (int) $id;
        $db = db_connect();

        $existingUser = $db->table('users')->where('id', $id)->get()->getRowArray();

        if (! $existingUser) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[30]|is_unique[users.username,id,{$id}]",
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'     => 'required|in_list[admin,kepala_sekolah,guru]',
            'active'   => 'required|in_list[0,1]',
        ];

        $password = (string) $this->request->getPost('password');
        $passwordConfirm = (string) $this->request->getPost('password_confirm');

        if ($password !== '' || $passwordConfirm !== '') {
            $rules['password'] = 'required|min_length[6]';
            $rules['password_confirm'] = 'required|matches[password]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $uploadedPhotoPath = $this->uploadProfilePhoto($existingUser['profile_photo'] ?? null);
        if ($uploadedPhotoPath === false) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $finalPhotoPath = $existingUser['profile_photo'] ?? null;
        $removePhoto    = $this->request->getPost('remove_photo') == '1';

        if ($uploadedPhotoPath !== null) {
            $finalPhotoPath = $uploadedPhotoPath;
        } elseif ($removePhoto) {
            $this->deleteProfilePhotoFile($existingUser['profile_photo'] ?? null);
            $finalPhotoPath = null;
        }

        $roleName = trim((string) $this->request->getPost('role'));

        $group = $db->table('auth_groups')
            ->where('name', $roleName)
            ->get()
            ->getRowArray();

        if (! $group) {
            return redirect()->back()->withInput()->with('error', 'Role tidak ditemukan.');
        }

        $updateData = [
            'username'      => trim((string) $this->request->getPost('username')),
            'email'         => trim((string) $this->request->getPost('email')),
            'profile_photo' => $finalPhotoPath,
            'active'        => (int) $this->request->getPost('active'),
        ];

        if ($password !== '') {
            $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $db->transStart();

        $db->table('users')
            ->where('id', $id)
            ->update($updateData);

        $groupUser = $db->table('auth_groups_users')
            ->where('user_id', $id)
            ->get()
            ->getRowArray();

        if ($groupUser) {
            $db->table('auth_groups_users')
                ->where('user_id', $id)
                ->update([
                    'group_id' => (int) $group['id'],
                ]);
        } else {
            $db->table('auth_groups_users')->insert([
                'group_id' => (int) $group['id'],
                'user_id'  => $id,
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate user.');
        }

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil diupdate.');
    }

    public function delete($id)
    {
        $id = (int) $id;
        $db = db_connect();

        $user = $db->table('users')->where('id', $id)->get()->getRowArray();

        if (! $user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        if (function_exists('user_id') && (int) user_id() === $id) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User yang sedang login tidak bisa dihapus.');
        }

        $db->transStart();

        $db->table('auth_groups_users')->where('user_id', $id)->delete();
        $db->table('users')->where('id', $id)->delete();

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Gagal menghapus user. Pastikan user tidak terhubung ke data lain.');
        }

        $this->deleteProfilePhotoFile($user['profile_photo'] ?? null);

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil dihapus.');
    }

    protected function uploadProfilePhoto(?string $oldPath = null)
    {
        $file = $this->request->getFile('profile_photo');

        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $photoRules = [
            'profile_photo' => [
                'label' => 'Photo Profil',
                'rules' => 'is_image[profile_photo]|mime_in[profile_photo,image/jpg,image/jpeg,image/png,image/webp]|max_size[profile_photo,2048]',
            ],
        ];

        if (! $this->validate($photoRules)) {
            return false;
        }

        if (! is_dir($this->profileUploadPath)) {
            mkdir($this->profileUploadPath, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($this->profileUploadPath, $newName);

        if ($oldPath) {
            $this->deleteProfilePhotoFile($oldPath);
        }

        return 'uploads/profiles/' . $newName;
    }

    protected function deleteProfilePhotoFile(?string $path): void
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
