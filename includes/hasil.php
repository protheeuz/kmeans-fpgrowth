<?php if (!defined('myweb')) { exit(); } ?>
<?php

$link_list = $www . 'alternatif';
$link_update = $www . 'alternatif_update';

require_once 'fp_growth.php';

// Mengambil transaksi dari tabel penjualan
$transactions = [];
$minSupport = 2; // Default minimum support threshold
$confidenceThreshold = 0.5; // Default confidence

// Mengelompokkan transaksi berdasarkan tanggal
$q = $con->query("SELECT p.tanggal, GROUP_CONCAT(DISTINCT p.kode_produk ORDER BY p.kode_produk) AS items 
                  FROM penjualan p 
                  GROUP BY p.tanggal");

if ($q->num_rows > 0) {
    while ($row = $q->fetch_assoc()) {
        $transactions[] = explode(',', $row['items']);
    }
} else {
    echo "No transactions found.";
}

// Menghitung frekuensi item dari data penjualan
$frekuensi = [];
foreach ($transactions as $transaction) {
    foreach ($transaction as $item) {
        if (!isset($frekuensi[$item])) {
            $frekuensi[$item] = 0;
        }
        $frekuensi[$item]++;
    }
}

$tree = new FPTree();
$tree->buildHeaderTable($transactions);
foreach ($transactions as $transaction) {
    $tree->addTransaction($transaction);
}

$patterns = $tree->minePatterns($minSupport, $transactions);

$kriteria = array();
$q = $con->query("SELECT * FROM kriteria ORDER BY kode");
while ($h = $q->fetch_array()) {
    $kriteria[] = array('id' => $h['id_kriteria'], 'nama' => htmlspecialchars($h['nama']));
}

$cluster = array();
$nama_cluster = array();
$q = $con->query("SELECT * FROM cluster ORDER BY kode");
while ($h = $q->fetch_array()) {
    $cluster[] = array('id' => $h['id_cluster'], 'nama' => htmlspecialchars($h['nama']));
    $nama_cluster[$h['id_cluster']] = htmlspecialchars($h['nama']);
}

$nilai_kriteria = array();
$q = $con->query("SELECT * FROM nilai_kriteria");
while ($h = $q->fetch_array()) {
    $nilai_kriteria[$h['id_alternatif']][$h['id_kriteria']] = $h['nilai'];
}

$center_points = array();
$q = $con->query("SELECT * FROM center_points");
while ($h = $q->fetch_array()) {
    $center_points[$h['id_cluster']][$h['id_kriteria']] = $h['nilai'];
}

$alternatif = array();
$nilai_dataset = array();
$daftar_dataset = '';
$q = $con->query("SELECT * FROM alternatif ORDER BY id_alternatif");
while ($h = $q->fetch_array()) {
    $id = $h['id_alternatif'];
    $alternatif[] = $h['id_alternatif'];

    $daftar_dataset .= '
      <tr>
        <td class="text-center"></td>
        <td class="text-nowrap">' . htmlspecialchars($h['kode']) . '</td>
        <td class="text-nowrap">' . htmlspecialchars($h['nama']) . '</td>';
    foreach ($kriteria as $key => $value) {
        $nilai = '';
        if (isset($nilai_kriteria[$id][$value['id']])) {
            $nilai = $nilai_kriteria[$id][$value['id']];
        }
        $nilai_dataset[$id][$value['id']] = (float)$nilai;
        $daftar_dataset .= '<td class="text-center">' . htmlspecialchars($nilai) . '</td>';
    }
    $daftar_dataset .= '
      </tr>
    ';
}

$nilai_center_points = array();
$daftar_center_points = '';
$q = $con->query("SELECT * FROM cluster ORDER BY id_cluster");
while ($h = $q->fetch_array()) {
    $id = $h['id_cluster'];

    $daftar_center_points .= '
      <tr>
        <td class="text-center"></td>
        <td class="text-nowrap">' . htmlspecialchars($h['kode']) . '</td>
        <td class="text-nowrap">' . htmlspecialchars($h['nama']) . '</td>';
    foreach ($kriteria as $key => $value) {
        $nilai = '';
        if (isset($center_points[$id][$value['id']])) {
            $nilai = $center_points[$id][$value['id']];
        }
        $nilai_center_points[$id][$value['id']] = (float)$nilai;
        $daftar_center_points .= '<td class="text-center">' . htmlspecialchars($nilai) . '</td>';
    }
    $daftar_center_points .= '
      </tr>
    ';
}

$alternatif_cluster = array();
$daftar_iterasi_1 = '';
$q = $con->query("SELECT * FROM alternatif ORDER BY id_alternatif");
while ($h = $q->fetch_array()) {
    $id_alternatif = $h['id_alternatif'];

    $daftar_iterasi_1 .= '
    <tr>
    <td class="text-center"></td>
    <td class="text-nowrap">' . htmlspecialchars($h['kode']) . '</td>
    <td class="text-nowrap">' . htmlspecialchars($h['nama']) . '</td>';
    $id_cluster_min = '';
    $cluster_min = '';
    $nilai_min = 0;
    foreach ($cluster as $key => $value) {
        $id_cluster = $value['id'];
        $nilai = 0;
        foreach ($kriteria as $key2 => $value2) {
            $id_kriteria = $value2['id'];
            $nilai = $nilai + pow($nilai_dataset[$id_alternatif][$id_kriteria] - $nilai_center_points[$id_cluster][$id_kriteria], 2);
        }
        $nilai = pow($nilai, 0.5);
        if ($key == 0) {
            $id_cluster_min = $id_cluster;
            $cluster_min = $value['nama'];
            $nilai_min = $nilai;
        } else {
            if ($nilai < $nilai_min) {
                $id_cluster_min = $id_cluster;
                $cluster_min = $value['nama'];
                $nilai_min = $nilai;
            }
        }
        $daftar_iterasi_1 .= '<td class="text-center">' . round($nilai, 2) . '</td>';
    }
    $alternatif_cluster[$id_alternatif] = $id_cluster_min;
    $daftar_iterasi_1 .= '
    <td class="text-center">' . $cluster_min . '</td>
    </tr>
    ';
}

$nilai_center_points_2 = array();
$daftar_center_points_2 = '';
$q = $con->query("SELECT * FROM cluster ORDER BY id_cluster");
while ($h = $q->fetch_array()) {
    $id_cluster = $h['id_cluster'];

    $daftar_center_points_2 .= '
      <tr>
        <td class="text-center"></td>
        <td class="text-nowrap">' . htmlspecialchars($h['kode']) . '</td>
        <td class="text-nowrap">' . htmlspecialchars($h['nama']) . '</td>';
    foreach ($kriteria as $key => $value) {
        $id_kriteria = $value['id'];
        $nilai = 0;
        $c = 0;
        foreach ($alternatif as $key2 => $id_alternatif) {
            if ($alternatif_cluster[$id_alternatif] == $id_cluster) {
                $nilai = $nilai + $nilai_dataset[$id_alternatif][$id_kriteria];
                $c++;
            }
        }
        $nilai = round($nilai / $c, 2);
        $nilai_center_points_2[$id_cluster][$value['id']] = (float)$nilai;
        $daftar_center_points_2 .= '<td class="text-center">' . htmlspecialchars($nilai) . '</td>';
    }
    $daftar_center_points_2 .= '
      </tr>
    ';
}

$alternatif_cluster_2 = array();
$daftar_iterasi_2 = '';
$q = $con->query("SELECT * FROM alternatif ORDER BY id_alternatif");
while ($h = $q->fetch_array()) {
    $id_alternatif = $h['id_alternatif'];

    $daftar_iterasi_2 .= '
    <tr>
    <td class="text-center"></td>
    <td class="text-nowrap">' . htmlspecialchars($h['kode']) . '</td>
    <td class="text-nowrap">' . htmlspecialchars($h['nama']) . '</td>';
    $id_cluster_min = '';
    $cluster_min = '';
    $nilai_min = 0;
    foreach ($cluster as $key => $value) {
        $id_cluster = $value['id'];
        $nilai = 0;
        foreach ($kriteria as $key2 => $value2) {
            $id_kriteria = $value2['id'];
            $nilai = $nilai + pow($nilai_dataset[$id_alternatif][$id_kriteria] - $nilai_center_points_2[$id_cluster][$id_kriteria], 2);
        }
        $nilai = pow($nilai, 0.5);
        if ($key == 0) {
            $id_cluster_min = $id_cluster;
            $cluster_min = $value['nama'];
            $nilai_min = $nilai;
        } else {
            if ($nilai < $nilai_min) {
                $id_cluster_min = $id_cluster;
                $cluster_min = $value['nama'];
                $nilai_min = $nilai;
            }
        }
        $daftar_iterasi_2 .= '<td class="text-center">' . round($nilai, 2) . '</td>';
    }
    $alternatif_cluster_2[$id_alternatif] = $id_cluster_min;
    $daftar_iterasi_2 .= '
    <td class="text-center">' . $cluster_min . '</td>
    </tr>
    ';
}

$daftar_hasil = '';
$q = $con->query("SELECT * FROM alternatif ORDER BY id_alternatif");
while ($h = $q->fetch_array()) {
    $id = $h['id_alternatif'];
    $alternatif[] = $h['id_alternatif'];

    $daftar_hasil .= '
    <tr>
    <td class="text-center"></td>
    <td class="text-nowrap">' . htmlspecialchars($h['kode']) . '</td>
    <td class="text-nowrap">' . htmlspecialchars($h['nama']) . '</td>';
    foreach ($kriteria as $key => $value) {
        $nilai = '';
        if (isset($nilai_kriteria[$id][$value['id']])) {
            $nilai = $nilai_kriteria[$id][$value['id']];
        }
        $daftar_hasil .= '<td class="text-center">' . htmlspecialchars($nilai) . '</td>';
    }
    $daftar_hasil .= '
    <td class="text-center">' . $nama_cluster[$alternatif_cluster_2[$id]] . '</td>
    </tr>
    ';
}

$produk_terjual = array();
$q = $con->query("SELECT a.kode, a.nama, COALESCE(SUM(nk.nilai), 0) AS total_terjual 
                  FROM alternatif a 
                  JOIN nilai_kriteria nk ON a.id_alternatif = nk.id_alternatif 
                  WHERE nk.id_kriteria = (SELECT id_kriteria FROM kriteria WHERE nama = 'JUMLAH PENJUALAN')
                  GROUP BY a.kode, a.nama 
                  ORDER BY total_terjual DESC 
                  LIMIT 5");
while ($h = $q->fetch_array()) {
    $produk_terjual[] = array('nama' => $h['nama'], 'total' => $h['total_terjual']);
}

?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="content-header">Hasil Clustering dan Asosiasi Produk</div>
        </div>
    </div>
    <section>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Dataset</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel_dataset">
                                    <thead>
                                        <tr>
                                            <th width="40">NO</th>
                                            <th>KODE</th>
                                            <th>NAMA</th>
                                            <?php
                                            foreach ($kriteria as $key => $value) {
                                                echo '<th class="text-center">' . strtoupper($value['nama']) . '</th>';
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $daftar_dataset; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Center Points</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel_center_points">
                                    <thead>
                                        <tr>
                                            <th width="40">NO</th>
                                            <th>KODE</th>
                                            <th>NAMA</th>
                                            <?php
                                            foreach ($kriteria as $key => $value) {
                                                echo '<th class="text-center">' . strtoupper($value['nama']) . '</th>';
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $daftar_center_points; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Iterasi 1</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel_iterasi_1">
                                    <thead>
                                        <tr>
                                            <th width="40">NO</th>
                                            <th>KODE</th>
                                            <th>NAMA</th>
                                            <?php
                                            foreach ($cluster as $key => $value) {
                                                echo '<th class="text-center">' . strtoupper($value['nama']) . '</th>';
                                            }
                                            ?>
                                            <th>CLUSTER</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $daftar_iterasi_1; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Center Points 2</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel_center_points_2">
                                    <thead>
                                        <tr>
                                            <th width="40">NO</th>
                                            <th>KODE</th>
                                            <th>NAMA</th>
                                            <?php
                                            foreach ($kriteria as $key => $value) {
                                                echo '<th class="text-center">' . strtoupper($value['nama']) . '</th>';
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $daftar_center_points_2; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Iterasi 2</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel_iterasi_2">
                                    <thead>
                                        <tr>
                                            <th width="40">NO</th>
                                            <th>KODE</th>
                                            <th>NAMA</th>
                                            <?php
                                            foreach ($cluster as $key => $value) {
                                                echo '<th class="text-center">' . strtoupper($value['nama']) . '</th>';
                                            }
                                            ?>
                                            <th>CLUSTER</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $daftar_iterasi_2; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Hasil Akhir</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel_hasil">
                                    <thead>
                                        <tr>
                                            <th width="40">NO</th>
                                            <th>KODE</th>
                                            <th>NAMA</th>
                                            <?php
                                            foreach ($kriteria as $key => $value) {
                                                echo '<th class="text-center">' . strtoupper($value['nama']) . '</th>';
                                            }
                                            ?>
                                            <th>HASIL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $daftar_hasil; ?>
                                    </tbody>
                                </table>
                                <div class="card-body">
                                    <button id="exportPdfBtn" class="btn btn-primary">Ekspor ke PDF</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tambahkan tabel untuk menampilkan produk yang banyak terjual -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Produk Terbanyak Terjual</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel_produk_terjual">
                                    <thead>
                                        <tr>
                                            <th width="40">NO</th>
                                            <th>NAMA PRODUK</th>
                                            <th>TOTAL TERJUAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        foreach ($produk_terjual as $key => $value) {
                                            echo '
                                            <tr>
                                                <td class="text-center">' . $no . '</td>
                                                <td class="text-nowrap">' . htmlspecialchars($value['nama']) . '</td>
                                                <td class="text-nowrap">' . htmlspecialchars($value['total']) . '</td>
                                            </tr>
                                            ';
                                            $no++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tambahkan tabel untuk menampilkan hasil asosiasi produk -->
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
                                            <th>Pola</th>
                                            <th>Frekuensi</th>
                                            <th>Confidence</th>
                                            <th>Rekomendasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        foreach ($patterns as $item => $patternList) {
                                            foreach ($patternList as $pattern) {
                                                $patternStr = implode(", ", $pattern['pattern']);
                                                $recommendation = '';
                                                if (isset($pattern['confidence']) && $pattern['confidence'] >= $confidenceThreshold) {
                                                    $recommendation = 'Rekomendasi untuk pembelian bersama, diletakkan di rak bersampingan, dan dijadikan satu ikatan produk.';
                                                }
                                                echo '
                                                <tr>
                                                    <td class="text-center">' . $no . '</td>
                                                    <td class="text-nowrap">' . htmlspecialchars($patternStr) . '</td>
                                                    <td class="text-center">' . htmlspecialchars($pattern['frequency']) . '</td>
                                                    <td class="text-center">' . (isset($pattern['confidence']) ? htmlspecialchars($pattern['confidence']) : '-') . '</td>
                                                    <td class="text-nowrap">' . htmlspecialchars($recommendation) . '</td>
                                                </tr>
                                                ';
                                                $no++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var t = $('#tabel_dataset').DataTable({
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [1, 'asc']
            ]
        });

        t.on('order.dt search.dt', function() {
            t.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        var t = $('#tabel_center_points').DataTable({
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [1, 'asc']
            ]
        });

        t.on('order.dt search.dt', function() {
            t.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        var t = $('#tabel_iterasi_1').DataTable({
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [1, 'asc']
            ]
        });

        t.on('order.dt search.dt', function() {
            t.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        var t = $('#tabel_center_points_2').DataTable({
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [1, 'asc']
            ]
        });

        t.on('order.dt search.dt', function() {
            t.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        var t = $('#tabel_iterasi_2').DataTable({
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [1, 'asc']
            ]
        });

        t.on('order.dt search.dt', function() {
            t.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        var t = $('#tabel_hasil').DataTable({
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [1, 'asc']
            ]
        });

        t.on('order.dt search.dt', function() {
            t.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        var t = $('#tabel_produk_terjual').DataTable({
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [1, 'asc']
            ]
        });

        t.on('order.dt search.dt', function() {
            t.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        var t = $('#tabel_asosiasi').DataTable({
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [1, 'asc']
            ]
        });

        t.on('order.dt search.dt', function() {
            t.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        $('#exportPdfBtn').click(function() {
            const doc = new jsPDF();

            html2canvas(document.querySelector("#tabel_dataset")).then(canvas => {
                doc.addImage(canvas.toDataURL("image/png"), "PNG", 10, 10, 190, 0);
                doc.addPage();
                html2canvas(document.querySelector("#tabel_center_points")).then(canvas => {
                    doc.addImage(canvas.toDataURL("image/png"), "PNG", 10, 10, 190, 0);
                    doc.addPage();
                    html2canvas(document.querySelector("#tabel_iterasi_1")).then(canvas => {
                        doc.addImage(canvas.toDataURL("image/png"), "PNG", 10, 10, 190, 0);
                        doc.addPage();
                        html2canvas(document.querySelector("#tabel_center_points_2")).then(canvas => {
                            doc.addImage(canvas.toDataURL("image/png"), "PNG", 10, 10, 190, 0);
                            doc.addPage();
                            html2canvas(document.querySelector("#tabel_iterasi_2")).then(canvas => {
                                doc.addImage(canvas.toDataURL("image/png"), "PNG", 10, 10, 190, 0);
                                doc.addPage();
                                html2canvas(document.querySelector("#tabel_hasil")).then(canvas => {
                                    doc.addImage(canvas.toDataURL("image/png"), "PNG", 10, 10, 190, 0);
                                    doc.addPage();
                                    html2canvas(document.querySelector("#tabel_produk_terjual")).then(canvas => {
                                        doc.addImage(canvas.toDataURL("image/png"), "PNG", 10, 10, 190, 0);
                                        doc.addPage();
                                        html2canvas(document.querySelector("#tabel_asosiasi")).then(canvas => {
                                            doc.addImage(canvas.toDataURL("image/png"), "PNG", 10, 10, 190, 0);
                                            doc.save('hasil_clustering.pdf');
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
        });
    });
</script>