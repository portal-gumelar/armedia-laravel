<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan CSR ARMEDIA</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .kop-surat {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .kop-surat h1 {
            margin: 0;
            font-size: 24px;
            color: #0ea5e9; /* Sky 500 */
        }
        .kop-surat p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .subtitle {
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: center;
        }
        td {
            padding: 8px 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .ttd {
            float: right;
            width: 250px;
            text-align: center;
        }
        .ttd-space {
            height: 80px;
        }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h1>PT. AKSES ARTHA MEDIA</h1>
        <p>Jl. Raya Gumelar, Banyumas, Jawa Tengah<br>
        Email: info@armedia.co.id | Telp: 0812-XXXX-XXXX</p>
    </div>

    <div class="title">LAPORAN KONTRIBUSI KAS DESA (CSR)</div>
    <div class="subtitle">Periode: {{ $periodLabel }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Desa / Wilayah</th>
                <th>Total Pelanggan Aktif</th>
                <th>Total Kontribusi (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalCsr = 0; $totalPelanggan = 0; $no = 1; @endphp
            @foreach($data as $village => $row)
                @php 
                    $totalCsr += $row['csr_amount']; 
                    $totalPelanggan += $row['count'];
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>Desa {{ $village }}</td>
                    <td class="text-center">{{ $row['count'] }}</td>
                    <td class="text-right">{{ number_format($row['csr_amount'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL KESELURUHAN</th>
                <th class="text-center">{{ $totalPelanggan }}</th>
                <th class="text-right">{{ number_format($totalCsr, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <p>Demikian laporan kontribusi Dana Desa (CSR) dari PT. Akses Artha Media untuk periode <strong>{{ $periodLabel }}</strong>. Kami mengucapkan terima kasih atas kerjasama yang baik dari Pemerintah Desa setempat.</p>

    <div class="footer">
        <div class="ttd">
            <p>Banyumas, {{ $datePrinted }}<br>Hormat Kami,</p>
            <div class="ttd-space"></div>
            <p><strong>Pimpinan / Direktur</strong><br>PT. Akses Artha Media</p>
        </div>
    </div>

</body>
</html>
