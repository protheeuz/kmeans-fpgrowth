<?php if (!defined('myweb')) {
    exit();
} ?>

<?php
$link_list = $www . 'penjualan';
require_once 'fp_growth.php'; // Pastikan untuk mengimpor FP-Growth

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_produk = $_POST['kode_produk'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];
    $min_support = isset($_POST['min_support']) ? floatval($_POST['min_support']) / 100 : 0.01; // Default 1% jika tidak diisi
    $min_confidence = isset($_POST['min_confidence']) ? floatval($_POST['min_confidence']) / 100 : 0.5; // Default 50% jika tidak diisi

    // Validasi support maksimal
    $max_support = calculateMaxSupport($con) / 100; // Konversi dari persentase ke desimal
    if ($min_support > $max_support) {
        echo '<script>
            Swal.fire({
                text: "Nilai minimal support tidak boleh melebihi ' . ($max_support * 100) . '%",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: { confirmButton: "btn btn-primary" },
            });
        </script>';
    } else {
        $stmt = $con->prepare("INSERT INTO penjualan (kode_produk, jumlah, tanggal, min_support, min_confidence) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisdd", $kode_produk, $jumlah, $tanggal, $min_support, $min_confidence);
        if ($stmt->execute()) {
            echo '<script>
                Swal.fire({
                    text: "Data penjualan berhasil ditambahkan",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: { confirmButton: "btn btn-primary" },
                }).then(function() {
                    window.location.href = "' . $link_list . '";
                });
            </script>';
        } else {
            echo '<script>
                Swal.fire({
                    text: "Gagal menambahkan data penjualan",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: { confirmButton: "btn btn-primary" },
                });
            </script>';
        }
        $stmt->close();
    }
}

// Fungsi untuk menghitung support maksimum
function calculateMaxSupport($con)
{
    $totalTransactions = 0;
    $itemCount = [];

    $q = $con->query("SELECT GROUP_CONCAT(DISTINCT kode_produk ORDER BY kode_produk) AS items FROM penjualan GROUP BY tanggal");
    if ($q->num_rows > 0) {
        while ($row = $q->fetch_assoc()) {
            $totalTransactions++;
            $items = explode(',', $row['items']);
            foreach ($items as $item) {
                if (!isset($itemCount[$item])) {
                    $itemCount[$item] = 0;
                }
                $itemCount[$item]++;
            }
        }
    }

    $maxSupport = 0;
    foreach ($itemCount as $count) {
        $support = $count / $totalTransactions;
        if ($support > $maxSupport) {
            $maxSupport = $support;
        }
    }

    return round($maxSupport * 100, 2); // Support maksimum dalam persentase
}

// Ambil data produk untuk pilihan kode produk
$produk_options = '';
$q = $con->query("SELECT kode, nama FROM alternatif ORDER BY nama");
while ($h = $q->fetch_assoc()) {
    $produk_options .= '<option value="' . $h['kode'] . '">' . $h['nama'] . '</option>';
}

// Ambil data transaksi dari tabel penjualan
$transactions = [];
$q = $con->query("SELECT p.tanggal, GROUP_CONCAT(DISTINCT p.kode_produk ORDER BY p.kode_produk) AS items 
                  FROM penjualan p 
                  GROUP BY p.tanggal");

if ($q->num_rows > 0) {
    while ($row = $q->fetch_assoc()) {
        $transactions[] = explode(',', $row['items']);
    }
}

// Jalankan algoritma FP-Growth untuk mendapatkan pola asosiasi
$min_support = 0.01; // Default 1% jika tidak ada input
$min_confidence = 0.5; // Default 50% jika tidak ada input

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $min_support = floatval($_POST['min_support']) / 100; // Konversi dari persentase ke desimal
    $min_confidence = floatval($_POST['min_confidence']) / 100; // Konversi dari persentase ke desimal
}

$tree = new FPTree();
$tree->buildHeaderTable($transactions);
foreach ($transactions as $transaction) {
    $tree->addTransaction($transaction);
}

$patterns = $tree->minePatterns($min_support, $transactions, $min_confidence);

?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="content-header">Input Data Penjualan</div>
        </div>
    </div>
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="kode_produk">Kode Produk</label>
                                    <select name="kode_produk" id="kode_produk" class="form-control" required>
                                        <?php echo $produk_options; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="number" name="jumlah" id="jumlah" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="min_support">Minimal Support (%) (1-100%)</label>
                                    <input type="number" step="0.01" name="min_support" id="min_support" class="form-control" min="1" max="100" required>
                                </div>
                                <div class="form-group">
                                    <label for="min_confidence">Minimal Confidence (%) (1-100%)</label>
                                    <input type="number" step="0.01" name="min_confidence" id="min_confidence" class="form-control" min="1" max="100" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah Penjualan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tambahkan tabel untuk menampilkan hasil asosiasi produk -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Hasil Asosiasi Produk</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel_asosiasi">
                                    <thead>
                                        <tr>
                                            <th width="40">NO</th>
                                            <th>Nama Produk</th>
                                            <th>Frekuensi</th>
                                            <th>Support</th>
                                            <th>Confidence</th>
                                            <th>Rekomendasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $productNames = [];
                                        $q = $con->query("SELECT kode, nama FROM alternatif");
                                        while ($row = $q->fetch_assoc()) {
                                            $productNames[$row['kode']] = $row['nama'];
                                        }
                                        $no = 1;
                                        foreach ($patterns as $item => $patternList) {
                                            foreach ($patternList as $pattern) {
                                                $patternNames = array_map(function ($code) use ($productNames) {
                                                    return isset($productNames[$code]) ? $productNames[$code] : $code;
                                                }, $pattern['pattern']);
                                                $patternStr = implode(", ", $patternNames);
                                                $support = isset($pattern['support']) ? round($pattern['support'] * 100, 2) . '%' : '0%';
                                                $confidence = isset($pattern['confidence']) ? min(round($pattern['confidence'] * 100, 2), 100) . '%' : '0%'; // Ensure confidence does not exceed 100%
                                                $recommendation = '';
                                                if (isset($pattern['confidence']) && $pattern['confidence'] >= $min_confidence) {
                                                    $recommendation = 'Rekomendasi untuk pembelian bersama, diletakkan di rak bersampingan, dan dijadikan satu ikatan produk.';
                                                }
                                                echo '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td class="text-nowrap">' . htmlspecialchars($patternStr) . '</td>
                        <td class="text-center">' . htmlspecialchars($pattern['frequency']) . '</td>
                        <td class="text-center">' . htmlspecialchars($support) . '</td>
                        <td class="text-center">' . htmlspecialchars($confidence) . '</td>
                        <td class="text-nowrap">' . htmlspecialchars($recommendation) . '</td>
                    </tr>
                    ';
                                                $no++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <button id="exportPdfBtn" class="btn btn-primary">Ekspor ke PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#tabel_asosiasi').DataTable({
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [1, 'asc']
            ]
        });

        table.on('order.dt search.dt', function() {
            let i = 1;
            table.cells(null, 0, {
                search: 'applied',
                order: 'applied'
            }).every(function(cell) {
                this.data(i++);
            });
        }).draw();

        $('#exportPdfBtn').click(function() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape'); // Use landscape mode for wider tables

            // Header section
            doc.setFontSize(20);
            doc.text("HASIL AKHIR & ASOSIASI", 148, 20, null, null, "center");
            doc.setFontSize(16);
            doc.text("KKGJ MART - IVAN RYADI", 148, 30, null, null, "center");
            doc.text("THESIS PURPOSE", 148, 40, null, null, "center");

            doc.setFontSize(12);
            doc.text("- HASIL DARI HASIL AKHIR DAN HASIL ASOSIASI PRODUK -", 148, 50, null, null, "center");

            // Menambahkan tabel hasil asosiasi
            let hasilAsosiasi = [];
            $("#tabel_asosiasi thead tr th").each(function() {
                hasilAsosiasi.push($(this).text());
            });

            let hasilAsosiasiBody = [];
            $("#tabel_asosiasi tbody tr").each(function() {
                let row = [];
                $(this).find("td").each(function() {
                    row.push($(this).text());
                });
                hasilAsosiasiBody.push(row);
            });

            doc.autoTable({
                head: [hasilAsosiasi],
                body: hasilAsosiasiBody,
                startY: 60,
                theme: 'striped',
                headStyles: {
                    fillColor: [100, 100, 255]
                },
                styles: {
                    fontSize: 10,
                    cellWidth: 'auto'
                }, // Adjust cell width automatically
                tableLineColor: [0, 0, 0],
                tableLineWidth: 0.1
            });

            doc.save('hasil_asosiasi_produk.pdf');
        });
    });
</script>