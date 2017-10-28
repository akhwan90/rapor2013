<!DOCTYPE html>
<html>
<head>
	<title>Cetak Leger Nilai Pengetahuan</title>
	<style type="text/css">
		body {font-family: arial; font-size: 12pt}
		.table {border-collapse: collapse; border: solid 1px #999; width:100%}
		.table tr td, .table tr th {border:  solid 1px #999; padding: 3px; font-size: 12px}
		.rgt {text-align: right;}
		.ctr {text-align: center;}
	</style>
</head>
<body>
	<h4>Rekap Nilai Pengetahuan dan Keterampilan, <?php echo $teks_tasm; ?></h4>
	<hr style="border: solid 1px #000; margin-top: -10px; margin-bottom: 20px">
	<?php echo $html; ?>
	
	<p align="right" style="font-size: 10pt; font-style: italic;">Rendered in {elapsed_time}</p>
</body>
</html>