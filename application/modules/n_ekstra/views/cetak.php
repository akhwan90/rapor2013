<!DOCTYPE html>
<html>
<head>
	<title>Cetak Nilai Pengetahuan</title>
	<style type="text/css">
		body {font-family: arial; font-size: 12pt}
		.table {border-collapse: collapse; border: solid 1px #999; width:100%}
		.table tr td, .table tr th {border:  solid 1px #999; padding: 3px; font-size: 12px}
		.rgt {text-align: right;}
		.ctr {text-align: center;}
	</style>
</head>
<body>
	<?php 
	//hitung porsi 
	$jumlah_porsi = $this->config->item('pnp_h')+$this->config->item('pnp_t')+$this->config->item('pnp_a');

	$porsi_h = (($this->config->item('pnp_h') / $jumlah_porsi) * 100) / 100;
	$porsi_t = (($this->config->item('pnp_t') / $jumlah_porsi) * 100) / 100;
	$porsi_a = (($this->config->item('pnp_a') / $jumlah_porsi) * 100) / 100;

	$size_array = sizeof($meta_data);
	$np = $size_array-3;

	$width_th = 100-((5*$size_array-2)+(5*8)+3);
	?>
	
	<p align="center"><b>REKAP NILAI PENGETAHUAN</b>
	<br>
	Mata Pelajaran : <?php echo $detil_mapel_guru['nmmapel'].", Kelas : ".$detil_mapel_guru['nmkelas'].", Guru : ".$detil_mapel_guru['nmguru']; ?></p>

	<table class="table">
		<thead>
			<tr>
				<th rowspan="2" width="3%">No</th>
				<th rowspan="2" width="<?php echo $width_th; ?>%">Nama</th>
				<th colspan="<?php echo $np; ?>">KD</th>
				<th rowspan="2" width="5%">Rata-rata UH (<?php echo $this->config->item('pnp_h'); ?>)</th>
				<th rowspan="2" width="5%">UTS (<?php echo $this->config->item('pnp_t'); ?>)</th>
				<th rowspan="2" width="5%">UAS (<?php echo $this->config->item('pnp_a'); ?>)</th>
				<th rowspan="2" width="5%">Nilai Akhir</th>
				<th rowspan="2" width="5%">Predikat</th>
				<th rowspan="2" width="15%">Deskripsi</th>
			</tr>
			<tr>
				<?php 
				for ($i = 1; $i < ($size_array-2); $i++) {
					echo '<th width="5%">'.$meta_data[$i].'</th>';
				}
				?>
			</tr>
		</thead>

		<tbody>
			<?php 
			$html = "";
			if (!empty($data)) {
				$no = 0;
				foreach ($data as $d) {
					$no++;
					$html .= '<tr><td class="ctr">'.$no.'</td><td>'.$d['nama'].'</td>';

					$jml = 0;
					$total = 0; 
					$teks_deskripsi = array();

					for ($i = 1; $i < ($size_array-2); $i++) {

						//ambil nama fieldnya
						$field = $meta_data[$i];

						//ambil datanya
						$pc_field = explode("-", $d[$field]);
						
						$jml++;
						$total += $pc_field[0];

						//teks nilai deskripsi
						$teks_deskripsi[] = nilai_pre($pc_field[0])." pada ".$pc_field[1];
						
						$html .= '<td class="rgt">'.$pc_field[0].'</td>';
					}

					$nilai_akhir = ($porsi_h*($total/$jml))+($porsi_t*$d['nilai_uts'])+($porsi_a*$d['nilai_uas']);

					$nilai_huruf = nilai_huruf($nilai_akhir);
					$nilai_deskripsi = '<span style="font-size: 10px">'.implode(", ", $teks_deskripsi).'</span>';

					$html .= '<td class="rgt">'.number_format($total/$jml).'</td><td class="rgt">'.$d['nilai_uts'].'</td><td class="rgt">'.$d['nilai_uas'].'</td><td class="rgt">'.number_format($nilai_akhir).'</td><td class="ctr">'.$nilai_huruf.'</td><td>'.$nilai_deskripsi.'</td></tr>';

				}
			}
			echo $html;
			?>
		</tbody>



	</table>

</body>
</html>