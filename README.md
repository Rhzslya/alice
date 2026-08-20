# Aplikasi CBA UKP Center (Alice)

Aplikasi ini dipakai untuk mengelola data Ujian Kenaikan Pangkat (UKP) — mulai dari import hasil ujian, rekap nilai, sampai export data harian.

**Aplikasi ini hanya berjalan di komputer lokal (offline), tidak online/bisa diakses dari internet.** Semua data tersimpan di komputer tempat aplikasi ini dijalankan.

Panduan ini ditulis untuk siapa saja yang **tidak paham coding sama sekali** — cukup ikuti langkah-langkahnya persis seperti yang tertulis.

---

## 1. Yang Perlu Disiapkan Sekali Saja di Awal

### 1.1 Install Docker Desktop

Aplikasi ini "dikemas" pakai program bernama **Docker**. Docker inilah yang menjalankan aplikasinya, kamu tidak perlu install PHP atau MySQL secara manual. Ini instalasi biasa seperti install aplikasi lain, cuma ada beberapa langkah tambahan — ikuti persis.

#### Kalau komputer kamu Windows

1. Buka browser, ketik di kolom alamat (bukan kolom pencarian Google): `docker.com` lalu Enter.
2. Cari tombol **Download Docker Desktop**, klik, pilih versi **Windows**.
3. Setelah file `Docker Desktop Installer.exe` selesai terdownload, buka/klik dua kali file itu.
4. Ikuti saja instruksi di layar, klik **Next**/**OK** terus sampai selesai. Kalau muncul pilihan/centangan yang tidak dimengerti, biarkan saja default-nya (jangan diubah-ubah), lalu lanjut.
5. Kalau muncul kotak dialog soal **WSL 2** (Windows Subsystem for Linux), ikuti link/tombol yang disediakan installer untuk install itu juga — biasanya installer akan memandu otomatis, atau minta restart komputer dulu.
6. Setelah instalasi selesai, **restart komputer**.
7. Setelah komputer nyala lagi, buka **Docker Desktop** dari Start Menu.
8. Kalau diminta buat akun/login Docker, boleh dilewati (pilih "Skip" atau "Continue without signing in" kalau ada) — tidak wajib untuk pemakaian ini.
9. Tunggu sampai muncul tulisan **"Docker Desktop is running"** atau ikon paus 🐳 di pojok kanan bawah (system tray) sudah tidak lagi loading/berputar.

> **Kalau muncul error soal "virtualization is not enabled" atau "Hyper-V"**: ini artinya ada pengaturan khusus di komputer (BIOS) yang perlu diaktifkan dulu, biasanya oleh IT/teknisi komputer. Ini di luar cakupan panduan ini — minta bantuan tim teknis.

#### Kalau komputer kamu Mac

1. Buka browser, ketik di kolom alamat: `docker.com` lalu Enter.
2. Cari tombol **Download Docker Desktop**, klik, pilih versi **Mac** — perhatikan pilihan **Apple Chip** atau **Intel Chip** (kalau tidak tahu Mac kamu chip apa, cek lewat menu Apple logo di pojok kiri atas → **About This Mac**).
3. Buka file `.dmg` yang terdownload, lalu drag ikon Docker ke folder **Applications** sesuai instruksi di layar.
4. Buka aplikasi **Docker** dari folder Applications (atau Launchpad).
5. Kalau diminta izin/password Mac, masukkan password Mac kamu (ini normal, Docker butuh izin sistem).
6. Kalau diminta buat akun/login Docker, boleh dilewati.
7. Tunggu sampai ikon paus 🐳 di menu bar atas sudah tidak loading lagi.

#### Setelah Docker Desktop terinstall

- **Setiap kali mau pakai aplikasi ini, buka dulu Docker Desktop dan tunggu sampai ikon paus-nya siap (tidak loading).** Baru lanjut ke langkah-langkah berikutnya.
- Docker Desktop boleh dibiarkan terbuka terus di background selama komputer menyala.

### 1.2 Buka Folder Aplikasi di Terminal

- **Windows:** buka folder aplikasi ini, klik kanan di area kosong dalam folder, pilih **"Open in Terminal"** (atau buka Command Prompt/PowerShell lalu `cd` ke folder ini).
- **Mac:** buka folder ini, klik kanan, pilih **"New Terminal at Folder"**.

Semua perintah di bawah ini harus dijalankan **di dalam folder aplikasi ini** (folder yang ada file `docker-compose.yml`-nya).

### 1.3 Siapkan File & Folder yang Belum Ada

Aplikasi ini butuh beberapa file/folder pelengkap yang **tidak ikut ter-download** karena alasan teknis. Cukup dilakukan **sekali saja** di komputer ini.

Pertama, buka folder aplikasi ini (folder utama tempat kamu menyimpan/mengekstrak aplikasi ini — folder yang di dalamnya ada file `docker-compose.yml`, `Dockerfile`, folder `application`, dst). **Semua yang dibuat di bawah ini letaknya di dalam folder utama ini** (atau di dalam sub-foldernya, sesuai yang ditulis).

Supaya lebih jelas, ini posisi setiap file/folder yang perlu dibuat (tulisan **← BUAT INI** menandai yang belum ada dan harus kamu buat):

```
alice/                              <- folder utama aplikasi ini
├── docker-compose.yml              (sudah ada)
├── Dockerfile                      (sudah ada)
├── php.ini                         <- BUAT FILE INI (langkah a)
├── exim/                           <- BUAT FOLDER INI (langkah b)
├── uploads/                        <- BUAT FOLDER INI (langkah b)
└── application/
    └── cache/
        └── sessions/               <- BUAT FOLDER INI (langkah b, di dalam application/cache/)
```

**a. Buat file `php.ini`**

Di dalam folder utama (`alice/`, sejajar dengan `docker-compose.yml`), buat file baru bernama persis `php.ini` (bukan `php.ini.txt` — kalau di Windows Notepad, saat Save As pilih "Save as type: All Files" supaya tidak otomatis ditambah `.txt`). Isi filenya dengan teks berikut (copy-paste apa adanya):

```
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
max_input_time = 300
display_errors = On
error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE
```

**b. Buat 3 folder kosong berikut** (klik kanan → New Folder di File Explorer/Finder, tidak perlu diisi apa-apa di dalamnya):

1. `exim` — dibuat **langsung di dalam folder utama** `alice/` (sejajar dengan `docker-compose.yml`).
2. `uploads` — dibuat **langsung di dalam folder utama** `alice/` (sejajar dengan `docker-compose.yml`).
3. `sessions` — dibuat **di dalam folder `application/cache/`**. Kalau folder `cache` di dalam `application` belum ada juga, buat dulu foldernya, baru di dalamnya buat folder `sessions`. Jadi urutannya: buka folder `application` → buka folder `cache` → di dalam situ buat folder baru bernama `sessions`.

> Kalau nanti muncul error berwarna merah yang isinya ada kata **"Permission denied"** atau **"mkdir()"**, itu tandanya salah satu folder di atas belum ada, salah tempat, atau belum bisa ditulis. Cek ulang posisinya sesuai diagram di atas, atau minta bantuan tim teknis untuk dibantu set izin foldernya.

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

Kalau ini benar-benar pemasangan baru di komputer baru, database-nya masih **kosong** (belum ada data nilai/peserta sama sekali). Perlu di-import dulu satu kali dari file backup database (file `.sql`).

Import ini bisa dilakukan sendiri lewat browser (tidak perlu command line), pakai tools bawaan bernama **phpMyAdmin** yang sudah disiapkan bareng aplikasi ini.

1. Pastikan aplikasi sudah dinyalakan dulu (lihat bagian **2. Menjalankan Aplikasi** di atas — `docker compose up -d`).
2. Buka browser, kunjungi alamat:

   ```
   http://localhost:8081
   ```

3. Di halaman login phpMyAdmin, isi:
   - **Username:** `root`
   - **Password:** (kosongkan saja, tidak usah diisi)

   Lalu klik **Go** / **Login**.
4. Di panel sebelah kiri, klik nama database **`cba_ukp_center`**.
5. Di bagian atas halaman, klik tab **Import**.
6. Klik **Choose File** (atau **Browse**), pilih file `.sql` backup database yang sudah kamu punya.
7. Scroll ke bawah, klik tombol **Go** / **Import** di paling bawah halaman.
8. Tunggu sampai muncul kotak hijau bertuliskan **"Import has been successfully finished"**. Kalau file-nya besar, ini bisa makan waktu beberapa menit — jangan tutup/refresh halaman sebelum selesai.

> **Kalau muncul pesan error soal ukuran file terlalu besar**, beri tahu tim teknis untuk menaikkan batas ukuran upload-nya.

> phpMyAdmin ini adalah tools untuk mengelola database secara langsung — cukup hati-hati, jangan sembarangan klik menu lain selain **Import** kecuali diarahkan tim teknis, karena ada menu yang bisa **menghapus data**.

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
- **localhost:8080** = alamat aplikasi utama (CBA UKP Center) di browser, cuma bisa dibuka dari komputer yang sama tempat Docker-nya jalan.
- **localhost:8081** = alamat **phpMyAdmin**, dipakai khusus untuk import file `.sql` database (lihat bagian 4).
- **Import** = memasukkan data ke dalam aplikasi — bisa file `.cba` (hasil ujian, lewat aplikasi utama) atau file `.sql` (database, lewat phpMyAdmin).
- **Export** = mengambil/mengunduh data dari aplikasi jadi sebuah file (`.cba`/`.zip`).
