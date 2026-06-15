# Panduan Deployment Azure (Metode 100% GRATIS)
## Sistem Manajemen Freelance Project - TUBES CLOUD COMPUTING

Untuk menghindari tagihan kartu kredit atau menghabiskan kredit Azure for Students Anda, panduan ini menggunakan metode arsitektur cloud **100% GRATIS** menggunakan kombinasi:
1. **Neon.tech (PostgreSQL Serverless)**: Penyedia database PostgreSQL **Gratis Selamanya** (*Free Tier*). Ini jauh lebih aman dan gratis dibandingkan membuat Azure Database for PostgreSQL (yang rawan menghabiskan kredit/biaya).
2. **Azure Container Registry (ACR) Basic Tier**: Tempat menyimpan Docker image.
3. **Azure Container Apps (ACA) Free Tier**: Tempat menjalankan aplikasi dengan konfigurasi **Scale-to-Zero** (kontainer mati otomatis menjadi 0 replika jika tidak digunakan agar konsumsi biaya/kredit = Rp0,-). Azure memberikan kuota gratis **2 juta request** dan **180.000 vCPU-seconds** setiap bulan.

---

## 🛠️ Fase 1: Membuat & Menyiapkan Database di Neon.tech

### Langkah 1: Registrasi Akun
1. Buka browser Anda dan kunjungi halaman: [Neon.tech](https://neon.tech/).
2. Klik tombol **`Sign up for free`** di pojok kanan atas.
3. Pilih pendaftaran menggunakan akun **GitHub** Anda.

### Langkah 2: Membuat Project Baru
1. Setelah login, Anda akan masuk ke dashboard Neon. Klik tombol **`Create Project`**.
2. Isi kolom konfigurasi berikut:
   * **Project Name:** `Cloud Computing`
   * **Postgres Version:** Pilih versi terbaru (biasanya default `16` atau `18`).
   * **Region:** Pilih **`Asia Pacific (Singapore)`** (lokasi terdekat untuk latency rendah).
3. Klik tombol **`Create Project`** di bagian bawah.
4. Halaman baru akan memuat informasi **Connection Details**. Biarkan tab ini tetap terbuka karena kita akan menggunakan datanya.

### Langkah 3: Membuat Database `tubes_cloud_auth` dan `tubes_cloud_project`
1. Di panel sidebar sebelah kiri halaman Dashboard Neon Anda, cari bagian **BRANCH** lalu klik menu **`SQL Editor`** (pilihan ke-4 dari atas).
2. Di dalam kolom teks editor SQL yang terbuka (berjudul *New query*), hapus semua teks yang ada lalu ketikkan perintah SQL berikut:
   ```sql
   CREATE DATABASE tubes_cloud_auth;
   CREATE DATABASE tubes_cloud_project;
   ```
3. Klik tombol **`Run`** di kanan atas editor SQL untuk menjalankan perintah tersebut.
4. Di bagian bawah editor akan muncul tab **Success** yang menandakan kedua database telah berhasil dibuat.

---

## 📦 Fase 2: Membuat Azure Container Registry (ACR) di Azure Portal (SELESAI ✅)
*Langkah ini telah Anda lakukan dengan sukses dengan detail berikut:*
* **Resource Group:** `tubes-cloud`
* **Location:** `Southeast Asia`
* **Login Server:** `tubescloudcomptingkelompok7.azurecr.io`
* **Username:** `tubescloudcomptingkelompok7`
* **Password:** `7hbleCIvOPQ39bE8djpLcaqK3MRCs7ir573zYzo0vuefFnX05dTiJQQJ99CFACqBBLyEqg7NAAACAZCRQoFT`

---

## 📤 Fase 3: Build & Push Docker Image (Terminal Lokal)

*Buka aplikasi **PowerShell** atau **Command Prompt (CMD)** di komputer Anda, lalu masuk ke folder utama proyek:*
`C:\Users\Mahen\OneDrive\Desktop\Mahen\Kuliah\TUBES CLOUD COMPUTING`

### 1. Melakukan Login Docker ke Azure Registry Anda
Jalankan perintah berikut di terminal lokal Anda (langsung salin dan jalankan):
```bash
docker login tubescloudcomptingkelompok7.azurecr.io -u tubescloudcomptingkelompok7 -p 7hbleCIvOPQ39bE8djpLcaqK3MRCs7ir573zYzo0vuefFnX05dTiJQQJ99CFACqBBLyEqg7NAAACAZCRQoFT
```
*Jika berhasil, terminal akan menampilkan pesan `Login Succeeded`.*

### 2. Melakukan Build & Tagging Docker Image
Jalankan ketiga perintah di bawah satu per satu untuk membangun image lokal dengan tag registri Azure Anda:
```bash
docker build -t tubescloudcomptingkelompok7.azurecr.io/auth-service:latest ./auth-service
```
```bash
docker build -t tubescloudcomptingkelompok7.azurecr.io/project-service:latest ./project-service
```
```bash
docker build -t tubescloudcomptingkelompok7.azurecr.io/gateway-frontend:latest ./gateway-frontend
```

### 3. Mengunggah (Push) Image ke Azure Container Registry
Jalankan perintah ini satu per satu untuk mengunggah kontainer ke cloud Azure (proses ini membutuhkan koneksi internet stabil):
```bash
docker push tubescloudcomptingkelompok7.azurecr.io/auth-service:latest
```
```bash
docker push tubescloudcomptingkelompok7.azurecr.io/project-service:latest
```
```bash
docker push tubescloudcomptingkelompok7.azurecr.io/gateway-frontend:latest
```

---

## 🚀 Fase 4: Deploy Container ke Azure Container Apps (ACA)

### 1. Mendeploy `auth-service` (Backend Keamanan)
1. Di kolom pencarian bagian atas Azure Portal, ketik **`Container Apps`**, lalu klik layanan tersebut.
2. Klik tombol **`+ Create`** di pojok kiri atas halaman.
3. Di tab **Basics**, isi formulir berikut:
   * **Subscription:** Pilih subscription Anda (misalnya *Azure for Students*).
   * **Resource group:** Pilih **`tubes-cloud`** (Resource group Anda).
   * **Container app name:** Masukkan nama **`auth-service`**.
   * **Region:** Pilih **`Southeast Asia`** (Lokasi ACR Anda).
   * **Container Apps Environment:** Klik tombol **`Create new`** di bawah dropdown. Pada formulir pop-up yang muncul, isi *Environment name* dengan **`tubes-cloud-env`**, lalu klik **`Create`**.
4. Klik tombol **`Next: Container >`** di bagian bawah halaman.
5. Di tab **Container**, hapus centang pada opsi **`Use quickstart image`**. Isi konfigurasi container seperti berikut:
   * **Image source:** Pilih **`Azure Container Registry`**.
   * **Registry:** Pilih **`tubescloudcomptingkelompok7`**.
   * **Image:** Pilih **`auth-service`**.
   * **Tag:** Pilih **`latest`**.
   * **CPU and Memory:** Pilih ukuran paling kecil, yaitu **`0.25 Cores, 0.5 GiB`** (agar hemat kuota gratis).
6. Di halaman yang sama, cari kolom **Environment variables**, klik tombol **`+ Add`** berulang kali untuk memasukkan 10 variabel berikut:
   * `APP_KEY` = `base64:9V21U7g/r+al2xcp+rs3k+W30ZPBtPK3eVQ/er9OSOo=`
   * `APP_ENV` = `production`
   * `JWT_SECRET` = `sbIsdvGit6EBxfruZR8g6zOClspzOC0DENtSRg0B0M9SOPJcONFSnqyTL1oyPodn`
   * `JWT_ALGO` = `HS256`
   * `DB_CONNECTION` = `pgsql`
   * `DB_HOST` = `ep-lively-hat-ao1qoa5z.c-2.ap-southeast-1.aws.neon.tech`
   * `DB_PORT` = `5432`
   * `DB_DATABASE` = `tubes_cloud_auth`
   * `DB_USERNAME` = `neondb_owner`
   * `DB_PASSWORD` = (Masukkan kata sandi asli database Neon Anda yang tertera di screenshot pertama Neon)
7. Klik tombol **`Next: Ingress >`** di bagian bawah halaman.
8. Di tab **Ingress**, atur konfigurasi berikut:
   * **Ingress:** Centang kotak **`Enabled`** (Aktif).
   * **Ingress type:** Pilih **`Internal`** (Hanya dapat diakses secara privat di dalam virtual network, aman dari luar).
   * **Target Port:** Ketik **`8000`**.
9. Di halaman yang sama, cari tab atau bagian **Scale** (atau klik *Next: Scale*):
   * Atur **Minimum replicas** menjadi **`0`** (Ini adalah opsi penting *Scale-to-Zero* agar tagihan gratis Rp0,- saat kontainer tidak diakses).
   * Atur **Maximum replicas** menjadi **`1`**.
10. Klik tombol **`Review + create`** $\rightarrow$ tunggu verifikasi $\rightarrow$ klik **`Create`**.
11. Setelah deployment selesai, buka Container App `auth-service` Anda, cari **Application Url** (FQDN) di halaman Overview dan catat URL ini.
    * *Format URL FQDN Internal seharusnya:* `http://auth-service.internal.tubes-cloud-env.southeastasia.azurecontainerapps.io`

---

### 2. Mendeploy `project-service` (Backend Proyek & Keuangan)
1. Kembali ke halaman **Container Apps** di Azure Portal, klik **`+ Create`**.
2. Di tab **Basics**, lakukan pengisian berikut:
   * **Resource group:** Pilih **`tubes-cloud`**.
   * **Container app name:** Masukkan nama **`project-service`**.
   * **Container Apps Environment:** Pilih environment yang telah dibuat sebelumnya (**`tubes-cloud-env`**).
3. Klik tombol **`Next: Container >`** di bagian bawah.
4. Di tab **Container**, isi konfigurasi image berikut:
   * **Image source:** Pilih **`Azure Container Registry`**.
   * **Registry:** Pilih **`tubescloudcomptingkelompok7`**.
   * **Image:** Pilih **`project-service`**.
   * **Tag:** Pilih **`latest`**.
   * **CPU and Memory:** Pilih **`0.25 Cores, 0.5 GiB`**.
5. Di halaman yang sama, masukkan variabel lingkungan (Environment variables) berikut dengan mengklik tombol **`+ Add`**:
   * `APP_KEY` = `base64:VStdzSk9hWWAnEAYjIyrtZVSiBl8UCS6Wo60D5aQrWk=`
   * `APP_ENV` = `production`
   * `JWT_SECRET` = `sbIsdvGit6EBxfruZR8g6zOClspzOC0DENtSRg0B0M9SOPJcONFSnqyTL1oyPodn`
   * `JWT_ALGO` = `HS256`
   * `DB_CONNECTION` = `pgsql`
   * `DB_HOST` = `ep-lively-hat-ao1qoa5z.c-2.ap-southeast-1.aws.neon.tech`
   * `DB_PORT` = `5432`
   * `DB_DATABASE` = `tubes_cloud_project`
   * `DB_USERNAME` = `neondb_owner`
   * `DB_PASSWORD` = (Masukkan kata sandi asli database Neon Anda yang tertera di screenshot pertama Neon)
6. Klik tombol **`Next: Ingress >`** di bagian bawah.
7. Di tab **Ingress**, atur konfigurasi berikut:
   * **Ingress:** Centang kotak **`Enabled`**.
   * **Ingress type:** Pilih **`Internal`**.
   * **Target Port:** Ketik **`8000`**.
8. Atur bagian **Scale** (Skala):
   * Atur **Minimum replicas** menjadi **`0`**.
   * Atur **Maximum replicas** menjadi **`1`**.
9. Klik tombol **`Review + create`** $\rightarrow$ tunggu verifikasi $\rightarrow$ klik **`Create`**.
10. Setelah deployment selesai, salin dan **catat Application Url internal** milik `project-service` dari halaman Overview.
    * *Format URL FQDN Internal seharusnya:* `http://project-service.internal.tubes-cloud-env.southeastasia.azurecontainerapps.io`

---

### 3. Mendeploy `gateway-frontend` (Public Portal)
1. Kembali ke halaman **Container Apps** di Azure Portal, klik **`+ Create`**.
2. Di tab **Basics**, lakukan pengisian berikut:
   * **Resource group:** Pilih **`tubes-cloud`**.
   * **Container app name:** Masukkan nama **`gateway-frontend`**.
   * **Container Apps Environment:** Pilih environment **`tubes-cloud-env`**.
3. Klik tombol **`Next: Container >`** di bagian bawah.
4. Di tab **Container**, isi konfigurasi image berikut:
   * **Image source:** Pilih **`Azure Container Registry`**.
   * **Registry:** Pilih **`tubescloudcomptingkelompok7`**.
   * **Image:** Pilih **`gateway-frontend`**.
   * **Tag:** Pilih **`latest`**.
   * **CPU and Memory:** Pilih **`0.25 Cores, 0.5 GiB`**.
5. Di halaman yang sama, masukkan variabel lingkungan (Environment variables) berikut dengan mengklik tombol **`+ Add`**:
   * `APP_KEY` = `base64:VAzhflsdMC5VmKSs70327G4xmkn+hZuvUUr48sI1kgo=`
   * `APP_ENV` = `production`
   * `SESSION_DRIVER` = `cookie`
   * `AUTH_SERVICE_URL` = `http://auth-service.internal.tubes-cloud-env.southeastasia.azurecontainerapps.io/api`
   * `PROJECT_SERVICE_URL` = `http://project-service.internal.tubes-cloud-env.southeastasia.azurecontainerapps.io/api`
6. Klik tombol **`Next: Ingress >`** di bagian bawah.
7. Di tab **Ingress**, atur konfigurasi berikut:
   * **Ingress:** Centang kotak **`Enabled`**.
   * **Ingress type:** Pilih **`External`** (Ini akan membuka akses publik agar dapat diakses dari internet).
   * **Target Port:** Ketik **`8000`**.
8. Atur bagian **Scale** (Skala):
   * Atur **Minimum replicas** menjadi **`0`**.
   * Atur **Maximum replicas** menjadi **`1`**.
9. Klik tombol **`Review + create`** $\rightarrow$ tunggu verifikasi $\rightarrow$ klik **`Create`**.
10. Setelah deployment selesai, salin **Application Url** (eksternal) milik `gateway-frontend` dari halaman Overview. Ini adalah alamat link website utama Anda!

---

## ⚖️ Fase 5: Menjalankan Migrasi Database di Azure Portal

Setelah semua Container Apps berstatus running, jalankan perintah migrasi sekali saja ke masing-masing database di Neon:

### Langkah 1: Migrasi Database `auth-service`
1. Buka halaman Container App **`auth-service`** di Azure Portal.
2. Di panel sidebar sebelah kiri, cari bagian **Settings** lalu klik menu **`Console`**.
3. Di halaman Console yang terbuka, klik tombol **`Connect`** (menggunakan pengaturan default Startup console `/bin/sh`).
4. Setelah terminal terhubung, ketikkan perintah berikut lalu tekan Enter:
   ```bash
   php artisan migrate --force
   ```
5. Tunggu hingga proses migrasi database selesai memproses tabel migrasi.

### Langkah 2: Migrasi Database `project-service`
1. Buka halaman Container App **`project-service`** di Azure Portal.
2. Di panel sidebar sebelah kiri, klik menu **`Console`**.
3. Klik tombol **`Connect`**.
4. Setelah terminal terhubung, ketikkan perintah berikut lalu tekan Enter:
   ```bash
   php artisan migrate --force
   ```
5. Tunggu hingga proses migrasi database proyek selesai memproses tabel migrasi.

Aplikasi Anda kini sudah aktif 100% online secara gratis selamanya melalui URL eksternal milik `gateway-frontend`!
