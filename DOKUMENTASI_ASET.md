# Panduan Manajemen Aset Tetap & Penyusutan (Amortisasi)

Modul ini dirancang untuk mengelola aset tetap perusahaan dan menghitung penyusutan secara otomatis sesuai dengan **Undang-Undang Pajak Penghasilan (UU PPh)** yang berlaku di Indonesia.

## 1. Mendaftarkan Aset Baru

Untuk mendaftarkan aset, buka menu **Keuangan & Administrasi > Manajemen Aset Tetap** dan klik tombol **Tambah Aset**.

### Kelompok Harta Berwujud (Sesuai UU PPh)
Pilih kelompok yang sesuai untuk menentukan umur ekonomis aset:

| Kelompok | Umur Ekonomis | Contoh Aset |
| :--- | :--- | :--- |
| **Kelompok 1** | 4 Tahun | Mebel/peralatan kayu, alat perkantoran (mesin tik, fotokopi), alat komunikasi (HP). |
| **Kelompok 2** | 8 Tahun | Mebel/peralatan logam, AC, komputer, printer, kendaraan bermotor. |
| **Kelompok 3** | 16 Tahun | Mesin-mesin industri berat, kapal, pesawat. |
| **Kelompok 4** | 20 Tahun | Alat berat konstruksi, mesin pembangkit listrik. |
| **Bangunan (Permanen)** | 20 Tahun | Gedung permanen/beton. |
| **Bangunan (Non-P)** | 10 Tahun | Gudang kayu, bangunan semi-permanen. |

### Metode Penyusutan
1.  **Garis Lurus (Straight Line)**: Beban penyusutan sama besar setiap bulan selama masa manfaat.
2.  **Saldo Menurun (Declining Balance)**: Beban penyusutan lebih besar di awal tahun (hanya berlaku untuk harta bukan bangunan).

## 2. Cara Menjalankan Penyusutan Bulanan

Penyusutan tidak terjadi secara otomatis setiap hari, melainkan harus diproses secara manual setiap akhir bulan (atau kapanpun Anda siap melakukan tutup buku bulanan).

1.  Buka menu **Manajemen Aset Tetap**.
2.  Klik tombol **Proses Penyusutan**.
3.  Pilih **Bulan** dan **Tahun** yang ingin diproses.
4.  Klik **Jalankan Jurnal Penyusutan**.

### Apa yang Terjadi Saat Proses Berjalan?
Sistem akan secara otomatis membuat entri di **Jurnal Umum**:
- **(Debit)** Beban Penyusutan Aset
- **(Kredit)** Akumulasi Penyusutan Aset

Nilai Buku aset akan berkurang sesuai dengan hasil perhitungan.

## 3. Akun Akuntansi yang Dibutuhkan
Pastikan Anda memilih akun yang tepat pada form pendaftaran aset:
- **Akun Aset**: Akun neraca (1-3xxx) yang mencatat nilai perolehan.
- **Akun Akumulasi**: Akun kontra-aset (1-3xxx) yang mencatat total penyusutan.
- **Akun Beban**: Akun laba-rugi (5-xxxx) yang mencatat biaya penyusutan periode berjalan.

---

> [!TIP]
> **Penting**: Sesuai UU PPh, penyusutan dimulai pada bulan dilakukan pengeluaran (perolehan). Jika Anda membeli aset di tengah bulan, sistem akan menghitung penyusutan penuh untuk bulan tersebut.
