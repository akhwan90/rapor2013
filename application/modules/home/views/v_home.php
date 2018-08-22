<div class="card">
    <div class="header">
        <h4 class="title">Selamat datang di Aplikasi Raport Online</h4>
    </div>
    <div class="content">
        <?php 
        $wali_kelas = $this->session->userdata('app_rapot_walikelas');
        $is_wali = $wali_kelas['is_wali'];
        ?>
        <div style="display: inline; float: left"><i class="fa fa-user fa-5x"></i></div> 
        <div style="display: inline; float: left; margin-left: 50px; margin-top: 5px">
            Nama <b>: <?php echo $this->session->userdata('app_rapot_nama'); ?></b><br>
            NIP <b>: <?php echo $this->session->userdata('app_rapot_nip'); ?></b><br>
            Wali Kelas <b>: <?php echo $wali = $is_wali == true ? "Ya, kelas : ".$wali_kelas['nama_walikelas'] : "Tidak"; ?></b><br>
        </div>
    </div>
</div>
<!--
<div class="card col-md-6">
    <div class="header">
        <h4 class="title">Sudah Input Nilai Ketrampilan</h4>
    </div>
    <div class="content">
        <table class="table table-bordered table-stripped table-condensed">
        	<thead>
        		<tr>
        			<th>Nama Guru</th>
        			<th>Mapel</th>
        		</tr>
        	</thead>

        	<?php 
        	$arr_nk = array();
        	if (!empty($guru_input_nk)) {
        		foreach ($guru_input_nk as $k) {
        			echo '<tr><td>'.$k['nmguru']."</td><td>".$k['nmmapel']."-".$k['nmkelas']."</td></tr>";
        			$arr_nk[] = $k['id_guru'];
        		}
        		echo '<tr><td colspan="2"><b>Lainnya Belum :D</b></td></tr>';
        	} else {
        		echo '<tr><td colspan="2">Belum semua</td></tr>';
        	}
        	?>
        </table>
    </div>
</div>

<div class="col-md-1"></div>

<div class="card col-md-5">
    <div class="header">
        <h4 class="title">Sudah Input Nilai Pengetahuan</h4>
    </div>
    <div class="content">
        <table class="table table-bordered table-stripped table-condensed">
        	<thead>
        		<tr>
        			<th>Nama Guru</th>
        			<th>Mapel</th>
        		</tr>
        	</thead>

        	<?php 
            $arr_np = array();
        	if (!empty($guru_input_np)) {
        		foreach ($guru_input_np as $k) {
        			echo '<tr><td>'.$k['nmguru']."</td><td>".$k['nmmapel']."-".$k['nmkelas']."</td></tr>";
        			$arr_np[] = $k['id_guru'];
        		}
        		echo '<tr><td colspan="2"><b>Lainnya Belum :D</b></td></tr>';
        	} else {
        		echo '<tr><td colspan="2">Belum semua</td></tr>';
        	}
        	?>
        </table>
    </div>
</div>
-->
<script type="text/javascript">
    /*
	<?php 
    $nilai = 0;
    $ket = "";
    if (!in_array($this->session->userdata('app_rapot_konid'), $arr_nk)) {
        $ket .= "<span style=\'color: red\'><i class=\'fa fa-thumbs-down\'></i> Nilai Ketrampilan belum diisi..!<br></span>";
    } else {
        $ket .= "<span style=\'color: green\'><i class=\'fa fa-thumbs-up\'></i> Nilai Ketrampilan sudah diisi..!<br></span>";
        $nilai++;
    }
	if (!in_array($this->session->userdata('app_rapot_konid'), $arr_np)) {
        $ket .= "<span style=\'color: red\'><i class=\'fa fa-thumbs-down\'></i> Nilai Pengetahuan belum diisi..!<br></span>";
	} else {
        $ket .= "<span style=\'color: green\'><i class=\'fa fa-thumbs-up\'></i> Nilai Pengetahuan sudah diisi..!<br></span>";
        $nilai++;
    }

    if ($nilai < 2) {
        echo "swal(
          'Waduuhhh..',
          '<img src=\\'http://4.bp.blogspot.com/-hZpp9lVapgw/VJZ6grV3NBI/AAAAAAAAAbw/tltdqsQnaFQ/s1600/angerywoman.jpg\\' style=\\'width: 160px; height: 180px\\'><br><br>Pak Guru/Bu Guru ngisi nilainya belum lengkap..!!<br>".$ket."',
          'error'
        );";
    } else {
        echo "swal(
          'Baguss..',
          '<img src=\\'https://gozzip.id/system/App/BlogBody/photos/000/118/272/original/4a8a08f09d37b73795649038408b5f33.jpg\\' style=\\'width: 390px; height: 260px\\'><br><br>Pak Guru/Bu Guru sudah ngisi nilai..!!<br>".$ket."',
          'success'
        );";
    }
	?>
    */
</script>