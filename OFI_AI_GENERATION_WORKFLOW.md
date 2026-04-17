# OFI AI Generation Workflow

## Tujuan

Dokumen ini menjelaskan rancangan menyeluruh untuk fitur Generate OFI berbasis AI pada aplikasi AssessMe.

Pendekatan yang dipilih adalah model dua tombol:

1. `Generate Template`
2. `Generate AI`

Model ini dipilih untuk menjaga kehati-hatian, kemudahan operasional, dan kestabilan fitur existing.

## Prinsip Desain

1. Fitur existing tidak boleh rusak.
2. Jalur template dan jalur AI harus dipisahkan dengan jelas.
3. UI tetap sederhana dan tidak berubah drastis.
4. Hasil AI harus dapat diaudit.
5. Sistem harus tetap bisa bekerja walaupun provider AI gagal.
6. Output AI harus tervalidasi, bukan HTML bebas.

## Kondisi Saat Ini

Saat ini OFI dihasilkan dengan logika template atau rule-based.

Alur existing:

1. Tombol `Generate` di modal OFI memanggil JavaScript `generateAutoOFI()`.
2. Frontend melakukan `POST` ke endpoint generate OFI.
3. Backend menghitung current level dan target level.
4. Backend mengambil activity yang belum memenuhi threshold kepatuhan.
5. Backend membangun OFI dalam bentuk deskripsi HTML.
6. Hasil disimpan ke tabel `assessment_ofis` sebagai OFI `type=auto`.

Artinya, implementasi saat ini belum menggunakan AI.

## Target Arsitektur Baru

Fitur OFI akan memiliki dua jalur generate yang berbeda:

### 1. Generate Template

Jalur ini mempertahankan logika existing.

Karakteristik:

1. Cepat.
2. Murah.
3. Stabil.
4. Tidak membutuhkan API eksternal.
5. Menjadi baseline rekomendasi.

### 2. Generate AI

Jalur ini menggunakan model AI untuk menghasilkan rekomendasi OFI yang lebih kaya, kontekstual, dan naratif.

Karakteristik:

1. Memakai provider AI seperti Gemini atau DeepSeek.
2. Menggunakan konteks assessment sebagai input.
3. Menghasilkan output JSON terstruktur.
4. Tetap disimpan sebagai OFI auto agar UI existing tetap kompatibel.

## Perubahan UI

Di modal OFI, tombol generate akan dipecah menjadi dua:

1. `Generate Template`
2. `Generate AI`

Tujuan pemisahan ini:

1. User dapat memilih jalur yang diinginkan.
2. User tidak bingung apakah hasil berasal dari AI atau template.
3. Sistem lebih aman untuk tahap rollout.
4. Jalur template tetap tersedia jika AI bermasalah.

## Alur Kerja End-to-End

### A. Alur Generate Template

1. User membuka modal OFI.
2. User menekan tombol `Generate Template`.
3. Frontend memanggil endpoint template.
4. Backend menjalankan generator template yang sekarang.
5. Backend menyimpan hasil sebagai OFI auto.
6. Frontend me-refresh daftar OFI.

### B. Alur Generate AI

1. User membuka modal OFI.
2. User menekan tombol `Generate AI`.
3. Frontend memanggil endpoint AI.
4. Backend mengumpulkan seluruh konteks assessment yang relevan.
5. Backend membangun payload prompt terstruktur.
6. Backend memanggil provider AI aktif.
7. Backend menerima output JSON dari AI.
8. Backend memvalidasi struktur dan isi output.
9. Backend mengubah output JSON menjadi format OFI yang dipakai aplikasi.
10. Backend menyimpan hasil sebagai OFI auto dengan metadata AI.
11. Frontend me-refresh daftar OFI.

## Arsitektur Backend Yang Disarankan

### Controller Layer

Controller hanya bertugas sebagai orchestration entry point.

Method yang disarankan:

1. `generateTemplateOFI()`
2. `generateAiOFI()`

Tanggung jawab controller:

1. Validasi akses.
2. Ambil assessment dan GAMO objective.
3. Panggil service yang sesuai.
4. Kembalikan response JSON ke frontend.

### Service Layer

Service menjadi inti logika fitur.

Struktur yang disarankan:

1. `OfiTemplateGenerationService`
2. `OfiAiGenerationService`
3. `OfiPromptBuilder`
4. `OfiResponseValidator`
5. `OfiPersistenceService`

Tanggung jawab masing-masing:

#### OfiTemplateGenerationService

1. Menjalankan logika existing.
2. Menghasilkan baseline rekomendasi template.
3. Bisa dipakai sendiri atau menjadi input tambahan untuk AI.

#### OfiAiGenerationService

1. Mengumpulkan data konteks.
2. Memilih provider aktif.
3. Memanggil provider AI.
4. Menangani error dan fallback.

#### OfiPromptBuilder

1. Menyusun payload yang konsisten.
2. Memisahkan logika prompt dari controller.
3. Memudahkan versioning prompt.

#### OfiResponseValidator

1. Memastikan output AI berbentuk JSON valid.
2. Memastikan field wajib tersedia.
3. Menolak output yang tidak sesuai format.

#### OfiPersistenceService

1. Menyimpan hasil template.
2. Menyimpan hasil AI.
3. Menyimpan metadata sumber hasil.

### Provider Layer

Diperlukan abstraction agar provider AI bisa diganti tanpa mengubah business logic.

Kontrak interface yang disarankan:

`OfiAiProviderInterface`

Method utama yang disarankan:

1. `generate(array $payload): array`

Implementasi provider yang bisa dibuat:

1. `GeminiOfiProvider`
2. `DeepSeekOfiProvider`

Manfaat abstraction ini:

1. Mudah mengganti provider.
2. Mudah menambah provider baru.
3. Mudah melakukan testing.

## Payload Input Ke AI

AI tidak boleh diberi input bebas tanpa struktur. Payload harus konsisten.

Contoh struktur input:

```json
{
  "assessment": {
    "id": 123,
    "code": "ASM-001",
    "title": "Assessment 2026"
  },
  "gamo": {
    "id": 10,
    "code": "APO01",
    "name": "Managed I&T Management Framework"
  },
  "capability": {
    "current_level": 2,
    "target_level": 4,
    "gap": 2
  },
  "activities": [
    {
      "question_id": 1001,
      "code": "APO01.01.A3",
      "name": "Example activity",
      "translated_text": "Contoh aktivitas",
      "weight": 1,
      "capability_rating": "P",
      "capability_score": 2.5,
      "compliance_percentage": 62.5,
      "guidance": "Example guidance",
      "document_requirements": "Example document"
    }
  ],
  "baseline_template": {
    "title": "Rekomendasi Peningkatan Level 2 ke Level 4",
    "recommendations": [
      {
        "activity_code": "APO01.01.A3",
        "activity_name": "Example activity",
        "current_compliance": 62.5,
        "level": 3
      }
    ]
  }
}
```

## Output Yang Diminta Dari AI

AI harus diminta mengembalikan JSON terstruktur, bukan HTML.

Contoh struktur output:

```json
{
  "title": "Rekomendasi Peningkatan Kapabilitas APO01 dari Level 2 ke Level 4",
  "summary": "Fokus perbaikan perlu diarahkan pada penguatan aktivitas tata kelola yang belum mencapai tingkat largely achieved.",
  "priority": "high",
  "rationale": "Gap level sebesar 2 menunjukkan kebutuhan percepatan pada aktivitas yang menjadi prasyarat level berikutnya.",
  "recommendations": [
    {
      "activity_code": "APO01.01.A3",
      "issue": "Kepatuhan aktivitas masih rendah dan belum menunjukkan konsistensi penerapan.",
      "recommended_action": "Susun prosedur, tetapkan PIC, dan lakukan review periodik atas implementasi kontrol.",
      "expected_evidence": "SOP, notulen review, daftar PIC, hasil monitoring.",
      "priority": "high"
    }
  ]
}
```

## Prompt Strategy

Prompt harus ketat agar output AI tetap terarah.

Aturan prompt yang disarankan:

1. Model berperan sebagai konsultan COBIT 2019.
2. Rekomendasi harus spesifik terhadap activity yang bermasalah.
3. Rekomendasi harus actionable.
4. Rekomendasi harus menyebut artefak atau evidence yang relevan.
5. Output wajib JSON valid.
6. Dilarang mengembalikan markdown atau HTML.

Contoh intent prompt:

1. Analisis gap current level dan target level.
2. Gunakan baseline template sebagai referensi, bukan satu-satunya sumber.
3. Prioritaskan activity dengan compliance rendah.
4. Buat rekomendasi yang dapat dijalankan organisasi.
5. Hasilkan output JSON sesuai skema yang ditentukan.

## Konfigurasi Provider

Konfigurasi sebaiknya diletakkan di `config/services.php` dan `.env`.

Contoh konsep konfigurasi:

```php
'ofi_ai' => [
    'enabled' => env('OFI_AI_ENABLED', false),
    'default_provider' => env('OFI_AI_DEFAULT_PROVIDER', 'gemini'),
    'timeout' => env('OFI_AI_TIMEOUT', 30),
],

'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-2.5-pro'),
],

'deepseek' => [
    'api_key' => env('DEEPSEEK_API_KEY'),
    'base_url' => env('DEEPSEEK_BASE_URL'),
    'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
],
```

## Penyimpanan Dan Metadata

Hasil AI sebaiknya tetap disimpan ke tabel OFI yang sama agar UI tidak berubah besar.

Namun perlu metadata tambahan untuk audit.

Kolom yang disarankan:

1. `generation_source` dengan nilai `template` atau `ai`
2. `generation_provider`
3. `generation_model`
4. `prompt_version`
5. `fallback_used`
6. `generation_payload_hash` jika dibutuhkan

Manfaat metadata ini:

1. Mengetahui asal hasil generate.
2. Memudahkan debugging.
3. Memudahkan evaluasi kualitas AI.
4. Mendukung audit trail.

## Strategi Fallback

Fallback sangat penting.

Skenario fallback yang disarankan:

### Untuk Generate Template

Tidak membutuhkan fallback eksternal karena ini adalah baseline utama.

### Untuk Generate AI

Jika terjadi salah satu kondisi berikut:

1. API provider timeout
2. API provider error
3. Output bukan JSON valid
4. Field wajib tidak lengkap

Maka perilaku yang disarankan:

1. Request AI dinyatakan gagal.
2. User diberi pesan yang jelas.
3. User diarahkan memakai `Generate Template`.

Untuk fase awal rollout, fallback otomatis dari AI ke template sebaiknya tidak langsung menyimpan hasil template sebagai hasil AI, karena itu bisa membingungkan user.

## Logging Dan Observability

Agar fitur ini mudah dipelihara, perlu logging minimal pada event berikut:

1. Request generate AI dimulai.
2. Provider yang dipilih.
3. Waktu respons provider.
4. Validasi output berhasil atau gagal.
5. Penyimpanan OFI berhasil atau gagal.

Data sensitif seperti API key tidak boleh pernah ditulis ke log.

## Fase Implementasi

### Fase 1

1. Pecah tombol Generate menjadi dua tombol di modal OFI.
2. Pisahkan endpoint template dan AI.
3. Pertahankan generator template existing.

### Fase 2

1. Tambah konfigurasi provider di `services.php` dan `.env`.
2. Buat service AI dan provider abstraction.
3. Buat prompt builder.

### Fase 3

1. Tambah validator output AI.
2. Simpan hasil AI ke OFI.
3. Tambah metadata generation.

### Fase 4

1. Tambah logging.
2. Tambah fallback handling.
3. Uji hasil generate pada beberapa GAMO dan variasi gap level.

## Rekomendasi Implementasi Awal

Untuk implementasi pertama, pendekatan paling aman adalah:

1. Pertahankan generator template sebagai fitur utama yang sudah stabil.
2. Tambahkan jalur `Generate AI` secara terpisah.
3. Gunakan satu provider dulu, jangan dua sekaligus.
4. Gunakan output JSON yang tervalidasi.
5. Simpan metadata sumber hasil.

Jika ingin memilih provider pertama:

1. Gemini cocok jika ingin kualitas narasi dan reasoning yang lebih kuat.
2. DeepSeek cocok jika ingin biaya cenderung lebih hemat dan integrasi sederhana.

## Ringkasan Keputusan

Keputusan desain untuk fitur ini adalah sebagai berikut:

1. OFI akan memiliki dua tombol generate.
2. Jalur template tetap dipertahankan.
3. Jalur AI ditambahkan sebagai opsi terpisah.
4. Backend memakai service abstraction.
5. Output AI wajib JSON terstruktur.
6. Hasil tetap disimpan ke tabel OFI existing.
7. Metadata generation perlu ditambahkan.
8. Kegagalan AI tidak boleh merusak jalur template.

Dokumen ini menjadi acuan implementasi tahap berikutnya.