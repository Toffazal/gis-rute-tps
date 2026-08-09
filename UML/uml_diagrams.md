# Dokumentasi Diagram UML & DFD (Mermaid.js)

Dokumen ini berisi kode sintaks Mermaid.js untuk membuat diagram UML (_Use Case, Class, Activity, Sequence_) dan DFD (_Context Diagram & DFD Level 1_) dari sistem GIS Optimasi Rute Pengangkutan Sampah menggunakan Ant Colony Optimization (ACO).

Anda dapat menyalin (_copy_) kode di bawah ini dan menempelkannya langsung ke [Mermaid Live Editor](https://mermaid.live/) untuk menghasilkan diagram visualnya guna kebutuhan penulisan Bab 4 Skripsi.

---

## 1. Use Case Diagram

Diagram ini menggambarkan interaksi antara dua aktor utama (_Admin_ dan _Sopir_) dengan fungsionalitas sistem.

```mermaid
flowchart LR
    Admin[Admin]

    subgraph System ["Sistem GIS TPS & ACO"]
        UC_Login((Login Sistem))

        subgraph Admin_Features ["Fitur Kelola Admin"]
            direction TB
            UC_MasterTPS((Kelola Data TPS))
            UC_MasterAngkutan((Kelola Data Angkutan))
            UC_MasterSopir((Kelola Data Sopir))
            UC_Jadwal((Kelola Jadwal & Import/Export))
            UC_Website((Kelola Koordinat Kantor & TPA))
            UC_DashboardAdmin((Pantau Dashboard & Grafik Statistik))
            UC_MonitorRealtime((Pantau Live Rute & Timeline Sopir))
        end

        subgraph Sopir_Features ["Fitur Jalan Sopir"]
            direction TB
            UC_JadwalSopir((Lihat Jadwal Harian))
            UC_MulaiRute((Eksekusi Rute: Mulai Rute))
            UC_TibaSelesai((Eksekusi Rute: Tiba & Selesai))
            UC_SkipTPS((Eksekusi Rute: Lompati TPS dengan Alasan))
            UC_Prioritas((Eksekusi Rute: Prioritas TPS Manual))
            UC_SelesaiTPA((Eksekusi Rute: Selesai Rute di TPA))
            UC_RiwayatSopir((Lihat Riwayat Rute Sendiri))
        end
    end

    Sopir[Sopir]

    %% Hubungan Admin
    Admin --> UC_Login
    Admin --> UC_MasterTPS
    Admin --> UC_MasterAngkutan
    Admin --> UC_MasterSopir
    Admin --> UC_Jadwal
    Admin --> UC_Website
    Admin --> UC_DashboardAdmin
    Admin --> UC_MonitorRealtime

    %% Hubungan Sopir
    Sopir --> UC_Login
    Sopir --> UC_JadwalSopir
    Sopir --> UC_MulaiRute
    Sopir --> UC_TibaSelesai
    Sopir --> UC_SkipTPS
    Sopir --> UC_Prioritas
    Sopir --> UC_SelesaiTPA
    Sopir --> UC_RiwayatSopir
```

---

## 2. Class Diagram

Diagram ini memetakan kelas-kelas model Eloquent Laravel beserta relasi database dan method layanan algoritma ACO.

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string username
        +string email
        +string password
        +string role
        +ruteHarian() HasMany
        +casts() array
    }

    class Tps {
        +int id
        +string nama
        +double latitude
        +double longitude
        +string alamat
        +ruteTujuan() HasMany
    }

    class Angkutan {
        +int id
        +string nama
        +string nomor
        +ruteHarian() HasMany
    }

    class Jadwal {
        +int id
        +int user_id
        +int tps_id
        +int angkutan_id
        +string hari
        +user() BelongsTo
        +tps() BelongsTo
        +angkutan() BelongsTo
    }

    class RuteHarian {
        +int id
        +int user_id
        +int angkutan_id
        +date tanggal
        +string status
        +datetime waktu_mulai
        +datetime waktu_selesai
        +int total_jarak_meter
        +int total_durasi_detik
        +user() BelongsTo
        +angkutan() BelongsTo
        +ruteTujuan() HasMany
        +ruteLog() HasMany
    }

    class RuteTujuan {
        +int id
        +int rute_harian_id
        +int tps_id
        +string tipe
        +int urutan
        +string status
        +string alasan
        +int jarak_dari_sebelumnya_meter
        +datetime waktu_tiba
        +datetime waktu_selesai
        +ruteHarian() BelongsTo
        +tps() BelongsTo
        +ruteLog() HasMany
    }

    class RuteLog {
        +int id
        +int rute_harian_id
        +int rute_tujuan_id
        +string aksi
        +string alasan
        +ruteHarian() BelongsTo
        +ruteTujuan() BelongsTo
    }

    class Website {
        +int id
        +string nama
        +string logo
        +string koordinat_kantor
        +string koordinat_tpa
    }

    class MatriksJarak {
        +int id
        +string asal_tipe
        +int asal_id
        +string tujuan_tipe
        +int tujuan_id
        +int jarak_meter
    }

    class AcoService {
        -float alpha
        -float beta
        -float rho
        -float Q
        -int antCount
        -int iterations
        +optimize(kantorCoord, tpaCoord, tpsList, startNodeInfo) array
        +getJarakDirect(nodeFrom, nodeTo) int
        -constructTour(distMatrix, pheromone, n, totalNodes) array
        -calculateTourDistance(tour, distMatrix) float
        -buildDistanceMatrix(startNodeInfo, tpaCoord, tpsList) array
        -getJarak(asalTipe, asalId, tujuanTipe, tujuanId, coordAsal, coordTujuan) int
        -haversine(lat1, lon1, lat2, lon2) int
        -buildResult(bestTour, bestDistance, tpsList, distMatrix, startNodeInfo) array
    }

    %% Relasi Asosiasi
    User "1" --> "0..*" RuteHarian : Memiliki
    User "1" --> "0..*" Jadwal : Memiliki
    Tps "1" --> "0..*" RuteTujuan : Memiliki
    Tps "1" --> "0..*" Jadwal : Memiliki
    Angkutan "1" --> "0..*" RuteHarian : Memiliki
    Angkutan "1" --> "0..*" Jadwal : Memiliki
    RuteHarian "1" --> "0..*" RuteTujuan : Memiliki
    RuteHarian "1" --> "0..*" RuteLog : Memiliki
    RuteTujuan "1" --> "0..*" RuteLog : Memiliki

    %% Dependensi
    AcoService ..> MatriksJarak : Query jarak
    AcoService ..> RuteHarian : Dipanggil controller
```

---

## 3. Activity Diagram (Eksekusi Rute & Dinamika ACO)

Diagram alur ini menggambarkan proses sopir memulai pekerjaan, bagaimana ACO mengoptimasi rute awal, serta alur percabangan ketika sopir melakukan _skip_ rute atau reorder prioritas rute secara manual.

```mermaid
flowchart TD
    Start([Mulai]) --> Login[Sopir Login ke Aplikasi]
    Login --> Dashboard[Buka Dashboard Sopir]
    Dashboard --> CheckRoute{Apakah Rute Harian<br>Hari Ini Sudah Ada?}

    %% Cabang Rute Belum Ada
    CheckRoute -- Belum Ada --> GetJadwal[Ambil Jadwal TPS & Angkutan Hari Ini]
    GetJadwal --> RunACO[Jalankan ACO untuk Mengurutkan TPS Secara Optimal]
    RunACO --> CreateRoute[Buat Rute Harian & Rute Tujuan di Database]
    CreateRoute --> LoadMap[Tampilkan Jalur Rute Teroptimasi di Peta Leaflet]

    %% Cabang Rute Sudah Ada
    CheckRoute -- Sudah Ada --> LoadMap

    LoadMap --> ActionMulai[Klik 'Mulai Rute']
    ActionMulai --> StatusBerlangsung[Status Rute: 'Berlangsung' & TPS Pertama: 'Aktif']

    StatusBerlangsung --> Drive[Sopir Mengemudi ke Lokasi Aktif]
    Drive --> ChooseAction{Sopir Mengambil Tindakan}

    %% Tindakan A: Tiba & Selesai
    ChooseAction -- Tiba & Selesai --> ActionTiba[Klik 'Tiba di Lokasi']
    ActionTiba --> LogTiba[Sistem Catat Waktu Tiba & Log Tiba]
    LogTiba --> ActionSelesai[Klik 'Selesai']
    ActionSelesai --> StatusSelesai[Status TPS Update menjadi 'Selesai']
    StatusSelesai --> ActivateNext[Aktifkan TPS Urutan Berikutnya]
    ActivateNext --> CheckNext{Apakah Masih Ada<br>TPS Berikutnya?}

    %% Tindakan B: Skip TPS
    ChooseAction -- Lompati (Skip) --> ActionSkip[Klik 'Lewati']
    ActionSkip --> InputAlasan[Input Alasan Lewati & Pilih Jenis Skip]
    InputAlasan --> CheckSkipType{Pilihan Jenis Skip?}

    CheckSkipType -- Dilewati (Kunjungi Nanti) --> SkipLater[Status: 'Dilewati' & Duplikat TPS ke Urutan 999]
    SkipLater --> ReoptimizeACO[Jalankan ACO Re-optimasi Sisa TPS]
    ReoptimizeACO --> ActivateNext

    CheckSkipType -- Tidak Dikunjungi (Selesai) --> SkipPerm[Status: 'Tidak Dikunjungi']
    SkipPerm --> ActivateNext

    %% Tindakan C: Prioritas Manual
    ChooseAction -- Prioritas Manual --> ManualSelect[Pilih TPS Lain Secara Manual]
    ManualSelect --> ReorderACO[Jalankan ACO: Posisikan TPS Terpilih ke Urutan Pertama Setelah Selesai]
    ReorderACO --> StatusBerlangsung

    %% Check Next Loop
    CheckNext -- Ya --> Drive
    CheckNext -- Tidak --> TargetTPA[Sopir Bergerak Menuju TPA]

    TargetTPA --> TibaTPA[Sopir Tiba di TPA & Selesai Bongkar Sampah]
    TibaTPA --> SelesaiRute[Sopir Klik 'Selesai Rute']
    SelesaiRute --> EndRoute[Rute Harian Selesai, Hitung Total Jarak & Durasi Positif]
    EndRoute --> Selesai([Selesai])
```

---

## 4. Sequence Diagram (Generasi & Optimasi Rute ACO)

Diagram interaksi ini berfokus pada alur pemanggilan data dan penghitungan jalur terpendek ketika sopir membuka halaman dashboard pertama kali.

```mermaid
sequenceDiagram
    autonumber
    actor Sopir as Sopir
    participant DC as DashboardController (Sopir)
    participant ACO as AcoService
    participant DB as Database

    Sopir->>DC: Buka Dashboard (/sopir)
    activate DC
    DC->>DB: Query RuteHarian tanggal hari ini
    DB->>DC: Return null (Belum ada rute hari ini)
    DC->>DB: Query Jadwal Sopir berdasarkan hari ini
    DB->>DC: Return Daftar TPS & Angkutan

    DC->>ACO: optimize(kantorCoord, tpaCoord, tpsList)
    activate ACO
    ACO->>ACO: buildDistanceMatrix()

    loop Perhitungan Matriks Jarak (Setiap Pasang Node)
        ACO->>DB: Query MatriksJarak (asal, tujuan)
        alt Jika data jarak ditemukan
            DB->>ACO: Return jarak_meter
        else Jika data jarak tidak ditemukan
            ACO->>ACO: Hitung haversine(coordAsal, coordTujuan)
        end
    end

    ACO->>ACO: Inisialisasi Matriks Pheromone
    loop Iterasi ACO (50 Iterasi)
        ACO->>ACO: Semut membangun jalur kunjungan (constructTour)
        ACO->>ACO: Hitung total jarak jalur (calculateTourDistance)
        ACO->>ACO: Deposit & Evaporasi Pheromone
    end

    ACO->>DC: Return ['urutan' => TPS[], 'total_jarak' => ...]
    deactivate ACO

    DC->>DB: RuteHarian::create(...)
    DC->>DB: RuteTujuan::create(...) (Urutan TPS teroptimasi ACO)
    DC->>DB: RuteTujuan::create(...) (TPA sebagai urutan terakhir)
    DC->>Sopir: Kirim views dashboard & Map Leaflet Rute Teroptimasi
    deactivate DC
```

---

## 5. DFD (Data Flow Diagram)

Menyajikan alur aliran data dari entitas luar ke dalam proses sistem secara menyeluruh.

### DFD Level 0 (Context Diagram)

Menggambarkan batasan sistem secara makro serta aliran data utama antara sistem dengan _Admin_ dan _Sopir_.

```mermaid
flowchart TD
    subgraph Context_Diagram ["DFD Level 0 (Context Diagram)"]
        Admin[Admin]
        Sopir[Sopir]
        System[[Sistem GIS TPS & ACO]]

        %% Aliran Data Admin
        Admin -->|1. Data Master TPS, Angkutan, Sopir| System
        Admin -->|2. Data Jadwal & Koordinat Website| System
        System -->|3. Dashboard Statistik & Grafik| Admin
        System -->|4. Laporan Riwayat & Live Map Monitoring| Admin

        %% Aliran Data Sopir
        Sopir -->|5. Data Login & Aksi Perjalanan Rute| System
        Sopir -->|6. Input Alasan Skip & Prioritas Manual| System
        System -->|7. Peta Jalur Rute Teroptimasi| Sopir
        System -->|8. Info Jadwal & Riwayat Rute Sendiri| Sopir
    end
```

### DFD Level 1

Menggambarkan sub-proses utama di dalam sistem dan hubungannya dengan penyimpanan data (_Data Store_).

```mermaid
flowchart TD
    subgraph DFD_Level_1 ["DFD Level 1"]
        %% Entitas
        Ent_Admin[Admin]
        Ent_Sopir[Sopir]

        %% Proses
        P1[[1.0 Kelola Data Master]]
        P2[[2.0 Kelola Jadwal Pengangkutan]]
        P3[[3.0 Optimasi Rute - ACO Engine]]
        P4[[4.0 Eksekusi Perjalanan Rute]]
        P5[[5.0 Pemantauan & Riwayat]]

        %% Data Stores
        DS1[(D1: TPS)]
        DS2[(D2: Angkutan)]
        DS3[(D3: Users)]
        DS4[(D4: Jadwal)]
        DS5[(D5: Rute Harian & Tujuan)]
        DS6[(D6: Rute Log)]
        DS7[(D7: Matriks Jarak)]
        DS8[(D8: Website Settings)]

        %% Proses 1.0
        Ent_Admin -->|Input TPS, Angkutan, Sopir| P1
        P1 -->|Simpan/Update| DS1
        P1 -->|Simpan/Update| DS2
        P1 -->|Simpan/Update| DS3
        P1 -->|Simpan/Update| DS8
        P1 -->|Data Master Terkini| Ent_Admin

        %% Proses 2.0
        Ent_Admin -->|Input/Import Jadwal| P2
        P2 -->|Simpan Jadwal| DS4
        P2 -->|Baca Detail TPS & Angkutan| DS1 & DS2
        P2 -->|Tampilkan Jadwal| Ent_Admin

        %% Proses 3.0 (ACO)
        DS4 -->|Kirim Jadwal Hari Ini| P3
        DS8 -->|Kirim Koordinat Kantor & TPA| P3
        DS1 -->|Kirim Koordinat TPS| P3
        DS7 -->|Cek/Simpan Matriks Jarak| P3
        P3 -->|Simpan Rute Terurut| DS5

        %% Proses 4.0
        Ent_Sopir -->|Aksi Perjalanan: Mulai, Tiba, Selesai, Skip, Prioritas| P4
        P4 -->|Update Status Rute & Tujuan| DS5
        P4 -->|Simpan Log Perjalanan| DS6
        P4 -->|Trigger Re-optimasi Jika Skip atau Prioritas| P3
        DS5 -->|Tampilkan Jalur di Peta Leaflet| Ent_Sopir

        %% Proses 5.0
        DS5 & DS6 -->|Baca Rute & Log Perjalanan| P5
        P5 -->|Grafik Kinerja & Live Map Monitoring| Ent_Admin
        P5 -->|Riwayat Rute Sendiri| Ent_Sopir
    end
```
