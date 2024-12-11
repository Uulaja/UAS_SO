<?php
// Konfigurasi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medis";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$nama = $_POST['nama'];
$keparahan = $_POST['keparahan'];
$riwayat = $_POST['riwayat'];
$usia = $_POST['usia'];

// Simpan ke database
$sql = "INSERT INTO pasien (nama, keparahan, riwayat, usia) 
        VALUES ('$nama', '$keparahan', '$riwayat', '$usia')";

if ($conn->query($sql) === TRUE) {
     // Jika berhasil, alihkan ke process.php
     header("Location: process.php");
     exit;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
