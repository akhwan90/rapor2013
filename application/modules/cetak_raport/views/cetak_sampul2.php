<!DOCTYPE html>
<html>
<head>
	<title>Cetak Raport</title>
	<style type="text/css">
		body {font-family: arial; font-size: 12pt}
		.table {border-collapse: collapse; border: solid 1px #999; width:100%}
		.table tr td, .table tr th {border:  solid 1px #999; padding: 3px; font-size: 12px}
		.rgt {text-align: right;}
		.ctr {text-align: center;}
		table tr td {vertical-align: top}
	</style>
</head>
<body>
	<center>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<b>LAPORAN</b><br><br>
		HASIL PENCAPAIAN KOMPETENSI PESERTA DIDIK<br>
		<?php echo strtoupper($c['sekolah_nama']); ?>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<table style="margin-left:10%; width: 70%">
			<tr>
				<td width="20%">Nama Madrasah</td>
				<td width="2%">:</td>
				<td width="50%"><?php echo strtoupper($c['sekolah_nama']); ?></td>
			</tr>
			<tr>
				<td>NSM/NPSN</td>
				<td>:</td>
				<td><?=$c['detil_sekolah']['nss']." / ".$c['detil_sekolah']['npsn'];?></td>
			</tr>
			<tr>
				<td>Alamat</td>
				<td>:</td>
				<td><?=$c['detil_sekolah']['alamat'].", Kode Pos : ".$c['detil_sekolah']['kodepos'].", <br>Telepon : ".$c['detil_sekolah']['telp'];?></td>
			</tr>
			<tr>
				<td>Kelurahan</td>
				<td>:</td>
				<td><?=$c['detil_sekolah']['desa'];?></td>
			</tr>
			<tr>
				<td>Kecamatan</td>
				<td>:</td>
				<td><?=$c['detil_sekolah']['kec'];?></td>
			</tr>
			<tr>
				<td>Kabupaten/Kota</td>
				<td>:</td>
				<td><?=$c['detil_sekolah']['kab'];?></td>
			</tr>
			<tr>
				<td>Propinsi</td>
				<td>:</td>
				<td><?=$c['detil_sekolah']['prov'];?></td>
			</tr>
			<tr>
				<td>Website</td>
				<td>:</td>
				<td><?=$c['detil_sekolah']['web'];?></td>
			</tr>
			<tr>
				<td>Email</td>
				<td>:</td>
				<td><?=$c['detil_sekolah']['email'];?></td>
			</tr>
		</table>



	</center>
	
</body>
</html>