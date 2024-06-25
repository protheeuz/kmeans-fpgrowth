<?php if(!defined('myweb')){ exit(); }?>
<?php  

$link_list = $www.'cluster';
$link_update = $www.'cluster_update';

$error = '';
$success = '';
$id = '';
$action = 'add';
$kode = '';
$nama = '';

$kriteria = array();
$q = $con->query("SELECT * FROM kriteria ORDER BY kode");
while($h = $q->fetch_array()){
	$kriteria[] = array('id'=>$h['id_kriteria'], 'nama'=>htmlspecialchars($h['nama']));
}

if(isset($_POST['save'])){
	$id = $_POST['id'];
	if($id != ''){
		$action = 'edit';
	}
	$kode = $_POST['kode'];
	$nama = $_POST['nama'];
	
	if(empty($kode) or empty($nama)){
		$error = 'Lengkapi kode dan nama terlebih dahulu';
	}else{
		if($action == 'add'){
			$q = $con->query("SELECT * FROM cluster WHERE kode='".escape($kode)."'");
			if ($q->num_rows > 0) {
				$error = 'Kode sudah terdaftar';
			}else{
				$con->query("INSERT INTO cluster(kode, nama) VALUES('".escape($kode)."', '".escape($nama)."')");
				$id_cluster = $con->insert_id;
				foreach ($kriteria as $key => $value) {
					if(!empty($_POST['nilai_'.$value['id']])){
						$nilai = $_POST['nilai_'.$value['id']];
					}else{
						$nilai = 0;
					}
					$con->query("INSERT INTO center_points(id_cluster, id_kriteria, nilai) VALUES('".escape($id_cluster)."', '".escape($value['id'])."', '".escape($nilai)."')");
				}
				$success = 'Data cluster berhasil disimpan';
			}

		}elseif($action == 'edit'){
			$q = $con->query("SELECT * FROM cluster WHERE kode='".escape($kode)."' and id_cluster <> '".escape($id)."'");
			if ($q->num_rows > 0) {
				$error = 'Kode sudah terdaftar';
			}else{
				$con->query("UPDATE cluster SET kode='".escape($kode)."', nama='".escape($nama)."' WHERE id_cluster='".escape($id)."'");
				$con->query("DELETE FROM center_points WHERE id_cluster='".escape($id)."'");
				
				foreach ($kriteria as $key => $value) {
					if(!empty($_POST['nilai_'.$value['id']])){
						$nilai = $_POST['nilai_'.$value['id']];
					}else{
						$nilai = 0;
					}
					$con->query("INSERT INTO center_points(id_cluster, id_kriteria, nilai) VALUES('".escape($id)."', '".escape($value['id'])."', '".escape($nilai)."')");
				}
				$success = 'Data cluster berhasil diperbarui';
			}
		}
		
	}
	if(!empty($error)){
		header('HTTP/1.1 500 Internal Server Error');
		echo $error;
	}elseif(!empty($success)){
		echo $success;
	}
	die;


}else{
	if(isset($_GET['id'])){
		$id = $_GET['id'];
		$action = 'edit';
		$q = $con->query("SELECT * FROM cluster WHERE id_cluster='".escape($id)."'");
		$h = $q->fetch_assoc();
		$kode = $h['kode'];
		$nama = $h['nama'];
	}
}

$center_points = array();
$q = $con->query("SELECT * FROM center_points WHERE id_cluster = '".escape($id)."'");
while($h = $q->fetch_array()){
	$center_points[$h['id_cluster']][$h['id_kriteria']] = $h['nilai'];
}

if($action=='add'){$header='Input Data Cluster';}else{$header='Ubah Data Cluster';}

?>
<div class="content-wrapper">
	<div class="row">
		<div class="col-12">
			<div class="content-header"><?php echo $header;?></div>
		</div>
	</div>
	<section>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<!-- <div class="card-header">
			          <h4 class="card-title">Horizontal Form</h4>
			        </div> -->
					<div class="card-content">
						<div class="card-body">
							<form action="<?php echo $link_update;?>" method="post" id="form_edit">
								<input name="id" type="hidden" value="<?php echo $id;?>">
								<div class="form-group">
									<label for="kode" class="col-form-label">Kode <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="kode" id="kode" value="<?php echo htmlspecialchars($kode);?>" autofocus required>
								</div>
								<div class="form-group">
									<label for="nama" class="col-form-label">Nama Cluester <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="nama" id="nama" value="<?php echo htmlspecialchars($nama);?>" required>
								</div>
								<?php  
								foreach ($kriteria as $key => $value) {
									$nilai = '';
									if(isset($center_points[$id][$value['id']])){
										$nilai = $center_points[$id][$value['id']];
									}
									echo '
									<div class="form-group">
										<label for="nilai_'.$value['id'].'" class="col-form-label">Nilai Center Points '.$value['nama'].'</label>
										<input type="number" class="form-control" name="nilai_'.$value['id'].'" id="nilai_'.$value['id'].'" value="'.$nilai.'" >
									</div>
									';
								}

								?>
								<div class="form-group">
									<a href="<?php echo $link_list;?>" class="btn btn-primary"><i class="ft-x"></i> Batal</a>
									<button type="submit" name="save" id="btn_save" class="btn btn-success"><i class="ft-check"></i> Simpan </button>
								</div>
							</form>

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


</div>
<script type="text/javascript">
$(document).ready(function () {
	$('#form_edit').submit(function (e) {
		data = $(this).serializeArray();
		data.push({'name': 'save', 'value': 'true'});
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: data,
			beforeSend: function(data) {
		        $('#btn_save').prop('disabled', true);
				$('#alert_error').hide();
			},
			error: function(xhr, status, error) {
		        $('#btn_save').prop('disabled', false);
                Swal.fire({
					text: xhr.responseText,
					icon: "error",
					buttonsStyling: !1,
					confirmButtonText: "OK",
					customClass: { confirmButton: "btn btn-primary" },
				});
			},
			success: function(data) {
		        $('#btn_save').prop('disabled', false);
                Swal.fire({
					text: data,
					icon: "success",
					buttonsStyling: !1,
					confirmButtonText: "OK",
					customClass: { confirmButton: "btn btn-primary" },
				}).then(function () {
					window.location.href = '<?php echo $link_list;?>';
				});
			}
		});
		e.preventDefault();
	});



})
</script>