<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Laporan Riwayat Pengangkutan Sampah' }}</title>
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ $website->logo ? asset('img/' . $website->logo) : asset('ample/plugins/images/favicon.png') }}">

    <!-- Google Fonts & FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* General Page & Print Styling */
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
            margin: 0 0 8px 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-table {
            margin: 0 auto;
            border-collapse: collapse;
            font-size: 9.5pt;
            text-align: left;
        }

        .meta-table td {
            padding: 1.5px 6px;
            vertical-align: top;
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
            padding: 5px 6px;
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
        }
    </style>
</head>

<body>

    <!-- Floating Action Toolbar (Hanya Tampil di Layar, Tidak Dicetak) -->
    <div class="no-print-toolbar no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-file-alt text-warning"></i>
            <span><strong>Pratinjau Cetak Laporan</strong> &bull; Ukuran Kertas: A4 Portrait</span>
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
            {{-- <div class="double-line"></div> --}}
        @else
            <div class="kop-surat-manual">
                @if ($website && $website->logo && file_exists(public_path('img/' . $website->logo)))
                    <img src="{{ asset('img/' . $website->logo) }}" alt="Logo" class="kop-logo">
                @endif
                <div class="kop-text">
                    <h2>{{ $website->nama ?? 'DINAS LINGKUNGAN HIDUP KOTA BINJAI' }}</h2>
                    <p>{{ $website->alamat ?? 'Jl. Sibolga No.5, Rambung Bar., Kec. Binjai Sel., Kota Binjai, Sumatera Utara 20735' }}
                    </p>
                    <p>Telepon: {{ $website->telepon ?? '-' }} | Email: {{ $website->email ?? '-' }}</p>
                </div>
            </div>
            <div class="double-line"></div>
        @endif

        <!-- 2. TEMPLATE HEADER LAPORAN -->
        <div class="header-laporan">
            <h3>LAPORAN RIWAYAT PENGANGKUTAN SAMPAH</h3>
            <table class="meta-table">
                <tr>
                    <td style="font-weight: bold; width: 150px;">Rentang Tanggal</td>
                    <td style="width: 10px;">:</td>
                    <td style="font-weight: bold;">{{ $periodeText }}</td>
                </tr>
                @if ($selectedSopir)
                    <tr>
                        <td style="font-weight: bold;">Sopir Tertentu</td>
                        <td>:</td>
                        <td>{{ $selectedSopir->name }} ({{ $selectedSopir->username }})</td>
                    </tr>
                @endif
                @if ($statusFilter)
                    <tr>
                        <td style="font-weight: bold;">Status Pengangkutan</td>
                        <td>:</td>
                        <td>
                            @if ($statusFilter === 'selesai')
                                Selesai
                            @elseif($statusFilter === 'berlangsung')
                                Berlangsung (Aktif)
                            @elseif($statusFilter === 'belum_mulai')
                                Belum Mulai
                            @else
                                {{ ucfirst($statusFilter) }}
                            @endif
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="font-weight: bold;">Waktu Cetak</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
                </tr>
            </table>
        </div>

        <!-- 3. TABEL DATA RIWAYAT -->
        <table class="table-data">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="85">Tanggal</th>
                    <th>Nama Sopir</th>
                    <th>Kendaraan / Plat</th>
                    <th width="65">Total TPS</th>
                    <th width="80">Total Jarak</th>
                    <th width="90">Durasi</th>
                    <th width="75">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $idx => $rute)
                    @php
                        $tpsCount = $rute->ruteTujuan->where('tipe', 'tps')->count();

                        // Hitung durasi
                        $durasiSeconds = abs($rute->total_durasi_detik ?? 0);
                        $jam = floor($durasiSeconds / 3600);
                        $menit = floor(($durasiSeconds % 3600) / 60);

                        $durasiText = '-';
                        if ($rute->status === 'berlangsung') {
                            $durasiText = 'Sedang Jalan';
                        } elseif ($rute->total_durasi_detik) {
                            $durasiText = ($jam > 0 ? $jam . 'j ' : '') . $menit . 'm';
                        }

                        $statusLabel = match ($rute->status) {
                            'selesai' => 'Selesai',
                            'berlangsung' => 'Berlangsung',
                            'belum_mulai' => 'Belum Mulai',
                            default => ucfirst($rute->status),
                        };
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $rute->tanggal->translatedFormat('d/m/Y') }}</td>
                        <td><strong>{{ $rute->user->name ?? 'Sopir Terhapus' }}</strong></td>
                        <td>
                            {{ $rute->angkutan->nama ?? '-' }}
                            @if (!empty($rute->angkutan->nomor))
                                <br><small style="color: #555;">({{ $rute->angkutan->nomor }})</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $tpsCount }}</td>
                        <td class="text-end">
                            {{ number_format(($rute->total_jarak_meter ?? 0) / 1000, 1) }} km
                        </td>
                        <td class="text-center">{{ $durasiText }}</td>
                        <td class="text-center fw-bold">{{ $statusLabel }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px; color: #777;">
                            Tidak ada data riwayat pengangkutan yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($riwayat->isNotEmpty())
                <tfoot>
                    <tr style="background-color: #f1f3f5; font-weight: bold;">
                        <td colspan="4" class="text-center">RINGKASAN KESELURUHAN</td>
                        <td class="text-center">{{ $totalTps }} Titik</td>
                        <td class="text-end">{{ number_format($totalJarakMeter / 1000, 1) }} km</td>
                        <td class="text-center">
                            @php
                                $totalJam = floor($totalDurasiDetik / 3600);
                                $totalMenit = floor(($totalDurasiDetik % 3600) / 60);
                            @endphp
                            {{ $totalJam > 0 ? $totalJam . 'j ' : '' }}{{ $totalMenit }}m
                        </td>
                        <td class="text-center">{{ $totalPerjalanan }} Trip</td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <!-- 4. BAGIAN TANDA TANGAN -->
        <div class="ttd-container">
            <div class="ttd-box">
                <div>Binjai, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 3px;">Penanggung Jawab / Pengelola,</div>
                <div class="ttd-space"></div>
                <div style="text-decoration: underline; font-weight: bold;">(
                    .................................................... )</div>
                <div style="margin-top: 2px;">NIP. ..............................................</div>
            </div>
            <div style="clear: both;"></div>
        </div>

    </div>

    <!-- Script Otomatis Menampilkan Dialog Print Saat Halaman Dibuka -->
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            // Beri sedikit delay agar gambar kop surat selesai dimuat sebelum dialog print muncul
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>

</body>

</html>
