<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSppModuleTables extends Migration
{
    public function up()
    {
        /**
         * 1) Master tahun ajaran
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'nominal_spp' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nama_tahun_ajaran');
        $this->forge->createTable('tahun_ajaran', true);

        /**
         * 2) Master kelas
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kelas' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nama_kelas');
        $this->forge->createTable('kelas', true);

        /**
         * 3) Master kas sekolah
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kas' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'nomor_rekening' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'saldo_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'saldo_berjalan' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nama_kas');
        $this->forge->createTable('kas_sekolah', true);

        /**
         * 4) Kelas per tahun ajaran
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kelas_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'tahun_ajaran_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'wali_kelas_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'kuota_siswa' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['kelas_id', 'tahun_ajaran_id'], false, false, 'kelas_id_tahun_ajaran_id');
        $this->forge->addUniqueKey(['kelas_id', 'tahun_ajaran_id'], 'kelas_tahun_ajaran_unique');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tahun_ajaran_id', 'tahun_ajaran', 'id', 'CASCADE', 'CASCADE');
        // disesuaikan dengan dump
        $this->forge->addForeignKey('wali_kelas_user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('kelas_tahun_ajaran', true);

        /**
         * 5) Siswa
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nis' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'nama_siswa' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
                'comment'    => 'L = Laki-laki, P = Perempuan',
            ],
            'kelas_tahun_ajaran_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'nama_orang_tua' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'nomor_hp_orang_tua' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
                'null'       => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'gambar_siswa' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nis');
        $this->forge->addKey('kelas_tahun_ajaran_id');
        $this->forge->addForeignKey('kelas_tahun_ajaran_id', 'kelas_tahun_ajaran', 'id', 'CASCADE', '');
        $this->forge->createTable('siswa', true);

        /**
         * 6) Tagihan SPP
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'siswa_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'kelas_tahun_ajaran_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'tahun_ajaran_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'bulan' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
            ],
            'tahun' => [
                'type'     => 'SMALLINT',
                'unsigned' => true,
            ],
            'nominal_tagihan' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'nominal_terbayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'tanggal_jatuh_tempo' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status_pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_bayar', 'sebagian', 'lunas'],
                'default'    => 'belum_bayar',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['siswa_id', 'tahun_ajaran_id'], false, false, 'siswa_id_tahun_ajaran_id');
        $this->forge->addUniqueKey(['siswa_id', 'tahun_ajaran_id', 'bulan', 'tahun'], 'tagihan_spp_unique');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_tahun_ajaran_id', 'kelas_tahun_ajaran', 'id', 'CASCADE', '');
        $this->forge->addForeignKey('tahun_ajaran_id', 'tahun_ajaran', 'id', 'CASCADE', '');
        $this->forge->createTable('tagihan_spp', true);

        /**
         * 7) Pembayaran SPP
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tagihan_spp_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'kode_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'tanggal_bayar' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'jumlah_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'metode_pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['tunai', 'transfer', 'qris', 'lainnya'],
                'default'    => 'tunai',
            ],
            'bukti_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'dicatat_oleh_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_pembayaran_record' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'void'],
                'default'    => 'active',
            ],
            'void_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'voided_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'voided_by_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'edited_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'edited_by_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_pembayaran');
        $this->forge->addKey(['tagihan_spp_id', 'tanggal_bayar'], false, false, 'tagihan_spp_id_tanggal_bayar');
        $this->forge->addKey('status_pembayaran_record', false, false, 'idx_pembayaran_spp_status');
        $this->forge->addKey('voided_by_user_id', false, false, 'idx_pembayaran_spp_voided_by');
        $this->forge->addKey('edited_by_user_id', false, false, 'idx_pembayaran_spp_edited_by');
        $this->forge->addForeignKey('tagihan_spp_id', 'tagihan_spp', 'id', 'CASCADE', 'CASCADE');
        // disesuaikan dengan dump
        $this->forge->addForeignKey('dicatat_oleh_user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('voided_by_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('edited_by_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('pembayaran_spp', true);

        /**
         * 8) Saldo SPP bulanan
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun_ajaran_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'bulan' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
            ],
            'tahun' => [
                'type'     => 'SMALLINT',
                'unsigned' => true,
            ],
            'nama_periode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'saldo_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'total_masuk' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'total_keluar' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'saldo_akhir' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'is_locked' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['tahun_ajaran_id', 'bulan', 'tahun'], 'saldo_spp_bulanan_unique');
        $this->forge->addForeignKey('tahun_ajaran_id', 'tahun_ajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('saldo_spp_bulanan', true);

        /**
         * 9) Transaksi SPP bulanan
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'saldo_spp_bulanan_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'pembayaran_spp_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'tanggal_transaksi' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'jenis_transaksi' => [
                'type'       => 'ENUM',
                'constraint' => ['debit', 'kredit'],
                'default'    => 'debit',
            ],
            'sumber_transaksi' => [
                'type'       => 'ENUM',
                'constraint' => ['pembayaran_spp', 'penyesuaian', 'pengembalian'],
                'default'    => 'pembayaran_spp',
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'saldo_sebelum' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'saldo_sesudah' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'dibuat_oleh_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('saldo_spp_bulanan_id');
        $this->forge->addKey('pembayaran_spp_id');
        $this->forge->addKey('dibuat_oleh_user_id');
        $this->forge->addForeignKey('saldo_spp_bulanan_id', 'saldo_spp_bulanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pembayaran_spp_id', 'pembayaran_spp', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('dibuat_oleh_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('transaksi_spp_bulanan', true);

        /**
         * 10) Transaksi kas
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kas_sekolah_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'pembayaran_spp_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'tanggal_transaksi' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'jenis_transaksi' => [
                'type'       => 'ENUM',
                'constraint' => ['debit', 'kredit'],
                'comment'    => 'debit = pemasukan, kredit = pengeluaran',
            ],
            'sumber_transaksi' => [
                'type'       => 'ENUM',
                'constraint' => ['spp', 'operasional', 'pemasukan_lain', 'pengeluaran_lain'],
                'default'    => 'operasional',
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'saldo_sebelum' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'saldo_sesudah' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'dibuat_oleh_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['kas_sekolah_id', 'tanggal_transaksi'], false, false, 'kas_sekolah_id_tanggal_transaksi');
        $this->forge->addKey('pembayaran_spp_id');
        $this->forge->addForeignKey('kas_sekolah_id', 'kas_sekolah', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pembayaran_spp_id', 'pembayaran_spp', 'id', 'CASCADE', 'SET NULL');
        // disesuaikan dengan dump
        $this->forge->addForeignKey('dibuat_oleh_user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('transaksi_kas', true);

        /**
         * 11) Laporan bulanan keuangan
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bulan' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
            ],
            'tahun' => [
                'type'     => 'SMALLINT',
                'unsigned' => true,
            ],
            'saldo_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'total_pemasukan' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'total_pengeluaran' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'saldo_akhir' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => 0.00,
            ],
            'status_laporan' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'menunggu_approval', 'disetujui', 'ditolak'],
                'default'    => 'draft',
            ],
            'dibuat_oleh_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['bulan', 'tahun'], 'laporan_bulanan_unique');
        // disesuaikan dengan dump
        $this->forge->addForeignKey('dibuat_oleh_user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('laporan_bulanan_keuangan', true);

        /**
         * 12) Approval laporan bulanan
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'laporan_bulanan_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'approver_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'role_approval' => [
                'type'       => 'ENUM',
                'constraint' => ['kepala_sekolah', 'bendahara'],
                'comment'    => 'bendahara memakai user dengan role guru',
            ],
            'status_approval' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'disetujui', 'ditolak'],
                'default'    => 'pending',
            ],
            'tanggal_approval' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['laporan_bulanan_id', 'role_approval'], 'approval_laporan_role_unique');
        $this->forge->addForeignKey('laporan_bulanan_id', 'laporan_bulanan_keuangan', 'id', 'CASCADE', 'CASCADE');
        // disesuaikan dengan dump
        $this->forge->addForeignKey('approver_user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('approval_laporan_bulanan', true);

        /**
         * Seed minimal kas sekolah.
         * Saya sengaja tidak seed kelas default supaya tidak bentrok dengan data aktual di dump.
         */
        $kasExists = $this->db->table('kas_sekolah')
            ->where('nama_kas', 'Saldo TK Kartini')
            ->countAllResults();

        if ($kasExists === 0) {
            $this->db->table('kas_sekolah')->insert([
                'nama_kas'       => 'Saldo TK Kartini',
                'saldo_awal'     => 0,
                'saldo_berjalan' => 0,
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('approval_laporan_bulanan', true);
        $this->forge->dropTable('laporan_bulanan_keuangan', true);
        $this->forge->dropTable('transaksi_kas', true);
        $this->forge->dropTable('transaksi_spp_bulanan', true);
        $this->forge->dropTable('saldo_spp_bulanan', true);
        $this->forge->dropTable('pembayaran_spp', true);
        $this->forge->dropTable('tagihan_spp', true);
        $this->forge->dropTable('siswa', true);
        $this->forge->dropTable('kelas_tahun_ajaran', true);
        $this->forge->dropTable('kas_sekolah', true);
        $this->forge->dropTable('kelas', true);
        $this->forge->dropTable('tahun_ajaran', true);
    }
}
