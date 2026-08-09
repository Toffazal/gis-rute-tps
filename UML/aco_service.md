# Analisis Algoritma Ant Colony Optimization (ACO) dan Mekanisme Perhitungan Jarak & Waktu

Dokumen ini berisi penjelasan teoritis dan teknis mengenai implementasi algoritma **Ant Colony Optimization (ACO)**, penentuan jarak (Distance Matrix), serta pengelolaan waktu perjalanan di dalam aplikasi GIS Rute TPS. Dokumen ini dirancang dengan gaya akademis yang dapat diadaptasi langsung ke dalam **Bab 3 (Metodologi Penelitian)** atau **Bab 4 (Analisis dan Hasil)** pada penulisan skripsi Anda.

---

## 1. Konsep Dasar Implementasi ACO pada Sistem
Algoritma *Ant Colony Optimization* (ACO) diadaptasi dari perilaku koloni semut asli dalam mencari makanan dari sarang menuju sumber makanan. Di dalam aplikasi ini:
* **Sarang (Titik Awal)** didefinisikan sebagai Kantor/Pool Truk Sampah (atau koordinat TPS terakhir jika dalam mode re-optimasi jalan).
* **Sumber Makanan (Titik Antara)** dianalogikan sebagai daftar **TPS** (Tempat Pembuangan Sementara) yang harus dikunjungi.
* **Tujuan Akhir** dianalogikan sebagai **TPA** (Tempat Pemprosesan Akhir) sebagai tempat pembuangan akhir sekaligus titik selesainya rute.
* **Jejak Pheromone ($\tau$)** adalah zat kimia yang ditinggalkan oleh semut di sepanjang jalur lintasan. Jalur yang lebih pendek akan dilewati semut dengan lebih cepat, mengumpulkan konsentrasi pheromone yang lebih tinggi, dan akhirnya menarik koloni semut berikutnya untuk memilih jalur tersebut.

---

## 2. Parameter Algoritma ACO
Penentuan performa dan kecepatan konvergensi algoritma ACO sangat dipengaruhi oleh parameter-parameter berikut:

### a. Konstanta Pengatur Jejak Pheromone ($\alpha$)
Parameter $\alpha$ (alpha) menentukan bobot relatif dari jejak pheromone yang tersisa pada suatu jalur.
* **Fungsi**: Mengatur intensitas pengaruh jejak semut terdahulu terhadap keputusan semut saat ini.
* **Nilai Sistem**: `1.0`
* **Dampak Akademis**: Jika $\alpha$ bernilai besar, semut akan sangat cenderung mengikuti jalur yang sudah sering dilewati sebelumnya (cepat konvergen ke solusi suboptimal/lokal optimum). Jika $\alpha = 0$, semut hanya akan memilih jalur berdasarkan jarak terdekat (komputasi serakah/*greedy search*).

### b. Konstanta Pengatur Visibilitas Jarak ($\beta$)
Parameter $\beta$ (beta) menentukan bobot relatif dari nilai heuristik jarak (visibilitas/kedekatan fisik antar lokasi).
* **Fungsi**: Mengatur tingkat pengaruh kedekatan fisik lokasi terhadap pilihan semut. Nilai heuristik didefinisikan sebagai kedekatan $\eta_{ij} = \frac{1}{d_{ij}}$, di mana $d_{ij}$ adalah jarak antara titik $i$ dan $j$.
* **Nilai Sistem**: `3.0`
* **Dampak Akademis**: Nilai $\beta$ yang lebih tinggi (`3.0`) berarti semut sangat mengutamakan jarak yang dekat dalam memilih tujuan berikutnya. Hal ini mencegah semut memilih rute acak yang terlalu jauh di awal iterasi.

### c. Tingkat Evaporasi Pheromone ($\rho$)
Parameter $\rho$ (rho) merepresentasikan laju penguapan pheromone dari waktu ke waktu ($0 \le \rho < 1$).
* **Fungsi**: Mengurangi akumulasi pheromone secara berkala agar jalur-jalur buruk yang sempat dilewati di iterasi awal tidak mendominasi keputusan semut di iterasi akhir.
* **Nilai Sistem**: `0.5` (50% pheromone menguap di setiap akhir iterasi).
* **Rumus Evaporasi**:
  $$\tau_{ij} \leftarrow (1 - \rho) \cdot \tau_{ij}$$

### d. Konstanta Deposit Pheromone ($Q$)
Konstanta $Q$ digunakan untuk menghitung jumlah pheromone baru yang diletakkan oleh semut di jalur yang dilaluinya.
* **Fungsi**: Semut yang berhasil menyelesaikan rute dengan total panjang $L_k$ akan menaruh pheromone baru sebesar $\Delta \tau_{ij}^k$ pada jalur yang dilewatinya.
* **Nilai Sistem**: `100.0`
* **Rumus Kontribusi Pheromone**:
  $$\Delta \tau_{ij}^k = \frac{Q}{L_k}$$
  *Di mana $L_k$ merupakan akumulasi total jarak tempuh semut $k$ dalam meter.*

### e. Jumlah Semut (*Ant Count*)
Jumlah agen pencari (semut) yang disimulasikan untuk menjelajahi grafik rute pada setiap iterasi tunggal.
* **Nilai Sistem**: `20 semut`
* **Dampak Akademis**: Semakin banyak semut, semakin luas eksplorasi alternatif jalur yang dilakukan, sehingga memperbesar peluang menemukan rute terpendek global (*global optimum*). Namun, beban kerja memori server akan meningkat secara linear sebanding dengan jumlah semut.

### f. Jumlah Iterasi (*Iterations*)
Batas pengulangan siklus pencarian rute oleh seluruh koloni semut.
* **Nilai Sistem**: `50 iterasi`
* **Dampak Akademis**: Pada setiap iterasi, 20 semut akan membangun rute dan memperbarui peta pheromone. Dengan 50 iterasi, total perjalanan semut yang disimulasikan adalah sebanyak $20 \times 50 = 1000$ kali perjalanan, menjamin kestabilan konvergensi menuju jalur terpendek.

---

## 3. Penentuan Jarak Antar Lokasi (*Distance Matrix*)
Sistem menerapkan **dua lapis penentuan jarak** untuk memastikan perhitungan rute tetap akurat dalam segala kondisi:

```
                  [ Mulai Ambil Jarak Titik A ke B ]
                                  |
                                  v
                 /---------------------------------\
                / Apakah Data Jarak A ke B Tersedia \
               <     di Tabel 'matriks_jarak'?       >
                \-----------------------------------/
                                  |
                   +--------------+--------------+
                   | Ya                          | Tidak
                   v                             v
       [ Ambil Nilai jarak_meter ]     [ Hitung secara matematis ]
       (Sesuai kondisi jalan asli)     (Menggunakan Rumus Haversine)
```

### Metode A: Query Tabel Database `matriks_jarak` (Prioritas)
Sebelum melakukan perhitungan, sistem memprioritaskan data jarak riil berdasarkan jalan raya (*road network distance*) yang telah disimpan pada tabel `matriks_jarak`. Data ini biasanya diperoleh melalui API eksternal (seperti Open Source Routing Machine / OSRM atau Google Maps).

### Metode B: Formula Haversine (Fallback)
Jika data koordinat belum memiliki pencatatan jarak di database, sistem menggunakan **Formula Haversine** untuk menghitung jarak lingkaran besar (*great-circle distance*) antara dua titik koordinat bola bumi (lintang dan bujur) secara langsung:

#### Rumus Matematika:
$$d = 2r \cdot \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta \phi}{2}\right) + \cos(\phi_1)\cos(\phi_2)\sin^2\left(\frac{\Delta \lambda}{2}\right)}\right)$$

Dimana:
* $d$ = Jarak antar dua titik lokasi (meter).
* $r$ = Jari-jari rata-rata bumi ($6.371.000$ meter).
* $\phi_1, \phi_2$ = Latitude titik asal dan tujuan (dalam radian).
* $\Delta \phi$ = Selisih latitude ($\phi_2 - \phi_1$ dalam radian).
* $\Delta \lambda$ = Selisih longitude ($\lambda_2 - \lambda_1$ dalam radian).

---

## 4. Penghitungan dan Pengelolaan Waktu Perjalanan (Durasi)
Untuk mengukur durasi waktu pengangkutan secara akurat dan menghindari eror matematis/zona waktu, sistem menggunakan mekanisme pencatatan waktu berikut:

### a. Struktur Aliran Waktu Rute
1. **Mulai Perjalanan (`waktu_mulai`)**: Disimpan dalam tipe data `datetime` ketika sopir menekan tombol "Mulai Rute" pada aplikasi handphone/device.
2. **Kunjungan TPS (`waktu_tiba` & `waktu_selesai`)**: Dicatat secara individual pada setiap TPS yang dikunjungi untuk menghitung durasi bongkar-muat sampah di lokasi tersebut.
3. **Selesai Perjalanan (`waktu_selesai`)**: Disimpan saat sopir tiba di TPA dan menyelesaikan seluruh tugas hariannya.

### b. Formula Perhitungan Durasi Perjalanan Positif
Selisih waktu dihitung menggunakan library `Carbon` di Laravel. Untuk menghindari nilai negatif akibat clock skew (pergeseran waktu lokal server) atau kesalahan pembanding waktu, sistem menghitung durasi menggunakan nilai absolut (`abs`) detik dan dikonversi ke tipe data integer:

$$\text{Durasi Perjalanan (Detik)} = \text{int}\left( \left| t_{\text{selesai}} - t_{\text{mulai}} \right| \right)$$

Di tingkat aplikasi (Blade Template), total detik yang disimpan di database tersebut dikonversi kembali ke format **Jam** dan **Menit** secara aman:
$$\text{Jam} = \lfloor \frac{\text{Durasi}}{3600} \rfloor$$
$$\text{Menit} = \lfloor \frac{\text{Durasi} \pmod{3600}}{60} \rfloor$$

---

## 5. Flowchart & Langkah Algoritma ACO pada Sistem
Berikut adalah proses sekuensial perhitungan rute yang dijalankan di dalam [AcoService.php](file:///c:/wamp64/www/KERJAAN%20PRIBADI/gis-rute-tps/app/Services/AcoService.php):

1. **Inisialisasi**: Input koordinat kantor, TPA, dan daftar TPS. Bangun matriks jarak ($distMatrix$).
2. **Setup Pheromone**: Atur seluruh nilai pheromone awal ($\tau_{ij}$) pada setiap sisi penghubung lokasi dengan nilai standar `1.0`.
3. **Loop Iterasi** ($iter = 1 \to 50$):
   * Untuk setiap semut ($ant = 1 \to 20$):
     * Semut diposisikan pada Kantor (Node 0).
     * Selama masih ada TPS yang belum dikunjungi:
       * Hitung probabilitas pemilihan TPS berikutnya ($j$) berdasarkan rumus:
         $$P_{ij} = \frac{[\tau_{ij}]^\alpha \cdot [\eta_{ij}]^\beta}{\sum_{k \in \text{Allowed}} [\tau_{ik}]^\alpha \cdot [\eta_{ik}]^\beta}$$
       * Gunakan metode *Roulette Wheel Selection* untuk menentukan TPS berikutnya berdasarkan probabilitas di atas.
     * Setelah semua TPS terlewati, semut diarahkan langsung menuju TPA (Node Akhir).
     * Hitung total jarak jalur ($L_k$) yang dibentuk semut tersebut.
     * **Evaluasi**: Jika total jarak $L_k$ lebih pendek dari rekor rute terbaik sebelumnya ($bestDistance$), simpan jalur semut ini sebagai *bestTour*.
   * **Evaporasi**: Kurangi pheromone di seluruh sisi jalur dengan laju evaporasi $\rho = 0.5$.
   * **Deposit**: Tambahkan pheromone baru pada sisi jalur yang dilewati oleh semut pada iterasi tersebut dengan rumus $\Delta \tau = \frac{100}{L_k}$.
4. **Output**: Mengirimkan urutan TPS terbaik (*bestTour*), total jarak optimal, dan detail jarak antar titik kembali ke controller untuk disimpan di database.
