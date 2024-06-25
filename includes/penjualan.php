<?php if(!defined('myweb')){ exit(); }?>

<?php
$link_list = $www.'penjualan';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_produk = $_POST['kode_produk'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    $stmt = $con->prepare("INSERT INTO penjualan (kode_produk, jumlah, tanggal) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $kode_produk, $jumlah, $tanggal);
    if ($stmt->execute()) {
        echo "<script>alert('Data penjualan berhasil ditambahkan');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data penjualan');</script>";
    }
    $stmt->close();
}

// Ambil data produk untuk pilihan kode produk
$produk_options = '';
$q = $con->query("SELECT kode, nama FROM alternatif ORDER BY nama");
while ($h = $q->fetch_assoc()) {
    $produk_options .= '<option value="'.$h['kode'].'">'.$h['nama'].'</option>';
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="content-header">Tambah Data Penjualan</div>
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
                                <button type="submit" class="btn btn-primary">Tambah Penjualan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
