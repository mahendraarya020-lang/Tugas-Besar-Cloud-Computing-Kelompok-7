# Panduan Perekaman Layar (Screen Recording Guide) - Edisi Proses Deployment
## Langkah Demi Langkah Deployment Microservices ke Microsoft Azure

Panduan ini dirancang untuk menunjukkan **proses pembuatan dan deployment** secara bertahap (step-by-step) dari nol hingga aplikasi berjalan di cloud Microsoft Azure. Gunakan panduan ini agar rekaman terlihat runut, logis, dan mencakup semua poin penting yang dinilai oleh dosen.

---

## 📹 Struktur Timeline & Durasi Video (Target: 7 - 10 Menit)

| Scene | Aktivitas / Proses yang Direkam | Durasi Target | Fokus Penilaian Dosen |
| :--- | :--- | :---: | :--- |
| **Scene 1** | Pembuatan Azure Database for PostgreSQL & Database | 1.5 Menit | Setup Database & Networking Cloud |
| **Scene 2** | Pembuatan Azure Container Registry (ACR) | 1 Menit | Setup Repositori Image Privat |
| **Scene 3** | Build & Push Docker Image via Terminal Lokal | 1.5 Menit | Proses Kontainerisasi & Tagging ACR |
| **Scene 4** | Deployment Container App (ACA) Backend | 2 Menit | Konfigurasi Environment & Keamanan Internal Ingress |
| **Scene 5** | Deployment Container App (ACA) Gateway Frontend | 1.5 Menit | Konfigurasi Gateway & Eksternal Ingress |
| **Scene 6** | Eksekusi Migrasi Database & Uji Coba URL Publik | 1.5 Menit | Uji Coba Aplikasi (End-to-End) & Verifikasi |

---

## 📝 Panduan & Naskah Narasi Perekaman Proses Deployment

### 🎬 Scene 1: Pembuatan Azure Database for PostgreSQL
* **Layar:** Pengisian form pembuatan Azure Database for PostgreSQL di Azure Portal.
* **Langkah:**
  1. Tunjukkan saat memilih resource group dan mengisi server name.
  2. Sorot tab **Networking**, dan tunjukkan saat Anda mencentang opsi **"Allow public access from any Azure service within Azure to this server"**. (PENTING!).
  3. Setelah server terbuat, tunjukkan pembuatan dua database (`tubes_cloud_auth` dan `tubes_cloud_project`) di panel menu **Databases**.
* **Contoh Narasi:**
  > *"Pertama, saya akan mendemonstrasikan proses pembuatan database PostgreSQL di Azure. Saya membuat server database fleksibel dengan spesifikasi Burstable untuk efisiensi biaya. Pada konfigurasi jaringan, saya mencentang izin akses publik untuk layanan Azure agar Container Apps dapat terhubung nanti. Setelah server aktif, saya menambahkan dua database terpisah, yaitu `tubes_cloud_auth` untuk auth-service dan `tubes_cloud_project` untuk project-service."*

---

### 🎬 Scene 2: Pembuatan Azure Container Registry (ACR)
* **Layar:** Azure Portal - Pembuatan Container Registry.
* **Langkah:**
  1. Tampilkan form pengisian ACR dengan SKU **Basic**.
  2. Setelah dideploy, buka resource registry tersebut, masuk ke menu **Access keys**, lalu rekam momen saat Anda menyalakan (**Enabled**) toggle **Admin user**.
* **Contoh Narasi:**
  > *"Selanjutnya, saya membuat Azure Container Registry untuk menjadi repositori privat tempat menyimpan Docker image. Setelah deployment ACR selesai, saya masuk ke menu Access Keys dan mengaktifkan fitur Admin User untuk mendapatkan username dan password akses kontainer."*

---

### 🎬 Scene 3: Build & Push Docker Image via Terminal Lokal
* **Layar:** Terminal/PowerShell di komputer lokal.
* **Langkah:**
  1. Jalankan perintah login: `docker login <login-server> -u <username> -p <password>` (isi disembunyikan/sensor jika perlu).
  2. Jalankan perintah build: `docker build -t <acr-name>.azurecr.io/auth-service:latest ./auth-service`.
  3. Jalankan perintah push: `docker push <acr-name>.azurecr.io/auth-service:latest`.
  4. **EDIT VIDEO:** Percepat bagian loading unggah (*fast-forward*) hingga statusnya berhasil terunggah ke ACR.
* **Contoh Narasi:**
  > *"Beralih ke komputer lokal, saya melakukan login docker ke registry Azure yang baru saja dibuat. Kemudian, saya mem-build image masing-masing microservice secara lokal berdasarkan Dockerfile yang sudah dikonfigurasi, lalu mengunggahnya ke Azure Container Registry menggunakan perintah docker push."*

---

### 🎬 Scene 4: Deployment Container Apps (ACA) Backend
* **Layar:** Azure Portal - Pembuatan Container App `auth-service` dan `project-service`.
* **Langkah:**
  1. Buat Container App `auth-service`, arahkan source image ke registry ACR yang berisi `auth-service:latest`.
  2. Tunjukkan pengisian variabel lingkungan (seperti `DB_HOST`, `DB_DATABASE=tubes_cloud_auth`, `JWT_SECRET`, dll.).
  3. Tunjukkan di tab **Ingress**: aktifkan Ingress dan pilih tipe **Internal** dengan port **8000**.
  4. Jelaskan bahwa langkah serupa diulangi untuk `project-service` dengan database `tubes_cloud_project`.
* **Contoh Narasi:**
  > *"Sekarang saya akan melakukan deployment kontainer backend di Azure Container Apps. Di sini saya membuat Container App bernama `auth-service` menggunakan image dari ACR. Variabel lingkungan diinjeksikan secara aman, mencakup kredensial database PostgreSQL dan JWT secret key. Untuk keamanan arsitektur, Ingress diset ke **Internal** pada port 8000 sehingga layanan backend terisolasi. Langkah yang sama diulangi untuk mendeploy `project-service` yang terhubung ke database `tubes_cloud_project`."*

---

### 🎬 Scene 5: Deployment Container App (ACA) Gateway Frontend
* **Layar:** Azure Portal - Pembuatan Container App `gateway-frontend`.
* **Langkah:**
  1. Buat Container App, arahkan ke image `gateway-frontend:latest` di ACR.
  2. Pada variabel lingkungan, tunjukkan pengisian `AUTH_SERVICE_URL` dan `PROJECT_SERVICE_URL` menggunakan URL FQDN internal milik `auth-service` dan `project-service` (misal: `http://auth-service.internal...`).
  3. Di tab **Ingress**, tunjukkan Anda memilih tipe **External** dengan port **8000**.
* **Contoh Narasi:**
  > *"Layanan terakhir yang dideploy adalah `gateway-frontend`. Karena berfungsi sebagai penghubung luar, saya mengatur Ingress kontainer ini ke tipe **External** agar dapat diakses dari internet. Pada bagian environment variables, saya mengarahkan URL auth-service dan project-service ke FQDN internal Azure masing-masing backend yang telah disalin sebelumnya."*

---

### 🎬 Scene 6: Eksekusi Migrasi Database & Uji Coba URL Publik
* **Layar:** Menu **Console** di Azure Container Apps dan tampilan browser web.
* **Langkah:**
  1. Masuk ke Container App `auth-service` $\rightarrow$ **Console** $\rightarrow$ Hubungkan ke shell kontainer $\rightarrow$ jalankan `php artisan migrate --force`.
  2. Ulangi di `project-service` untuk menjalankan `php artisan migrate --force`.
  3. Buka URL publik milik `gateway-frontend` di browser baru.
  4. Lakukan demo singkat (registrasi, login, buat klien, proyek, milestone, generate invoice, dan logout) untuk menunjukkan aplikasi berjalan lancar secara online.
* **Contoh Narasi:**
  > *"Langkah terakhir, saya masuk ke Console kontainer backend di Azure Portal untuk menjalankan perintah migrasi database `php artisan migrate --force` di masing-masing service. Setelah tabel database terbuat, saya membuka URL publik Gateway Frontend. Saya mendemonstrasikan alur lengkap aplikasi mulai dari pendaftaran akun, pembuatan data klien dan proyek, penambahan milestone, hingga pembuatan invoice cetak. Semua proses ini berjalan secara real-time dan terhubung dengan aman di Microsoft Azure. Terima kasih."*

---

## 💡 Tips Pengambilan Gambar Tambahan (Khusus Proses Deployment)
1. **Gunakan Efek Transisi:** Gunakan transisi halus saat berpindah dari proses pembuatan database server (Scene 1) ke proses pembuatan ACR (Scene 2) agar durasi tidak terbuang oleh waktu tunggu pembuatan resource (*deployment in progress*).
2. **Sorot Panel Penting:** Saat mengisi data sensitif seperti password DB atau JWT key, Anda dapat memperbesar layar (*zoom*) ke area isian variabel lingkungan agar dosen dapat melihat variabel apa saja yang diinjeksikan secara modular.
3. **Pastikan Docker Desktop Aktif:** Sebelum mulai merekam, jalankan terlebih dahulu Docker Desktop lokal agar perintah build/push tidak menghasilkan error saat direkam.
