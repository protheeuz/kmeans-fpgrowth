<?php if(!defined('myweb')){ exit(); }?>
<div class="content-wrapper">
    <section id="basic-hidden-label-form-layouts">
        <div class="row match-height">
            <div class="col-lg-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Daftar Penjualan</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel_penjualan">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>NAMA PRODUK</th>
                                            <th>JUMLAH</th>
                                            <th>TANGGAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Mengambil data penjualan dengan join ke tabel alternatif untuk mendapatkan nama produk
                                        $q = $con->query("SELECT p.jumlah, p.tanggal, a.nama AS nama_produk 
                                                          FROM penjualan p 
                                                          JOIN alternatif a ON p.kode_produk = a.kode 
                                                          ORDER BY p.tanggal DESC");
                                        $no = 1;
                                        while($row = $q->fetch_assoc()) {
                                            echo "
                                            <tr>
                                                <td>{$no}</td>
                                                <td>{$row['nama_produk']}</td>
                                                <td>{$row['jumlah']}</td>
                                                <td>{$row['tanggal']}</td>
                                            </tr>
                                            ";
                                            $no++;
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
$(document).ready(function () {
    var t = $('#tabel_penjualan').DataTable({
        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": [0]
        }],
        "order": [[3, 'desc']]
    });
});
</script>