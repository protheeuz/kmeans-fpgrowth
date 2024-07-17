<?php
if (!defined('myweb')) { exit(); }

require 'vendor/autoload.php'; // Path to autoload.php from Composer

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$link_list = $www.'alternatif';

// Fungsi untuk melakukan escape terhadap input
function escape($value) {
    global $con;
    return mysqli_real_escape_string($con, $value);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file_mimes = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    if (in_array($_FILES['file']['type'], $file_mimes)) {
        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);

        if ('csv' == $extension) {
            $reader = IOFactory::createReader('Csv');
        } else {
            $reader = IOFactory::createReader('Xlsx');
        }

        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        // Assuming the first row is the header
        $header = $sheetData[0];
        $data = array_slice($sheetData, 1);

        $con->query("START TRANSACTION");

        try {
            foreach ($data as $row) {
                // Mapping columns to the expected database fields
                $kode = escape($row[0]);
                $nama = escape($row[1]);
                $nilai_harga = escape($row[2]);
                $nilai_stok = escape($row[3]);
                $nilai_jumlah_penjualan = escape($row[4]);
                $jumlah_terjual = escape($row[5]);

                // Insert into alternatif table
                $con->query("INSERT INTO alternatif(kode, nama, jumlah_terjual) VALUES('$kode', '$nama', '$jumlah_terjual')");
                $id_alternatif = $con->insert_id;

                // Insert into nilai_kriteria table
                // Assuming kriteria IDs are: 1 for Harga, 2 for Stok, 3 for Jumlah Penjualan
                $con->query("INSERT INTO nilai_kriteria(id_alternatif, id_kriteria, nilai) VALUES('$id_alternatif', 1, '$nilai_harga')");
                $con->query("INSERT INTO nilai_kriteria(id_alternatif, id_kriteria, nilai) VALUES('$id_alternatif', 2, '$nilai_stok')");
                $con->query("INSERT INTO nilai_kriteria(id_alternatif, id_kriteria, nilai) VALUES('$id_alternatif', 3, '$nilai_jumlah_penjualan')");
            }

            $con->query("COMMIT");
            echo 'Data berhasil diunggah';
        } catch (Exception $e) {
            $con->query("ROLLBACK");
            header('HTTP/1.1 500 Internal Server Error');
            echo 'Gagal mengunggah data: ' . $e->getMessage();
        }
    } else {
        header('HTTP/1.1 415 Unsupported Media Type');
        echo 'Tipe file tidak didukung. Hanya file Excel yang diperbolehkan.';
    }
    die;
}
?>

<script type="text/javascript">
    window.location.href = '<?php echo $link_list;?>';
</script>