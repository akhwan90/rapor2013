<!DOCTYPE html>
<html>
<head>
	<title>Cetak Nilai Keterampilan</title>
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
	echo $html;
	/*
	<?php 
	$size_array = sizeof($meta_data);
	$np = $size_array-1;

	$width_th = 100-((10*$size_array)+(2*10)+4+20);

	?>
	
	<p align="center"><b>REKAP NILAI KETERAMPILAN</b>
	<br>
	Mata Pelajaran : <?php echo $detil_mapel_guru['nmmapel'].", Kelas : ".$detil_mapel_guru['nmkelas'].", Guru : ".$detil_mapel_guru['nmguru']; ?></p>

	<table class="table">
		<thead>
			<tr>
				<th rowspan="2" width="4%">No</th>
				<th rowspan="2" width="<?php echo $width_th; ?>%">Nama</th>
				<th colspan="<?php echo $np; ?>">KD</th>
				<th rowspan="2" width="10%">Rata-rata</th>
				<th rowspan="2" width="10%">Predikat</th>
				<th rowspan="2" width="20%">Deskripsi</th>
			</tr>
			<tr>
				<?php 
				
				for ($i = 1; $i < ($size_array); $i++) {
					echo '<th width="10%">'.$meta_data[$i].'</th>';
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

					$teks_deskripsi = array("KURANG"=>array(),"CUKUP"=>array(),"BAIK"=>array(),"SANGAT BAIK"=>array());

					for ($i = 1; $i < ($size_array); $i++) {
						
						$field = $meta_data[$i];
						$pc_field = explode("///", $d[$field]);

						if (nilai_huruf($pc_field[0]) == "D") {
							$teks_deskripsi['KURANG'][] = $pc_field[1];
						} else if (nilai_huruf($pc_field[0]) == "C") {
							$teks_deskripsi['CUKUP'][] = $pc_field[1];
						} else if (nilai_huruf($pc_field[0]) == "B") {
							$teks_deskripsi['BAIK'][] = $pc_field[1];
						} else if (nilai_huruf($pc_field[0]) == "A") {
							$teks_deskripsi['SANGAT BAIK'][] = $pc_field[1];
						} 

						$jml++;
						$total += $pc_field[0];

						
						$html .= '<td class="ctr">'.$pc_field[0].'</td>';
					}
					$nilai_rata = $total/$jml;
					$nilai_huruf = nilai_huruf($nilai_rata);
					
					$nilai_deskripsi = '';

					foreach ($teks_deskripsi as $k => $v) {
						if (!empty($v)) {
							$nilai_deskripsi .= $k.": ".implode(", ", $v).". ";
						}

					}


					$html .= '<td class="ctr">'.number_format($nilai_rata).'</td><td class="ctr">'.$nilai_huruf.'</td><td><span style="font-size: 11px">'.$nilai_deskripsi.'</span></td></tr>';

				}
			}
			echo $html;
			?>
		</tbody>



	</table>
	<!--<center style="font-size: 12px; margin-top: 20px"><i>Elapsed time : {elapsed_time} second</i></center>-->
	*/
	?>

</body>
</html>