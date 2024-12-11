<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjadwalan Layanan Medis Darurat</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <?php
        // Konfigurasi database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "medis";

        // Buat koneksi ke database
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Cek koneksi
        if ($conn->connect_error) {
            die("Koneksi gagal: " . $conn->connect_error);
        }

        // Ambil data dari database
        $sql = "SELECT * FROM pasien";
        $result = $conn->query($sql);

        echo "<h1>Penjadwalan Layanan Medis Darurat</h1>";

        if ($result->num_rows > 0) {
            // Bobot untuk perhitungan prioritas
            $w1 = 1.0; // Bobot untuk tingkat keparahan
            $w2 = 0.3; // Bobot untuk riwayat penyakit
            $w3 = 0.2; // Bobot untuk usia

            // Header tabel
            echo "<table>
                    <thead>
                        <tr>
                            <th>ID Pasien</th>
                            <th>Nama Pasien</th>
                            <th>Tingkat Keparahan</th>
                            <th>Riwayat Penyakit</th>
                            <th>Usia</th>
                            <th>Prioritas (Skor)</th>
                            <th>Waktu Tunggu (menit)</th>
                            <th>Waktu Penanganan (menit)</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $result->fetch_assoc()) {
                // Validasi nilai keparahan
                $keparahan = $row['keparahan'];
                $keparahan_label = match ((int)$keparahan) {
                    5 => "Sangat Kritikal",
                    4 => "Kritis",
                    3 => "Sedang",
                    2 => "Ringan",
                    1 => "Sangat Ringan",
                    default => "Tidak Diketahui",
                };

                $riwayat_label = $row['riwayat'] == 1 ? "Ada Riwayat" : "Tidak Ada Riwayat";

                // Hitung prioritas
                $prioritas = ($w1 * $row['keparahan']) + ($w2 * $row['riwayat']) + ($w3 * $row['usia']);
                $waktuTunggu = max(5, 30 - intval($prioritas * 2));
                $waktuPenanganan = $waktuTunggu + 10;

                // Update database dengan hasil perhitungan
                $updateSql = "UPDATE pasien SET 
                                prioritas = $prioritas, 
                                waktu_tunggu = $waktuTunggu, 
                                waktu_penanganan = $waktuPenanganan 
                              WHERE id = {$row['id']}";
                $conn->query($updateSql);

                // Tampilkan data dalam tabel
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['keparahan']} ({$keparahan_label})</td>
                        <td>{$row['riwayat']} ({$riwayat_label})</td>
                        <td>{$row['usia']}</td>
                        <td>" . number_format($prioritas, 1) . "</td>
                        <td>$waktuTunggu</td>
                        <td>$waktuPenanganan</td>
                      </tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p class='no-data'>Tidak ada data pasien yang ditemukan.</p>";
        }

        // Tutup koneksi database
        $conn->close();
        ?>
        <!-- Tombol Kembali -->
        <div class="button-container">
            <a href="index.html" class="button">Isi Form Ulang</a>
        </div>
    </div>
</body>
</html>
