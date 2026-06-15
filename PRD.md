# Product Requirement Document (PRD)
## Sistem Manajemen Freelance Project (TUBES CLOUD COMPUTING)

---

### 1. Ringkasan Produk (Product Summary)

Sistem Manajemen Freelance Project adalah platform berbasis web dengan arsitektur *microservices* yang dirancang untuk membantu freelancer mengelola proyek, melacak kemajuan per klien, memantau milestone & deadline, mencatat status pembayaran, serta menghasilkan faktur (*invoice generator*) secara otomatis.

* **Target Pengguna:** Freelancer (sebagai pemilik data/User) dan Administrator Sistem.
* **Arsitektur Teknis:** *Microservices* berbasis Laravel (Blade/Tailwind untuk Frontend & Gateway, JWT untuk Autentikasi, dan RESTful API untuk Project Management).

---

### 2. Manajemen Peran & Hak Akses (RBAC)

Sistem menerapkan Role-Based Access Control (RBAC) yang ketat melalui validasi token JWT:

| Peran (Role) | Deskripsi Hak Akses |
| :--- | :--- |
| **Admin** | Memiliki akses penuh (*Full CRUD*) terhadap seluruh data di dalam sistem lintas pengguna untuk kebutuhan pengawasan, pemeliharaan, dan *logging*. |
| **User (Freelancer)** | Hanya memiliki akses *CRUD terbatas pada data milik sendiri* (proyek, klien, invoice, dan milestone yang mereka buat). Tidak dapat melihat data freelancer lain. |

---

### 3. Arsitektur Fitur & Kebutuhan Fungsional (25% Bobot Penilaian)

Fitur-fitur ini dibagi dan didistribusikan ke dalam beberapa services spesifik:

#### A. Gateway / Frontend Service (Port 80)
* **UI/UX Responsif:** Antarmuka berbasis Tailwind CSS yang bersih untuk manajemen dashboard.
* **Routing & Load Balancing:** Meneruskan request dari client browser ke service yang tepat.
* **Manajemen Sesi:** Menyimpan token JWT di dalam *Cookie HttpOnly* demi keamanan tingkat tinggi.

#### B. Auth Service (Port 8001)
* **Registrasi & Login:** Menggunakan kredensial email dan password.
* **Generasi Token:** Menghasilkan token JWT yang membawa klaim identitas (`user_id`) dan peran (`role`).
* **User Database:** Mengelola penyimpanan data kredensial pengguna (PostgreSQL).

#### C. Project Service (Port 8002)
Layanan inti yang menangani data bisnis (CRUD) dengan validasi input yang ketat:
* **Manajemen Proyek & Klien:**
  * Membuat, membaca, memperbarui, dan menghapus data proyek serta profil klien.
  * Melacak status proyek (Pipeline: *Pitching*, *In Progress*, *Review*, *Completed*).
* **Pelacakan Milestone & Deadline:**
  * Pencatatan target jangka pendek (milestone) di setiap proyek beserta tenggat waktunya.
* **Modul Keuangan & Invoice Generator:**
  * Mencatat nilai proyek dan status pembayaran (*Unpaid*, *Partially Paid*, *Paid*).
  * Pembuatan invoice (faktur) otomatis berbasis data proyek yang dapat diunduh atau dilihat oleh klien.
* **Project Database:** Penyimpanan terpisah khusus data bisnis (PostgreSQL).

---

### 4. Alur Autentikasi & Keamanan (10% Bobot Penilaian)

Setiap pertukaran data wajib mengikuti protokol keamanan berikut:

1. **Autentikasi:** User melakukan login melalui Gateway $\rightarrow$ Forward ke Auth Service $\rightarrow$ Validasi kredensial $\rightarrow$ Generate JWT Token.
2. **Penyimpanan:** Token dikembalikan ke Gateway dan disimpan dengan aman di sisi client menggunakan *HttpOnly Cookie* untuk mencegah serangan XSS (*Cross-Site Scripting*).
3. **Validasi Request:** Setiap kali User mengakses data proyek (Port 8002), Gateway meneruskan JWT di dalam header. Project Service akan memverifikasi signature JWT dan memeriksa klaim role sebelum mengizinkan aksi CRUD.

---

### 5. Penanganan Error & Logging (10% Bobot Penilaian)

Untuk memastikan sistem stabil dan mudah dilacak saat terjadi kegagalan:

* **Format Respon Error Konsisten:** Semua service wajib mengembalikan format JSON yang seragam saat terjadi error, misalnya:
  ```json
  {
    "success": false,
    "message": "Validation failed",
    "errors": { 
      "deadline": ["The deadline field is required."] 
    }
  }
  ```
* **Kode Status HTTP yang Tepat:**
  * `401 Unauthorized` (Token kedaluwarsa atau tidak ada).
  * `403 Forbidden` (User mencoba mengakses proyek milik orang lain atau Admin area).
  * `422 Unprocessable Entity` (Gagal validasi input form).
* **Logging:** Pemanfaatan logging internal (seperti Laravel Log channel) untuk mencatat aktivitas error krusial di setiap service Container.

---

### 6. Rencana Kesiapan Deployment ke Azure (15% Bobot Penilaian)

Seluruh arsitektur yang terhubung via Docker Network lokal akan dimigrasikan ke infrastruktur Cloud Azure dengan skema berikut:

* **Containerization:** Gateway, Auth Service, dan Project Service masing-masing dibungkus ke dalam Docker Image.
* **Azure Container Registry (ACR):** Digunakan sebagai repositori privat untuk menyimpan Docker Images yang siap pakai.
* **Azure App Service / Azure Container Apps:** Tempat menjalankan container secara live dengan URL publik yang aktif.
* **Security & Environment Management:**
  * Semua data sensitif seperti database credentials, JWT Secret key, dan API keys **wajib disembunyikan** dari source code.
  * Variabel tersebut akan diinjeksikan secara aman melalui fitur *Application Settings / Environment Variables* atau *Azure Key Vault* (Secrets aman).

---

### 7. Kualitas Kode & Struktur Repositori (10% Bobot Penilaian)

* **Penerapan Konsep Modular:** Memisahkan logika bisnis (Controller/Service pattern) dari skema database.
* **Kode Bersih:** Wajib menyertakan komentar yang jelas pada fungsi-fungsi kompleks (terutama pada logika kalkulasi invoice dan validasi token).
* **Dokumentasi:** Repositori wajib dilengkapi dengan file `README.md` yang memuat:
  * Panduan instalasi lokal menggunakan docker-compose.
  * Daftar Endpoint API (API Documentation/Postman Collection).
  * Langkah-langkah deployment ke Azure.
