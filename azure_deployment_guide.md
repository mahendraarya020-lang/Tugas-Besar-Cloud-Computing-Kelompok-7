# Panduan Deployment Azure (Metode 100% GRATIS)
## Sistem Manajemen Freelance Project - TUBES CLOUD COMPUTING

Untuk menghindari tagihan kartu kredit atau menghabiskan kredit Azure for Students Anda, panduan ini menggunakan metode arsitektur cloud **100% GRATIS** menggunakan kombinasi:
1. **Neon.tech (atau Supabase)** sebagai penyedia database PostgreSQL **Gratis Selamanya** (*Free Tier*). Ini jauh lebih aman dan gratis dibandingkan membuat Azure Database for PostgreSQL (yang rentan memakan kredit/biaya).
2. **Azure Container Registry (ACR) Free Credits / Basic Tier** untuk repositori image.
3. **Azure Container Apps (ACA) Free Tier** dengan konfigurasi **Scale-to-Zero** (kontainer mati otomatis menjadi 0 replika saat tidak digunakan sehingga konsumsi biaya/kredit = Rp0,-). Azure memberikan kuota gratis **2 juta request** dan **180.000 vCPU-seconds** gratis setiap bulan.

---

## 🛠️ Fase 1: Membuat Database PostgreSQL GRATIS di Neon.tech

Sebagai ganti dari Azure PostgreSQL yang berbayar, kita akan menggunakan **Neon** (layanan database serverless PostgreSQL gratis selamanya).

1. Buka website [Neon.tech](https://neon.tech/) dan daftar menggunakan akun GitHub Anda.
2. Buat project baru dengan nama: `tubes-cloud-computing`.
3. Pilih lokasi server terdekat (misalnya: `Singapore` atau `Asia Pacific`).
4. Setelah project terbuat, Anda akan langsung diberikan **Connection String** database.
5. Pada Dashboard Neon Anda, salin detail koneksi berikut:
   * **Host:** (misalnya: `ep-cool-flower-a1bc2de.ap-southeast-1.aws.neon.tech`)
   * **Database:** `neondb` (atau buat database baru di menu Databases: `tubes_cloud_auth` dan `tubes_cloud_project` jika ingin terpisah secara fisik. Namun untuk menghemat free-tier project Neon, Anda bisa menyatukan data menggunakan skema yang sama atau membuat dua project Neon gratis secara terpisah).
     * *Rekomendasi agar 100% gratis:* Buat **dua project Neon gratis** (atau buat dua database di dalam satu project Neon Anda):
       * DB 1 (Auth Service): `neondb` di Project Neon pertama (misal host `ep-auth...`)
       * DB 2 (Project Service): `neondb` di Project Neon kedua (misal host `ep-project...`)
   * **Username:** `neondb_owner` (atau sesuai yang tertera)
   * **Password:** (klik tampilkan password dan salin)

---

## 📦 Fase 2: Membuat Azure Container Registry (ACR)

1. Cari **Container registries** di Azure Portal, klik **Create**.
2. Konfigurasikan:
   * **Resource Group:** (Klik *Create new*) `rg-tubes-cloud`
   * **Registry Name:** `tubescloudregistrymahen` (atau ganti dengan nama unik pilihan Anda)
   * **SKU:** `Basic` (ACR Basic sangat murah dan dicover oleh kredit gratis mahasiswa. Jika ingin 100% tanpa registri berbayar, Anda bisa langsung deploy dari Docker Hub publik, tetapi ACR Basic ini aman digunakan selama masa kuliah).
3. Setelah dideploy, masuk ke menu **Access keys** di sidebar kiri ACR Anda:
   * Klik **Enabled** pada **Admin user**.
   * Catat **Login server**, **Username**, dan **Password** yang tampil.

---

## 📤 Fase 3: Build & Push Docker Image (Terminal Lokal)
*Buka Terminal / PowerShell di root folder proyek Anda:*
`C:\Users\Mahen\OneDrive\Desktop\Mahen\Kuliah\TUBES CLOUD COMPUTING`

### 1. Login ke ACR Anda
```bash
docker login tubescloudregistrymahen.azurecr.io -u tubescloudregistrymahen -p <PASTE_PASSWORD_ACR_ANDA>
```

### 2. Build & Tag Docker Image
```bash
docker build -t tubescloudregistrymahen.azurecr.io/auth-service:latest ./auth-service
```
```bash
docker build -t tubescloudregistrymahen.azurecr.io/project-service:latest ./project-service
```
```bash
docker build -t tubescloudregistrymahen.azurecr.io/gateway-frontend:latest ./gateway-frontend
```

### 3. Push Image ke ACR
```bash
docker push tubescloudregistrymahen.azurecr.io/auth-service:latest
```
```bash
docker push tubescloudregistrymahen.azurecr.io/project-service:latest
```
```bash
docker push tubescloudregistrymahen.azurecr.io/gateway-frontend:latest
```

---

## 🚀 Fase 4: Deploy Container ke Azure Container Apps (ACA)

### 1. Deploy `auth-service` (Backend)
1. Cari **Container Apps** di Azure Portal, klik **Create**.
2. **Basics:**
   * **Resource Group:** `rg-tubes-cloud`
   * **Container App Name:** `auth-service`
   * **Environment:** (Klik *Create new*) `tubes-cloud-env`
3. **Container:**
   * Hilangkan centang *Use quickstart image*.
   * **Image source:** `Azure Container Registry`
   * **Registry:** `tubescloudregistrymahen`
   * **Image:** `auth-service`
   * **Tag:** `latest`
   * **Environment Variables (Salin & Tempel):**
     * Klik **+ Add** untuk setiap baris variabel di bawah:
     ```text
     APP_KEY = base64:9V21U7g/r+al2xcp+rs3k+W30ZPBtPK3eVQ/er9OSOo=
     ```
     ```text
     APP_ENV = production
     ```
     ```text
     JWT_SECRET = sbIsdvGit6EBxfruZR8g6zOClspzOC0DENtSRg0B0M9SOPJcONFSnqyTL1oyPodn
     ```
     ```text
     JWT_ALGO = HS256
     ```
     ```text
     DB_CONNECTION = pgsql
     DB_HOST = ep-lively-hat-ao1qoa5z.c-2.ap-southeast-1.aws.neon.tech
     DB_PORT = 5432
     DB_DATABASE = tubes_cloud_auth
     DB_USERNAME = neondb_owner
     DB_PASSWORD = <PASTE_PASSWORD_NEON_DATABASE_ANDA>
     ```
4. **Ingress:**
   * Ingress: `Enabled`
   * Ingress type: `Internal` (Aman & Terisolasi)
   * Target Port: `8000`
5. **Scale (Pengaturan agar Rp 0,- / Gratis):**
   * Cari tab/bagian **Scale** sebelum klik create (atau edit setelah dibuat).
   * Set **Min replicas** = `0` (Sangat penting! Kontainer akan mati saat tidak ada request, sehingga biaya = 0).
   * Set **Max replicas** = `1`.
6. Klik **Review + create** $\rightarrow$ **Create**. Setelah selesai, salin URL internalnya dari halaman Overview (misal: `http://auth-service.internal.tubes-cloud-env.eastasia.azurecontainerapps.io`).

---

### 2. Deploy `project-service` (Backend)
1. Buat Container App baru dengan nama `project-service` di environment yang sama (`tubes-cloud-env`).
2. **Container:**
   * **Image:** `project-service`
   * **Tag:** `latest`
   * **Environment Variables (Salin & Tempel):**
     ```text
     APP_KEY = base64:VStdzSk9hWWAnEAYjIyrtZVSiBl8UCS6Wo60D5aQrWk=
     ```
     ```text
     APP_ENV = production
     ```
     ```text
     JWT_SECRET = sbIsdvGit6EBxfruZR8g6zOClspzOC0DENtSRg0B0M9SOPJcONFSnqyTL1oyPodn
     ```
     ```text
     JWT_ALGO = HS256
     ```
     ```text
     DB_CONNECTION = pgsql
     DB_HOST = ep-lively-hat-ao1qoa5z.c-2.ap-southeast-1.aws.neon.tech
     DB_PORT = 5432
     DB_DATABASE = tubes_cloud_project
     DB_USERNAME = neondb_owner
     DB_PASSWORD = <PASTE_PASSWORD_NEON_DATABASE_ANDA>
     ```
3. **Ingress:**
   * Ingress: `Enabled`
   * Ingress type: `Internal`
   * Target Port: `8000`
4. **Scale:**
   * Set **Min replicas** = `0` (Scale to Zero - Gratis).
   * Set **Max replicas** = `1`.
5. Klik **Review + create** $\rightarrow$ **Create**. Setelah selesai, salin URL internalnya.

---

### 3. Deploy `gateway-frontend` (Frontend / Public Gateway)
1. Buat Container App baru dengan nama `gateway-frontend` di environment yang sama.
2. **Container:**
   * **Image:** `gateway-frontend`
   * **Tag:** `latest`
   * **Environment Variables (Salin & Tempel - Ganti dengan URL FQDN Internal Anda):**
     ```text
     APP_KEY = base64:VAzhflsdMC5VmKSs70327G4xmkn+hZuvUUr48sI1kgo=
     ```
     ```text
     APP_ENV = production
     ```
     ```text
     SESSION_DRIVER = cookie
     ```
     ```text
     AUTH_SERVICE_URL = http://auth-service.internal.tubes-cloud-env.eastasia.azurecontainerapps.io/api
     ```
     ```text
     PROJECT_SERVICE_URL = http://project-service.internal.tubes-cloud-env.eastasia.azurecontainerapps.io/api
     ```
3. **Ingress:**
   * Ingress: `Enabled`
   * Ingress type: `External` (Dapat diakses publik dari internet)
   * Target Port: `8000`
4. **Scale:**
   * Set **Min replicas** = `0` (Scale to Zero - Gratis).
   * Set **Max replicas** = `1`.
5. Klik **Review + create** $\rightarrow$ **Create**. Catat URL eksternal yang tertera (ini adalah tautan web aplikasi Anda).

---

## ⚖️ Fase 5: Menjalankan Migrasi Database

1. Buka Container App **auth-service** di Azure Portal.
2. Di sidebar kiri, klik **Console**.
3. Hubungkan ke kontainer `auth-service` menggunakan shell `/bin/sh`, klik **Connect**.
4. Jalankan perintah migrasi berikut untuk membuat tabel di database Neon `tubes_cloud_auth`:
   ```bash
   php artisan migrate --force
   ```
5. Buka Container App **project-service** $\rightarrow$ **Console** $\rightarrow$ Hubungkan ke `/bin/sh`.
6. Jalankan perintah migrasi berikut untuk membuat tabel di database Neon `tubes_cloud_project`:
   ```bash
   php artisan migrate --force
   ```

Aplikasi Anda kini sudah aktif 100% online secara gratis selamanya melalui URL eksternal milik `gateway-frontend`!
Jika kontainer tidak diakses, kontainer akan mati otomatis (scale to 0) sehingga kuota gratis Anda tidak terpakai secara sia-sia.
