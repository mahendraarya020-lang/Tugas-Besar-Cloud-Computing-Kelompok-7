# Sistem Manajemen Freelance Project (TUBES CLOUD COMPUTING)

TUBES CLOUD COMPUTING adalah platform berbasis web dengan arsitektur *microservices* yang dirancang untuk membantu freelancer mengelola proyek, melacak kemajuan per klien, memantau milestone & deadline, mencatat status pembayaran, serta menghasilkan faktur (*invoice generator*) secara otomatis.

Aplikasi ini menggunakan framework **Laravel** untuk ketiga microservice-nya:
1. **Gateway/Frontend Service** (Port 80/8000) - Menyajikan UI berbasis Blade + Tailwind CSS dan mem-proxy request ke service lainnya.
2. **Auth Service** (Port 8001/8000) - Menangani otentikasi JWT dan manajemen pengguna.
3. **Project Service** (Port 8002/8000) - Menangani CRUD Proyek, Klien, Milestone, dan Invoice.

---

## 1. Panduan Instalasi Lokal (Docker Compose)

Untuk menjalankan seluruh sistem di mesin lokal Anda:

### Prasyarat
- Docker Desktop terinstal dan sedang berjalan.

### Langkah-langkah
1. Jalankan file launcher script `run.bat` di root direktori dengan mengklik ganda atau via terminal:
   ```cmd
   run.bat
   ```
   *Script ini otomatis akan:*
   - Memeriksa status Docker.
   - Membangun dan menjalankan container (`docker-compose up -d --build`).
   - Menunggu database terinisialisasi.
   - Menjalankan migrasi database di container `auth-service` dan `project-service`.

2. Akses layanan di browser/klien API Anda:
   - **Gateway/Frontend (UI):** [http://localhost](http://localhost) (atau [http://localhost/login](http://localhost/login))
   - **Auth Service API:** [http://localhost:8001/api](http://localhost:8001/api)
   - **Project Service API:** [http://localhost:8002/api](http://localhost:8002/api)

3. Untuk menghentikan sistem, jalankan:
   ```bash
   docker-compose down
   ```

---

## 2. Dokumentasi API (API Endpoints)

Semua service menggunakan format respon error yang konsisten dalam JSON ketika terjadi kegagalan validasi atau otorisasi.

### A. Auth Service Endpoints (Port 8001)

| Method | Endpoint | Deskripsi | Headers | Body (JSON) |
| :--- | :--- | :--- | :--- | :--- |
| **POST** | `/api/register` | Mendaftarkan akun pengguna baru | - | `name`, `email`, `password`, `role` (Admin/User) |
| **POST** | `/api/login` | Melakukan login & mendapatkan JWT Token | - | `email`, `password` |
| **POST** | `/api/logout` | Mencabut/invalidate token JWT aktif | `Authorization: Bearer <token>` | - |
| **POST** | `/api/refresh` | Memperbarui masa aktif JWT Token | `Authorization: Bearer <token>` | - |
| **GET** | `/api/me` | Mendapatkan info profil pengguna aktif | `Authorization: Bearer <token>` | - |

### B. Project Service Endpoints (Port 8002)
*Seluruh endpoint di bawah mewajibkan Header: `Authorization: Bearer <token>`*

#### 👥 Client Registry
- `GET /api/clients` - Mengambil semua klien (Admin melihat semua, User melihat milik sendiri)
- `POST /api/clients` - Mendaftarkan klien baru (`name`, `email`, `phone` (opsional), `company` (opsional))
- `GET /api/clients/{client}` - Mendapatkan detail klien spesifik
- `PUT /api/clients/{client}` - Memperbarui data klien
- `DELETE /api/clients/{client}` - Menghapus data klien

#### 📂 Project Management
- `GET /api/projects` - Mengambil semua proyek
- `POST /api/projects` - Membuat proyek baru (`client_id`, `title`, `description` (opsional), `budget`, `status` (Pitching/In Progress/Review/Completed), `start_date`, `end_date`)
- `GET /api/projects/{project}` - Mendapatkan detail proyek spesifik
- `PUT /api/projects/{project}` - Memperbarui data proyek / mengganti status
- `DELETE /api/projects/{project}` - Menghapus proyek

#### 🏁 Milestones & Deadlines
- `GET /api/projects/{project}/milestones` - Mengambil milestone proyek tertentu
- `POST /api/projects/{project}/milestones` - Menambahkan milestone baru (`title`, `description` (opsional), `due_date`, `status` (pending/completed))
- `GET /api/milestones/{milestone}` - Detail milestone
- `PUT /api/milestones/{milestone}` - Memperbarui status/data milestone
- `DELETE /api/milestones/{milestone}` - Menghapus milestone

#### 💵 Invoices
- `GET /api/projects/{project}/invoices` - Mengambil invoice proyek tertentu
- `POST /api/projects/{project}/invoices` - Membuat invoice baru (`amount`, `due_date`, `status` (unpaid/partially paid/paid))
- `GET /api/invoices/{invoice}` - Detail invoice
- `PUT /api/invoices/{invoice}` - Memperbarui status/data invoice
- `DELETE /api/invoices/{invoice}` - Menghapus invoice
- `GET /api/invoices/{invoice}/download` - Mengambil HTML cetak faktur (invoice)

---

## 3. Langkah-langkah Deployment ke Microsoft Azure

Deployment dirancang menggunakan **Azure Container Registry (ACR)** untuk image hosting, **Azure Database for PostgreSQL (Flexible Server)** sebagai database terpusat, dan **Azure Container Apps (ACA)** untuk menjalankan container secara aman & otomatis.

```mermaid
graph TD
    Client[Browser Client] -->|Akses Publik (Port 443)| Gateway[gateway-frontend <br> ACA - External Ingress]
    
    subgraph Azure Container Apps Environment (Virtual Network)
        Gateway -->|Private Routing| AuthSvc[auth-service <br> ACA - Internal Ingress]
        Gateway -->|Private Routing| ProjSvc[project-service <br> ACA - Internal Ingress]
    end
    
    subgraph Database Layer
        AuthSvc -->|Port 5432| AzurePG[(Azure PostgreSQL Flexible Server)]
        ProjSvc -->|Port 5432| AzurePG
    end
```

### Langkah 1: Persiapan Database PostgreSQL di Azure
1. Masuk ke **Azure Portal** dan cari **Azure Database for PostgreSQL flexible servers**.
2. Klik **Create**, lalu konfigurasikan detail server (disarankan ukuran terkecil/burstable untuk kebutuhan akademis/testing).
3. Di tab **Networking**, pastikan Anda mengaktifkan opsi **"Allow public access from any Azure service within Azure to this server"** agar Azure Container Apps dapat terhubung tanpa konfigurasi VNet yang rumit.
4. Buat dua database terpisah di dalam server tersebut melalui panel *Databases* atau tool seperti pgAdmin:
   - `tubes_cloud_auth` (untuk database otentikasi)
   - `tubes_cloud_project` (untuk database proyek)

### Langkah 2: Membuat Azure Container Registry (ACR)
1. Cari **Container registries** di Azure Portal, klik **Create**.
2. Masukkan Registry Name unik (contoh: `tubescloudregistry`), pilih Resource Group, lalu pilih SKU **Basic**.
3. Setelah dideploy, buka menu **Access Keys** di registry baru Anda, lalu aktifkan **Admin user**. Catat *Login server*, *Username*, dan *Password* untuk proses login Docker.

### Langkah 3: Build & Push Docker Image
Gunakan CLI Docker lokal untuk mengunggah microservices Anda ke ACR. Jalankan perintah berikut di root project:

1. **Login ke ACR:**
   ```bash
   docker login <LOGIN_SERVER_ACR> -u <USERNAME_ACR> -p <PASSWORD_ACR>
   ```
2. **Build & Tag Images:**
   - **Auth Service:**
     ```bash
     docker build -t <LOGIN_SERVER_ACR>/auth-service:latest ./auth-service
     ```
   - **Project Service:**
     ```bash
     docker build -t <LOGIN_SERVER_ACR>/project-service:latest ./project-service
     ```
   - **Gateway Frontend:**
     ```bash
     docker build -t <LOGIN_SERVER_ACR>/gateway-frontend:latest ./gateway-frontend
     ```
3. **Push Images ke ACR:**
   ```bash
   docker push <LOGIN_SERVER_ACR>/auth-service:latest
   ```
   ```bash
   docker push <LOGIN_SERVER_ACR>/project-service:latest
   ```
   ```bash
   docker push <LOGIN_SERVER_ACR>/gateway-frontend:latest
   ```

### Langkah 4: Deploy Container ke Azure Container Apps (ACA)
1. Cari **Container Apps** di Azure Portal, klik **Create**.
2. Pilih Container App Environment baru (contoh: `tubes-cloud-environment`).

#### A. Deploy `auth-service`
1. Konfigurasikan App Name: `auth-service`.
2. Di tab *Container*, pilih source image dari **Azure Container Registry** Anda dan pilih `auth-service:latest`.
3. Di tab *Ingress*:
   - Aktifkan Ingress: **Enabled**
   - Ingress type: **Internal** (Aman, tidak diekspos ke publik)
   - Target Port: **8000**
4. Di bagian **Environment Variables** / **Secrets** (gunakan Secrets untuk data sensitif):
   - `APP_KEY`: *[Kunci Laravel Anda]*
   - `JWT_SECRET`: *[JWT Secret Key Anda]*
   - `JWT_ALGO`: `HS256`
   - `DB_CONNECTION`: `pgsql`
   - `DB_HOST`: *[Host Server Flexible PostgreSQL]*
   - `DB_PORT`: `5432`
   - `DB_DATABASE`: `tubes_cloud_auth`
   - `DB_USERNAME`: *[Username PostgreSQL]*
   - `DB_PASSWORD`: *[Password PostgreSQL]*

#### B. Deploy `project-service`
1. Lakukan hal yang sama seperti di atas dengan App Name: `project-service`.
2. Gunakan image `project-service:latest` dari ACR.
3. Ingress: **Enabled**, Ingress type: **Internal**, Target Port: **8000**.
4. Environment Variables:
   - Gunakan `DB_DATABASE`: `tubes_cloud_project`.
   - Pastikan `JWT_SECRET` bernilai **sama** dengan yang digunakan di `auth-service`.

#### C. Deploy `gateway-frontend`
1. Konfigurasikan App Name: `gateway-frontend`.
2. Gunakan image `gateway-frontend:latest` dari ACR.
3. Ingress:
   - Aktifkan Ingress: **Enabled**
   - Ingress type: **External** (Menerima lalu lintas dari internet)
   - Target Port: **8000**
4. Environment Variables:
   - `APP_KEY`: *[Kunci Laravel Gateway]*
   - `APP_ENV`: `production`
   - `SESSION_DRIVER`: `cookie` (Sangat disarankan agar session stateless & aman di cloud)
   - `AUTH_SERVICE_URL`: `http://auth-service.internal.<nama-env>.<lokasi>.azurecontainerapps.io/api` *(Gunakan URL internal FQDN milik auth-service dari portal)*
   - `PROJECT_SERVICE_URL`: `http://project-service.internal.<nama-env>.<lokasi>.azurecontainerapps.io/api` *(Gunakan URL internal FQDN milik project-service)*

### Langkah 5: Menjalankan Migrasi Database di Azure
Setelah container berjalan, jalankan migrasi database sekali saja ke masing-masing database Azure Flexible Server:
1. Buka Container App `auth-service` di Azure Portal.
2. Buka menu **Console** di bawah panel Monitoring.
3. Pilih container `auth-service`, ketik `/bin/sh` (atau `/bin/ash`), dan jalankan perintah migrasi:
   ```bash
   php artisan migrate --force
   ```
4. Ulangi langkah yang sama pada Container App `project-service`.

Sistem kini siap diakses secara online melalui URL eksternal yang disediakan oleh Container App `gateway-frontend`!
