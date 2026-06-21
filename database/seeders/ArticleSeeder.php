<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'category'   => 'TIPS & TRIK',
                'title'      => 'Cara Memaksimalkan Sinyal WiFi di Rumah',
                'excerpt'    => 'Posisikan router di tengah ruangan untuk jangkauan yang lebih baik. Hindari hambatan seperti dinding tebal dan perangkat elektronik yang dapat mengganggu sinyal.',
                'content'    => '<h2>Tips Mengoptimalkan WiFi di Rumah</h2>
<p>Sinyal WiFi yang lemah seringkali menjadi sumber frustrasi bagi pengguna internet di rumah. Dengan beberapa langkah sederhana, Anda dapat meningkatkan kualitas sinyal secara signifikan.</p>
<h3>1. Posisikan Router di Tempat Strategis</h3>
<p>Letakkan router di tengah rumah, posisi yang tinggi, dan jauh dari dinding tebal. Sinyal WiFi menyebar ke segala arah, sehingga posisi tengah memberikan jangkauan paling merata.</p>
<h3>2. Jauhkan dari Gangguan Elektromagnetik</h3>
<p>Microwave, telepon nirkabel, dan perangkat Bluetooth beroperasi pada frekuensi yang sama dengan WiFi 2.4GHz. Pastikan router jauh dari perangkat-perangkat tersebut.</p>
<h3>3. Gunakan Frekuensi 5GHz untuk Kecepatan Tinggi</h3>
<p>Jika router Anda mendukung dual-band, gunakan frekuensi 5GHz untuk perangkat yang dekat dengan router. Frekuensi ini lebih cepat meski jangkauannya lebih pendek.</p>
<h3>4. Update Firmware Router Secara Berkala</h3>
<p>Produsen router secara rutin merilis pembaruan firmware yang memperbaiki bug dan meningkatkan performa. Pastikan firmware router Anda selalu up-to-date.</p>
<p>Dengan menerapkan tips di atas, kecepatan dan stabilitas WiFi di rumah Anda akan meningkat secara signifikan. Jika masalah terus berlanjut, hubungi tim support ARMEDIA kami yang siap membantu 24 jam.</p>',
                'image_url'  => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&q=80',
                'cover_image'=> null,
                'gallery'    => null,
            ],
            [
                'category'   => 'TEKNOLOGI',
                'title'      => 'Mengenal Keunggulan Internet Fiber Optic',
                'excerpt'    => 'Fiber optic menggunakan cahaya untuk mentransmisikan data dengan kecepatan luar biasa dan latensi rendah — jauh melampaui kabel tembaga konvensional.',
                'content'    => '<h2>Mengapa Fiber Optic Lebih Unggul?</h2>
<p>Teknologi fiber optic telah merevolusi cara kita terhubung ke internet. Berbeda dengan kabel tembaga biasa, fiber optic menggunakan pulsa cahaya untuk mengirimkan data.</p>
<h3>Kecepatan yang Jauh Lebih Tinggi</h3>
<p>Fiber optic mampu mencapai kecepatan hingga 1 Gbps (1000 Mbps) atau bahkan lebih. Bandingkan dengan kabel tembaga yang biasanya maksimal 100 Mbps dalam kondisi ideal.</p>
<h3>Latensi Sangat Rendah</h3>
<p>Latency (ping) yang rendah sangat krusial untuk gaming online, video call, dan aplikasi real-time lainnya. Fiber optic memberikan latensi di bawah 5ms, sementara kabel tembaga bisa mencapai 50-100ms.</p>
<h3>Tidak Terpengaruh Cuaca dan Interferensi</h3>
<p>Kabel tembaga rentan terhadap gangguan elektromagnetik dan degradasi sinyal saat cuaca buruk. Fiber optic tidak memiliki masalah ini karena menggunakan cahaya sebagai media transmisi.</p>
<h3>Keandalan Tinggi</h3>
<p>Jaringan fiber optic lebih tahan lama dan minim downtime. ARMEDIA menggunakan infrastruktur fiber optic terkini untuk memastikan koneksi Anda selalu stabil sepanjang waktu.</p>',
                'image_url'  => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=800&q=80',
                'cover_image'=> null,
                'gallery'    => null,
            ],
            [
                'category'   => 'PROMO',
                'title'      => 'Promo Spesial Pelanggan Baru ARMEDIA 2026',
                'excerpt'    => 'Dapatkan penawaran eksklusif untuk pelanggan baru: gratis biaya instalasi dan 1 bulan pertama dengan harga spesial. Berlaku terbatas!',
                'content'    => '<h2>Promo Pelanggan Baru ARMEDIA 2026</h2>
<p>Kami dengan bangga mempersembahkan penawaran eksklusif bagi Anda yang ingin bergabung dengan keluarga besar ARMEDIA di tahun 2026 ini!</p>
<h3>Apa yang Anda Dapatkan?</h3>
<ul>
<li><strong>Gratis Biaya Instalasi</strong> — Tidak ada biaya pemasangan untuk pendaftar baru</li>
<li><strong>Harga Spesial Bulan Pertama</strong> — Nikmati diskon eksklusif di bulan pertama berlangganan</li>
<li><strong>Bonus Poin ACR</strong> — Langsung dapatkan poin reward sejak hari pertama</li>
<li><strong>Prioritas Teknis</strong> — Pelanggan baru mendapat prioritas survei dan pemasangan</li>
</ul>
<h3>Cara Mendapatkan Promo Ini</h3>
<p>Cukup daftarkan diri Anda melalui formulir pendaftaran di website ini. Isi data lengkap dan pilih paket yang sesuai kebutuhan Anda. Tim kami akan menghubungi Anda untuk konfirmasi jadwal pemasangan.</p>
<p><strong>⚠️ Penawaran ini berlaku terbatas! Daftarkan diri Anda sekarang sebelum kuota habis.</strong></p>',
                'image_url'  => 'https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=800&q=80',
                'cover_image'=> null,
                'gallery'    => null,
            ],
            [
                'category'   => 'INFORMASI',
                'title'      => 'Perluasan Jaringan ARMEDIA 2026 ke Seluruh Gumelar',
                'excerpt'    => 'ARMEDIA terus memperluas jaringan fiber optic ke berbagai pelosok desa di Kecamatan Gumelar, Banyumas. Cek apakah area Anda sudah terkover!',
                'content'    => '<h2>Ekspansi Jaringan ARMEDIA di Kecamatan Gumelar</h2>
<p>Sebagai wujud komitmen kami untuk menghadirkan internet berkualitas ke seluruh pelosok Kecamatan Gumelar, ARMEDIA terus melakukan ekspansi infrastruktur jaringan di tahun 2026.</p>
<h3>Area yang Sudah Terkover</h3>
<ul>
<li>✅ Desa Gumelar</li>
<li>✅ Desa Cihonje</li>
<li>✅ Desa Tlaga</li>
<li>✅ Desa Paningkaban</li>
<li>✅ Desa Gancang</li>
<li>✅ Desa Kedungurang</li>
<li>✅ Desa Samudra</li>
<li>✅ Desa Karang Kemojing</li>
</ul>
<h3>Target Ekspansi 2026</h3>
<p>Kami sedang dalam proses pembangunan infrastruktur untuk menjangkau lebih banyak desa di Kecamatan Gumelar. Estimasi penambahan coverage akan diumumkan secara berkala melalui website ini.</p>
<h3>Cek Ketersediaan di Area Anda</h3>
<p>Gunakan fitur <strong>Cek Area Coverage</strong> di halaman utama website kami untuk mengetahui apakah layanan ARMEDIA sudah tersedia di lokasi Anda. Jika belum, daftarkan minat Anda dan kami akan notifikasi saat area Anda sudah terkover.</p>',
                'image_url'  => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=800&q=80',
                'cover_image'=> null,
                'gallery'    => null,
            ],
        ];

        foreach ($articles as $article) {
            // Hanya insert jika belum ada (berdasarkan judul)
            Article::firstOrCreate(
                ['title' => $article['title']],
                $article
            );
        }
    }
}
