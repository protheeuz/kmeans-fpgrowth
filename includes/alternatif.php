<?php if(!defined('myweb')){ exit(); }?>
<?php  

$link_list = $www.'alternatif';
$link_update = $www.'alternatif_update';
$link_import = $www.'alternatif_import'; // Link untuk mengunggah dataset

if(isset($_POST['delete']) and isset($_POST['id'])){
    $id = $_POST['id'];
    mysqli_query($con, "DELETE FROM alternatif WHERE id_alternatif = '".escape($id)."'");
    die;
}

$kriteria = array();
$q = $con->query("SELECT * FROM kriteria ORDER BY kode");
while($h = $q->fetch_array()){
    $kriteria[] = array('id'=>$h['id_kriteria'], 'nama'=>htmlspecialchars($h['nama']));
}

$nilai_kriteria = array();
$q = $con->query("SELECT * FROM nilai_kriteria");
while($h = $q->fetch_array()){
    $nilai_kriteria[$h['id_alternatif']][$h['id_kriteria']] = $h['nilai'];
}

$daftar = '';
$q = $con->query("SELECT * FROM alternatif ORDER BY id_alternatif");
while($h = $q->fetch_array()){
    $id = $h['id_alternatif'];
    
    $daftar .= '
      <tr>
        <td class="text-center"></td>
        <td class="text-nowrap">'.htmlspecialchars($h['kode']).'</td>
        <td class="text-nowrap">'.htmlspecialchars($h['nama']).'</td>';
    foreach ($kriteria as $key => $value) {
        $nilai = isset($nilai_kriteria[$id][$value['id']]) ? $nilai_kriteria[$id][$value['id']] : '';
        $daftar .= '<td class="text-center">'.htmlspecialchars($nilai).'</td>';
    }
    $daftar .= '
        <td class="text-center text-nowrap">
            <a href="'.$link_update.'/?id='.$id.'" class="btn btn-primary btn-sm"><i class="ft-edit"></i></a>
            <a href="#" class="btn btn-danger btn-sm btn_delete" data-id="'.$id.'"><i class="ft-trash-2"></i></a>
        </td>
      </tr>
    ';
}
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="content-header">Alternatif</div>
        </div>
    </div>
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <a href="<?php echo $link_update; ?>" class="btn btn-success"><i class="ft-plus"></i> Alternatif Baru</a>
                        <button class="btn btn-primary" onclick="document.getElementById('fileInput').click();"><i class="ft-upload"></i> Pilih File Dataset</button>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="tabel">
                                    <thead>
                                        <tr>
                                            <th width="40" rowspan="2" class="align-top">NO</th>
                                            <th rowspan="2" class="align-top">KODE</th>
                                            <th rowspan="2" class="align-top">NAMA</th>
                                            <th class="text-center" colspan="<?php echo count($kriteria); ?>">NILAI</th>
                                            <th width="70" rowspan="2" class="align-top">AKSI</th>
                                        </tr>
                                        <tr>
                                            <?php  
                                            foreach ($kriteria as $key => $value) {
                                                echo '<th class="text-center">'.strtoupper($value['nama']).'</th>';
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $daftar;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form id="uploadForm" action="<?php echo $link_import; ?>" method="post" enctype="multipart/form-data">
                            <input type="file" id="fileInput" name="file" accept=".xlsx,.xls" style="display:none;" required>
                        </form>
                        <button class="btn btn-success" onclick="document.getElementById('uploadForm').submit();"><i class="ft-check"></i> Unggah</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    var t = $('#tabel').DataTable( {
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": [0,<?php echo count($kriteria)+3; ?>]
        } ],
        "order": [[ 1, 'asc' ]]
    } );
 
    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    $("#tabel").on("click", ".btn_delete", function(){
        var id = $(this).data('id');
        Swal.fire({
            text: "Anda yakin akan menghapus data ini ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: "btn btn-primary", cancelButton: "btn btn-danger" },
        }).then((result) => {
            if (result.isConfirmed) {
                data = [];
                data.push({'name': 'id', 'value': id});
                data.push({'name': 'delete', 'value': 'true'});
                $.ajax({
                    type: 'POST',
                    url: '<?php echo $link_list; ?>',
                    data: data,
                    error: function(xhr, status, error) {
                        Swal.fire({
                            text: xhr.responseText,
                            icon: "error",
                            buttonsStyling: !1,
                            confirmButtonText: "OK",
                            customClass: { confirmButton: "btn btn-primary" },
                        });
                    },
                    success: function(data) {
                        Swal.fire({
                            position: 'top-center',
                            icon: 'success',
                            text: 'Data berhasil dihapus',
                            showConfirmButton: false,
                            timer: 1500
                        }).then((result) => {
                            location.reload();
                        })
                    }
                });
            }
        })        
        return false;
    });
})
</script>
</body>
</html>