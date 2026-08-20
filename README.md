# Aplikasi CBA UKP Center (Alice)

Aplikasi ini dipakai untuk mengelola data Ujian Kenaikan Pangkat (UKP) — mulai dari import hasil ujian, rekap nilai, sampai export data harian.

**Aplikasi ini hanya berjalan di komputer lokal (offline), tidak online/bisa diakses dari internet.** Semua data tersimpan di komputer tempat aplikasi ini dijalankan.

Panduan ini ditulis untuk siapa saja yang **tidak paham coding sama sekali** — cukup ikuti langkah-langkahnya persis seperti yang tertulis.

---

## 1. Yang Perlu Disiapkan Sekali Saja di Awal

### 1.1 Install Docker Desktop

Aplikasi ini "dikemas" pakai program bernama **Docker**. Docker inilah yang menjalankan aplikasinya, kamu tidak perlu install PHP atau MySQL secara manual.

1. Download dan install **Docker Desktop** (cari "Docker Desktop" di Google, pilih sesuai sistem operasimu — Windows/Mac).
2. Setelah terinstall, buka Docker Desktop dan tunggu sampai statusnya "Running" (biasanya ada ikon paus 🐳 di taskbar/menu bar).
3. Biarkan Docker Desktop tetap terbuka/jalan setiap kali mau pakai aplikasi ini.

### 1.2 Buka Folder Aplikasi di Terminal

- **Windows:** buka folder aplikasi ini, klik kanan di area kosong dalam folder, pilih **"Open in Terminal"** (atau buka Command Prompt/PowerShell lalu `cd` ke folder ini).
- **Mac:** buka folder ini, klik kanan, pilih **"New Terminal at Folder"**.

Semua perintah di bawah ini harus dijalankan **di dalam folder aplikasi ini** (folder yang ada file `docker-compose.yml`-nya).

### 1.3 Siapkan File & Folder yang Belum Ada

Aplikasi ini butuh beberapa file/folder pelengkap yang **tidak ikut ter-download** karena alasan teknis. Cukup dilakukan **sekali saja** di komputer ini.

**a. Buat file `php.ini`**

Buat file baru bernama `php.ini` (persis di folder aplikasi ini, sejajar dengan `docker-compose.yml`), isi dengan teks berikut (copy-paste apa adanya):

```
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
max_input_time = 300
display_errors = On
error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE
```

**b. Buat folder-folder kosong berikut** (buat folder biasa lewat File Explorer/Finder, tidak perlu isi apa-apa di dalamnya):

- `application/cache/sessions`
- `exim`
- `uploads`

> Kalau nanti muncul error berwarna merah yang isinya ada kata **"Permission denied"** atau **"mkdir()"**, itu tandanya salah satu folder ini belum ada atau belum bisa ditulis. Beri tahu tim teknis untuk dibantu set izin foldernya.

---

## 2. Menjalankan Aplikasi

Setelah langkah 1 selesai (cukup sekali), setiap mau pakai aplikasinya:

1. Pastikan **Docker Desktop sudah menyala** (ikon paus 🐳 statusnya jalan).
2. Buka Terminal di folder aplikasi ini (lihat langkah 1.2).
3. Ketik perintah berikut, lalu tekan Enter:

   ```
   docker compose up -d
   ```

   Tunggu beberapa saat (untuk pertama kali bisa 1-5 menit karena Docker sedang menyiapkan semuanya). Kalau prosesnya sudah selesai, kamu akan kembali bisa mengetik di Terminal.

4. Buka browser (Chrome/Edge/Firefox), lalu kunjungi alamat ini:

   ```
   http://localhost:8080
   ```

5. Login dengan akun yang sudah diberikan tim teknis.

**Aplikasi ini harus tetap dibuka lewat alamat di atas selama Docker Desktop menyala.** Kalau Docker Desktop ditutup, aplikasinya juga ikut berhenti — buka lagi Docker Desktop dan ulangi langkah 3 di atas untuk menyalakannya kembali.

---

## 3. Mematikan Aplikasi

Kalau sudah selesai pakai dan ingin mematikan aplikasinya (opsional — boleh juga dibiarkan menyala), di Terminal ketik:

```
docker compose down
```

Data yang sudah diimport/disimpan **tidak akan hilang** meski aplikasi dimatikan — data baru hilang kalau ada perintah khusus yang sengaja menghapusnya (di luar cakupan panduan ini).

---

## 4. Isi Data Awal (Database)

Kalau ini benar-benar pemasangan baru di komputer baru, database-nya masih **kosong** (belum ada data nilai/peserta sama sekali). Perlu di-import dulu satu kali dari file backup database (file `.sql`) yang disiapkan tim teknis. Minta tim teknis untuk membantu proses import ini — biasanya cukup dilakukan sekali di awal.

---

## 5. Masalah Umum & Solusinya

| Yang terlihat | Kemungkinan penyebab | Yang harus dilakukan |
|---|---|---|
| Halaman putih kosong setelah klik tombol tertentu | Biasanya bukan aplikasi rusak, tapi memang tidak ada yang ditampilkan di halaman itu | Tekan tombol "Back" di browser, coba ulangi. Kalau sering terjadi, beri tahu tim teknis |
| Tulisan merah "A PHP Error was encountered" muncul di atas halaman | Ada bug di aplikasi | Screenshot semuanya (jangan sebagian), kirim ke tim teknis |
| "Permission denied" / "mkdir()" | Folder pelengkap (lihat langkah 1.3) belum dibuat atau izinnya salah | Cek ulang langkah 1.3, atau minta bantuan tim teknis |
| Import file `.cba` gagal / macet setelah file kedua | Biasanya karena percobaan import sebelumnya gagal di tengah jalan | Coba import ulang file yang sama. Kalau tetap gagal, screenshot error-nya dan kirim ke tim teknis |
| Browser bilang "This site can't be reached" di `localhost:8080` | Docker Desktop belum menyala, atau aplikasinya belum dijalankan | Buka Docker Desktop, lalu ulangi langkah 2 |
| Setelah ganti komputer / install ulang, semua data hilang | Data tersimpan di komputer lama, tidak otomatis pindah | Perlu bawa file backup database dari komputer lama, minta bantuan tim teknis untuk pindahkan |

**Kalau ragu atau error yang muncul tidak ada di tabel ini, jangan coba klik-klik sembarangan** — screenshot dulu semua pesan errornya (termasuk yang berwarna merah paling bawah), lalu kirim ke tim teknis.

---

## 6. Istilah Singkat

- **Docker** = program yang "membungkus" aplikasi ini supaya bisa jalan tanpa perlu install macam-macam software satu-satu.
- **localhost:8080** = alamat aplikasi ini di browser, cuma bisa dibuka dari komputer yang sama tempat Docker-nya jalan.
- **Import** = memasukkan hasil ujian (file `.cba`) ke dalam aplikasi.
- **Export** = mengambil/mengunduh data dari aplikasi jadi sebuah file (`.cba`/`.zip`).
