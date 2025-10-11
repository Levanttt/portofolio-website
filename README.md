# Portfolio Website

Website portfolio modern untuk menampilkan karya, skill, dan kontak, dibangun dengan PHP, PostgreSQL, dan Tailwind CSS.

## Fitur

- **Landing Page** dengan animasi background interaktif (Vanta.js)
- **Section About**: Profil, bio, dan link download CV
- **Section Skills**: Daftar skill dengan ikon
- **Section Projects**: Showcase project (Game/Web/UIUX) dengan modal detail, media preview, dan filter
- **Section Contact**: Form kontak dan info sosial media
- **Admin Panel** (login protected):
  - CRUD Project (upload gambar, tag, role, featured)
  - Lihat & hapus pesan kontak
  - Statistik jumlah project, skill, pesan

## Struktur Folder

```
.
├── admin/          # Admin panel (login, CRUD project, pesan)
├── config/         # Konfigurasi database
├── includes/       # Komponen PHP (header, footer, functions)
├── public/         # Public web root (index.php, assets, images)
├── views/          # Halaman utama (home, about, projects, contact)
└── README.md
```

## Instalasi

1. **Clone repo ini**
2. **Buat database PostgreSQL** dan import struktur tabel (lihat contoh di bawah)
3. **Edit** `config/database.php` sesuai kredensial database kamu
4. **Jalankan di XAMPP/Laragon/Apache** (root di folder `public/`)
5. **Akses Admin Panel** di `/admin` (password default: `admin123`, bisa diubah hash-nya)

## Contoh Struktur Tabel PostgreSQL

```sql
CREATE TABLE profile (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(100),
    title VARCHAR(100),
    bio TEXT,
    profile_image VARCHAR(255),
    cv_url VARCHAR(255),
    email VARCHAR(100),
    github_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    itch_url VARCHAR(255)
);

CREATE TABLE projects (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100),
    description TEXT,
    year INT,
    category VARCHAR(20),
    tags TEXT[],
    roles TEXT[],
    image_url VARCHAR(255),
    project_url VARCHAR(255),
    github_url VARCHAR(255),
    demo_url VARCHAR(255),
    media_url VARCHAR(255),
    media_type VARCHAR(20),
    tech_stack TEXT[],
    features TEXT[],
    gallery_images TEXT[],
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE skills (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50),
    icon VARCHAR(50),
    category VARCHAR(50),
    proficiency INT
);

CREATE TABLE contact_messages (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT NOW()
);
```

## Konfigurasi

- **Database:** Edit `config/database.php`
- **Password Admin:** Hash di `admin/index.php` (gunakan [password_hash](https://www.php.net/manual/en/function.password-hash.php))
- **Assets:** CSS & JS ada di `public/assets/`

## Lisensi

MIT License

---

> Dibuat dengan ❤️ oleh IrfanLA