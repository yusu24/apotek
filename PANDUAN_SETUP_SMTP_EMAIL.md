# 📖 Buku Panduan Setup Email (SMTP) & Backup Otomatis
**Sistem Informasi Apotek (Am Apotek System)**

---

## 📌 1. Pengenalan Pengaturan Email

Pengaturan Email di menu **Identitas Toko** berfungsi untuk 2 hal utama:
1. **Pengiriman Struk Belanja Elektronik (E-Receipt):** Mengirimkan nota penjualan dari kasir langsung ke email pelanggan.
2. **Pengiriman Backup Database & Laporan Mingguan:** Mengirimkan salinan database (`.sql`) dan laporan penjualan Excel secara otomatis ke email Pemilik/Owner Apotek setiap minggunya.

---

## 🔑 2. Langkah 1: Membuat "App Password" di Akun Gmail

Karena Google mewajibkan proteksi keamanan tinggi, Anda **tidak boleh** menggunakan password akun Gmail utama. Anda harus menggunakan **Password Aplikasi (App Password)** 16 karakter dari Google.

### Cara Membuat App Password Gmail:
1. Buka browser dan masuk ke [Google Account](https://myaccount.google.com/).
2. Pilih menu **Keamanan** (*Security*) di panel sebelah kiri.
3. Pastikan **Verifikasi 2 Langkah** (*2-Step Verification*) sudah **AKTIF**. (Jika belum aktif, ikuti petunjuk Google untuk mengaktifkannya via nomor HP).
4. Ketik **"Password Aplikasi"** atau **"App Passwords"** pada kolom pencarian di bagian atas halaman Akun Google.
5. Masukkan nama aplikasi, contoh: `Apotek System`, lalu klik **Buat** (*Create*).
6. Google akan menampilkan **16 karakter kode rahasia** (contoh: `qjak bvqs lzzw wxyc`).
7. **Salin / Catat kode 16 karakter tersebut** (tanpa spasi). Kode ini yang akan dipakai sebagai **Password Aplikasi (SMTP)**.

---

## ⚙️ 3. Langkah 2: Mengisi Formulir di Aplikasi Apotek

1. Buka aplikasi Apotek Anda, lalu masuk ke menu **Pengaturan Sistem** > **Identitas Toko** (`/settings/store`).
2. Gulir layar ke bawah sampai menemukan bagian **Pengaturan Email (SMTP)**.
3. Isi formulir sesuai rincian berikut:

| Nama Kolom | Contoh Pengisian | Penjelasan |
| :--- | :--- | :--- |
| **Email Pengirim Struk / Notifikasi** | `ampotek9@gmail.com` | Email yang bertugas mengirimkan struk belanja ke pelanggan. |
| **Email Penerima Backup Database** | `ampotek9@gmail.com` | Email milik Owner/Pemilik yang khusus menerima file backup database & laporan Excel mingguan. |
| **Nama Pengirim Struk** | `Arrohmah Medica` | Nama apotek yang akan muncul sebagai nama pengirim di inbox email pelanggan. |
| **Username Email (SMTP)** | `ampotek9@gmail.com` | Alamat email Gmail yang dipakai untuk login SMTP (sama dengan Email Pengirim). |
| **Password Aplikasi (SMTP)** | `qjakbvqslzwwxyc` | Kode 16 karakter **App Password** dari Google yang disalin pada Langkah 1. |
| **Host SMTP** | `smtp.gmail.com` | Server gateway Gmail (tetap diisi `smtp.gmail.com`). |
| **Port** | `587` | Port enkripsi Gmail (tetap diisi `587`). |
| **Enkripsi** | `tls` | Protokol keamanan (tetap diisi `tls`). |

4. Klik tombol **Simpan Pengaturan** di bagian bawah halaman.

---

## 🚀 4. Cara Kerja & Pengujian

### A. Tes Pengiriman Struk Kasir
1. Buka menu **Kasir (POS)**.
2. Lakukan transaksi penjualan seperti biasa.
3. Pada saat selesai pembayaran, centang/masukkan alamat email pelanggan.
4. Klik **Kirim Struk Email**. Struk akan terkirim dengan pengirim `Nama Toko Anda`.

### B. Tes Pengiriman Backup Otomatis
* Setiap minggu (secara otomatis via server VPS), sistem akan mengirimkan pesan berisi lampiran file backup database terbaru ke **Email Penerima Backup Database**.

---

## ❓ 5. Troubleshooting (Tanya Jawab Kendala)

* **Q: Mengapa email gagal terkirim / Error SMTP?**
  * **A:** Pastikan **Verifikasi 2 Langkah** di akun Google pengirim sudah aktif, dan pastikan Anda memasukkan **App Password 16 karakter**, bukan password asli login Gmail.

* **Q: Bisakah email pengirim struk dan penerima backup disamakan?**
  * **A:** Sangat bisa. Jika Anda ingin semua dikirim dan diterima oleh 1 email yang sama, cukup isikan email yang sama pada kolom **Email Pengirim Struk** dan **Email Penerima Backup Database**.
