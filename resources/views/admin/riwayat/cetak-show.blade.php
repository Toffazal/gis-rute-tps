<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cetak Detail Riwayat Rute Sopir' }}</title>
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ $website->logo ? asset('img/' . $website->logo) : asset('ample/plugins/images/favicon.png') }}">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            background-color: #e9ecef;
            margin: 0;
            padding: 20px 0;
            font-size: 10pt;
            line-height: 1.35;
        }

        /* Printable Sheet A4 */
        .sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 15mm 15mm 15mm;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        /* Action Toolbar */
        .no-print-toolbar {
            width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #343a40;
            color: #fff;
            padding: 10px 18px;
            border-radius: 6px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 13px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-print {
            background-color: #dc3545;
            color: #ffffff;
        }

        .btn-print:hover {
            background-color: #bb2d3b;
        }

        .btn-close-window {
            background-color: #6c757d;
            color: #ffffff;
        }

        .btn-close-window:hover {
            background-color: #5c636a;
        }

        /* Kop Surat */
        .kop-surat-img {
            width: 100%;
            max-height: 140px;
            object-fit: contain;
            display: block;
            margin: 0 auto 8px auto;
        }

        .kop-surat-manual {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 6px;
            text-align: center;
        }

        .kop-logo {
            max-height: 80px;
            max-width: 80px;
            object-fit: contain;
        }

        .kop-text h2 {
            margin: 0 0 2px 0;
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text p {
            margin: 0;
            font-size: 9.5pt;
            color: #333;
        }

        .double-line {
            border: 0;
            border-top: 2.5px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
            margin: 6px 0 16px 0;
        }

        /* Template Header / Judul Laporan */
        .header-laporan {
            text-align: center;
            margin-bottom: 18px;
        }

        .header-laporan h3 {
            margin: 0 0 4px 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-laporan p {
            margin: 0;
            font-size: 9pt;
            color: #555;
        }

        /* Info Header Card (Mirip baris 104-177 show.blade.php) */
        .info-header-box {
            border: 1px solid #333;
            background-color: #fafbfc;
            padding: 10px 14px;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px 10px;
            vertical-align: top;
            border-right: 1px solid #ccc;
        }

        .info-table td:last-child {
            border-right: none;
        }

        .meta-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #666;
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
            letter-spacing: 0.3px;
        }

        .meta-value {
            font-size: 10pt;
            font-weight: bold;
            color: #111;
            margin: 0;
        }

        .meta-sub {
            font-size: 8.5pt;
            color: #555;
            margin-top: 2px;
            display: block;
        }

        .badge-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #333;
            margin-top: 4px;
        }

        .badge-selesai {
            background-color: #d1e7dd;
            color: #0f5132;
            border-color: #badbcc;
        }

        .badge-berlangsung {
            background-color: #cff4fc;
            color: #055160;
            border-color: #b6effb;
        }

        .badge-belum_mulai {
            background-color: #f8f9fa;
            color: #495057;
            border-color: #ced4da;
        }

        /* Section Title */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 18px 0 8px 0;
            border-bottom: 1.5px solid #333;
            padding-bottom: 4px;
        }

        /* Table Report Data */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #444;
            padding: 5px 7px;
            vertical-align: middle;
        }

        .table-data th {
            background-color: #f1f3f5;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        .table-data tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        /* Tanda Tangan */
        .ttd-container {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
            font-size: 9.5pt;
        }

        .ttd-space {
            height: 65px;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 12mm 12mm;
            }

            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .sheet {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }

            .no-print {
                display: none !important;
            }

            .table-data th {
                background-color: #eaeaea !important;
            }

            .info-header-box {
                background-color: #ffffff !important;
            }
        }
    </style>
</head>

<body>

    <!-- Floating Action Toolbar (Hanya Tampil di Layar, Tidak Dicetak) -->
    <div class="no-print-toolbar no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-file-invoice text-warning"></i>
            <span><strong>Pratinjau Cetak Detail Riwayat Rute</strong> &bull; Ukuran Kertas: A4 Portrait</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fas fa-print"></i> Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn-action btn-close-window">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>

    <!-- Halaman Cetak Dokumen A4 -->
    <div class="sheet">

        <!-- 1. KOP SURAT (Dengan kopSurat jika ada, atau fallback manual) -->
        @if ($website && $website->kopSurat && file_exists(public_path('img/' . $website->kopSurat)))
            <div class="text-center">
                <img src="{{ asset('img/' . $website->kopSurat) }}" alt="Kop Surat" class="kop-surat-img">
            </div>
            <div class="double-line"></div>
        @else
            <div class="kop-surat-manual">
                @if ($website && $website->logo && file_exists(public_path('img/' . $website->logo)))
                    <img src="{{ asset('img/' . $website->logo) }}" alt="Logo" class="kop-logo">
                @endif
                <div class="kop-text">
                    <h2>{{ $website->nama ?? 'DINAS LINGKUNGAN HIDUP KOTA BINJAI' }}</h2>
                    <p>{{ $website->alamat ?? 'Jl. Sibolga No.5, Rambung Bar., Kec. Binjai Sel., Kota Binjai, Sumatera Utara 20735' }}</p>
                    <p>Telepon: {{ $website->telepon ?? '-' }} | Email: {{ $website->email ?? '-' }}</p>
                </div>
            </div>
            <div class="double-line"></div>
        @endif

        <!-- 2. TEMPLATE HEADER LAPORAN -->
        <div class="header-laporan">
            <h3>LAPORAN DETAIL AKTIVITAS & PERJALANAN RUTE</h3>
            <p>ID Rute: #{{ $ruteHarian->id }} &bull; Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>

        <!-- 3. INFO HEADER CARD (Sesuai baris 104-177 show.blade.php) -->
        <div class="info-header-box">
            <table class="info-table">
                <tr>
                    <!-- Nama Sopir -->
                    <td style="width: 25%;">
                        <span class="meta-label">Nama Sopir</span>
                        <p class="meta-value">{{ $ruteHarian->user->name ?? 'Sopir Terhapus' }}</p>
                        <span class="meta-sub">Username: {{ $ruteHarian->user->username ?? '-' }}</span>
                    </td>

                    <!-- Kendaraan / Angkutan -->
                    <td style="width: 25%;">
                        <span class="meta-label">Kendaraan / Angkutan</span>
                        <p class="meta-value">{{ $ruteHarian->angkutan->nama ?? '-' }}</p>
                        <span class="meta-sub">No Polisi: {{ $ruteHarian->angkutan->nomor ?? '-' }}</span>
                    </td>

                    <!-- Tanggal Tugas -->
                    <td style="width: 18%;">
                        <span class="meta-label">Tanggal Tugas</span>
                        <p class="meta-value">{{ $ruteHarian->tanggal->translatedFormat('d M Y') }}</p>
                        <span class="meta-sub">{{ $ruteHarian->tanggal->translatedFormat('l') }}</span>
                    </td>

                    <!-- Total Jarak & Durasi -->
                    <td style="width: 18%;">
                        <span class="meta-label">Total Jarak & Durasi</span>
                        <p class="meta-value" style="color: #0d6efd;">
                            {{ number_format(($ruteHarian->total_jarak_meter ?? 0) / 1000, 1) }} km
                        </p>
                        <span class="meta-sub">
                            @if ($ruteHarian->status === 'belum_mulai')
                                Belum Jalan
                            @elseif ($ruteHarian->status === 'berlangsung')
                                Sedang Jalan
                            @elseif ($ruteHarian->total_durasi_detik)
                                @php
                                    $durasi = abs($ruteHarian->total_durasi_detik);
                                    $jam = floor($durasi / 3600);
                                    $menit = floor(($durasi % 3600) / 60);
                                @endphp
                                {{ $jam > 0 ? $jam . 'j ' : '' }}{{ $menit }}m
                            @else
                                -
                            @endif
                        </span>
                    </td>

                    <!-- Status Rute -->
                    <td style="width: 14%; text-align: center;">
                        <span class="meta-label">Status Rute</span>
                        @php
                            $statusClass = match ($ruteHarian->status) {
                                'selesai' => 'badge-selesai',
                                'berlangsung' => 'badge-berlangsung',
                                'belum_mulai' => 'badge-belum_mulai',
                                default => '',
                            };
                            $statusLabel = match ($ruteHarian->status) {
                                'selesai' => 'Selesai',
                                'berlangsung' => 'Berlangsung',
                                'belum_mulai' => 'Belum Mulai',
                                default => ucfirst($ruteHarian->status),
                            };
                        @endphp
                        <span class="badge-status {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 4. TABEL TIMELINE AKTIVITAS SOPIR -->
        <div class="section-title">
            <i class="fas fa-history" style="font-size: 10pt; margin-right: 4px;"></i> Timeline Aktivitas Sopir
        </div>

        <table class="table-data">
            <thead>
                <tr>
                    <th width="35">No</th>
                    <th width="80">Waktu</th>
                    <th width="140">Aktivitas / Aksi</th>
                    <th>Lokasi Tujuan</th>
                    <th>Keterangan / Alasan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ruteHarian->ruteLog as $idx => $log)
                    @php
                        $labelAksi = match ($log->aksi) {
                            'mulai_rute' => 'Mulai Rute',
                            'tiba' => 'Tiba di Lokasi',
                            'selesai' => 'Selesai Pengangkutan',
                            'skip' => 'Dilewati (Skip)',
                            'tidak_dikunjungi' => 'Tidak Dikunjungi',
                            'reorder_manual' => 'Reorder Manual',
                            'reorder_otomatis' => 'Reorder Otomatis',
                            default => ucfirst(str_replace('_', ' ', $log->aksi)),
                        };

                        $lokasiNama = '-';
                        if ($log->ruteTujuan) {
                            if ($log->ruteTujuan->tipe === 'tpa') {
                                $lokasiNama = 'TPA (Titik Akhir Pembuangan)';
                            } elseif ($log->ruteTujuan->tps) {
                                $lokasiNama = $log->ruteTujuan->tps->nama . ($log->ruteTujuan->tps->alamat && $log->ruteTujuan->tps->alamat !== '-' ? ' (' . $log->ruteTujuan->tps->alamat . ')' : '');
                            } else {
                                $lokasiNama = 'TPS Terhapus';
                            }
                        } elseif ($log->aksi === 'mulai_rute') {
                            $lokasiNama = 'Kantor Dinas / Titik Awal';
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center fw-bold">{{ $log->created_at->format('H:i:s') }}</td>
                        <td>
                            @if ($log->aksi === 'mulai_rute' || $log->aksi === 'selesai')
                                <strong style="color: #198754;">{{ $labelAksi }}</strong>
                            @elseif ($log->aksi === 'tiba')
                                <strong style="color: #0dcaf0;">{{ $labelAksi }}</strong>
                            @elseif ($log->aksi === 'skip' || $log->aksi === 'tidak_dikunjungi')
                                <strong style="color: #dc3545;">{{ $labelAksi }}</strong>
                            @else
                                <strong>{{ $labelAksi }}</strong>
                            @endif
                        </td>
                        <td>{{ $lokasiNama }}</td>
                        <td>
                            @if ($log->alasan)
                                <span style="color: #dc3545; font-style: italic;">Alasan: {{ $log->alasan }}</span>
                            @else
                                <span style="color: #777;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 18px; color: #777;">
                            Belum ada catatan log aktivitas sopir pada rute ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 5. BAGIAN TANDA TANGAN -->
        <div class="ttd-container">
            <div class="ttd-box">
                <div>Binjai, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 3px;">Penanggung Jawab / Pengelola,</div>
                <div class="ttd-space"></div>
                <div style="text-decoration: underline; font-weight: bold;">( .................................................... )</div>
                <div style="margin-top: 2px;">NIP. ..............................................</div>
            </div>
            <div style="clear: both;"></div>
        </div>

    </div>

    <!-- Script Otomatis Menampilkan Dialog Print Saat Halaman Dibuka -->
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>

</body>

</html>
