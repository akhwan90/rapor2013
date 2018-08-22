<!DOCTYPE html>
<html>
<head>
	<title>Cetak Nilai Sikap Spiritual</title>
	<style type="text/css">
		body {font-family: arial; font-size: 12pt}
		.table {border-collapse: collapse; border: solid 1px #999; width:100%}
		.table tr td, .table tr th {border:  solid 1px #999; padding: 3px; font-size: 12px}
		.rgt {text-align: right;}
		.ctr {text-align: center;}
	</style>
</head>
<body>
	
	
	<p align="center"><b>REKAP NILAI SIKAP SPIRITUAL</b>
	<br><?php echo "Kelas : ".$detil_data['nmkelas'].", Nama Wali : ".$detil_data['nmguru']; ?></p>

	<table class="table">
		<thead>
			<tr>
				<th width="3%">No</th>
				<th width="20%">Nama</th>
				<th width="30%">Selalu Dilakukan</th>
				<th width="15%">Mulai Meningkat</th>
				<th width="32%">Deskripsi</th>
			</tr>
		</thead>

		<tbody>
			<?php 
			$html = "";
			if (!empty($data_nilai)) {
				$no = 0;
				foreach ($data_nilai as $d) {
					$no++;

					$pc_selalu = explode("-", $d['selalu']);
					$mulai_meningkat = $d['mulai_meningkat'];

					$html .= '<tr><td class="ctr">'.$no.'</td><td>'.$d['nama'].'</td>';

					$idx0 = $pc_selalu[0];
					$idx1 = $pc_selalu[1];

					$idx00 = $list_kd[$idx0];
					$idx11 = $list_kd[$idx1];
					$idx22 = $list_kd[$mulai_meningkat];

					$html .= '<td>'.$idx00.', '.$idx11.'</td><td>'.$idx22.'</td><td>SELALU melakukan sikap : '.$idx00.', '.$idx11.'; MULAI MENINGKAT pada sikap : '.$idx22.'.</td></tr>';

				}
			} else {
				$html .= '<tr><td colspan="5">Belum ada data</td></tr>';
			}
			echo $html;
			?>
		</tbody>



	</table>

</body>
</html>