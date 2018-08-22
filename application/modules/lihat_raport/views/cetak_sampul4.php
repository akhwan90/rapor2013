<!DOCTYPE html>
<html>
<head>
	<title>Cetak Raport</title>
	<style type="text/css">
		body {font-family: arial; font-size: 12pt; width: 8.5in; height: 12.5in; border: solid 2px #000; padding: 30px 10px}
		.table {border-collapse: collapse; border: solid 1px #999; width:100%}
		.table tr td, .table tr th {border:  solid 1px #999; padding: 3px; font-size: 12px}
		.rgt {text-align: right;}
		.ctr {text-align: center;}
		table tr td {vertical-align: top}
	</style>
</head>
<body>
	<table>
		<tr>
			<td width="10%">
			<img src="<?php echo base_url(); ?>aset/img/logo_kemenag.png" style="margin-top: 10px; width: 90px; height: 80px">
			</td>
			<td style="text-align: center"><b>KEMENTERIAN AGAMA REPUBLIK INDONESIA<br>
			KANTOR KEMENTERIAN AGAMA KABUPATEN KULON PROGO<br>
			MADRASAH TSANAWIYAH NEGERI SIDOHARJO<br></b>
			<span style="font-size: 10pt">Alamat : Sumoroto, Sidoharjo, Samigaluh, Kulon Progo. <br>
			Telp : 0811 267 5969 | Web : http://mtsn-sidoharjo.sch.id/</span>
			</td>
		</tr>
		<tr><td colspan="2"><hr style="border: solid 2px #000"></td></tr>
		<tr><td colspan="2" style="text-align: center; font-weight: bold; font-size: 14pt">KETERANGAN DIRI TENTANG PESERTA DIDIK</td></tr>
		<tr><td colspan="2">
			
			<table width="100%">
				<tr><td width="3%">1.</td><td width="40%">Nama Peserta Didik (Lengkap)</td><td width="2%">:</td><td width="55%"><?php echo $ds['nama']; ?></td></tr>
				<tr><td>2.</td><td>Nomor Induk</td><td>:</td><td><?php echo $ds['nis']; ?></td></tr>
				<tr><td>3.</td><td>NISN</td><td>:</td><td><?php echo $ds['nisn']; ?></td></tr>
				<tr><td>4.</td><td>Tempat, Tanggal Lahir</td><td>:</td><td><?php echo $ds['tmp_lahir'].", ".tjs($ds['tgl_lahir'],"l"); ?></td></tr>
				<tr><td>5.</td><td>Jenis Kelamin</td><td>:</td><td><?php echo jk($ds['jk']); ?></td></tr>
				<tr><td>6.</td><td>Agama</td><td>:</td><td><?php echo $ds['agama']; ?></td></tr>
				<tr><td>7.</td><td>Anak Ke</td><td>:</td><td><?php echo $ds['anakke']; ?></td></tr>
				<tr><td>8.</td><td>Status dalam Keluarga</td><td>:</td><td><?php echo status_anak($ds['status']); ?></td></tr>
				<tr><td>9.</td><td>Alamat Peserta Didik</td><td>:</td><td><?php echo $ds['alamat']; ?></td></tr>
				<tr><td>10.</td><td>Diterima di Madrasah ini</td><td>:</td><td></td></tr>
				<tr><td></td><td>a. Di Kelas</td><td>:</td><td><?php echo $ds['diterima_kelas']; ?></td></tr>
				<tr><td></td><td>b. Pada Tanggal</td><td>:</td><td><?php echo tjs($ds['diterima_tgl'],"l"); ?></td></tr>
				<tr><td></td><td>c. Semester</td><td>:</td><td><?php echo $ds['diterima_smt']; ?></td></tr>
				<tr><td>11.</td><td>Madrasah/Sekolah Asal</td><td>:</td><td></td></tr>
				<tr><td></td><td>a. Nama Madrasah / Sekolah</td><td>:</td><td><?php echo $ds['sek_asal']; ?></td></tr>
				<tr><td></td><td>b. Alamat</td><td>:</td><td><?php echo $ds['sek_asal_alamat']; ?></td></tr>
				<tr><td>12.</td><td>Ijazah SD / MI / Paket A</td><td>:</td><td></td></tr>
				<tr><td></td><td>a. Nomor</td><td>:</td><td><?php echo $ds['ijazah_no']; ?></td></tr>
				<tr><td></td><td>b. Tahun</td><td>:</td><td><?php echo $ds['ijazah_thn']; ?></td></tr>
				<tr><td>13.</td><td colspan="3">Surat Keterangan Hasil Ujian Akhir Sekolah Berstandar Nasional (UASBN) SD / MI / Paket A</td></tr>
				<tr><td></td><td>a. Nomor</td><td>:</td><td><?php echo $ds['skhun_no']; ?></td></tr>
				<tr><td></td><td>b. Tahun</td><td>:</td><td><?php echo $ds['skhun_thn']; ?></td></tr>
				<tr><td>14.</td><td>Orang Tua</td><td>:</td><td></td></tr>
				<tr><td></td><td>a. Nama Ayah</td><td>:</td><td><?php echo $ds['ortu_ayah']; ?></td></tr>
				<tr><td></td><td>b. Nama Ibu</td><td>:</td><td><?php echo $ds['ortu_ibu']; ?></td></tr>
				<tr><td></td><td>c. Alamat</td><td>:</td><td><?php echo $ds['ortu_alamat']; ?></td></tr>
				<tr><td></td><td>d. Telepon/HP</td><td>:</td><td><?php echo $ds['ortu_notelp']; ?></td></tr>
				<tr><td>15.</td><td>Pekerjaan Orang Tua</td><td>:</td><td></td></tr>
				<tr><td></td><td>a. Ayah</td><td>:</td><td><?php echo $ds['ortu_ayah_pkj']; ?></td></tr>
				<tr><td></td><td>b. Ibu</td><td>:</td><td><?php echo $ds['ortu_ibu_pkj']; ?></td></tr>
				<tr><td>16.</td><td>Wali</td><td>:</td><td></td></tr>
				<tr><td></td><td>a. Nama Wali</td><td>:</td><td><?php echo $ds['wali']; ?></td></tr>
				<tr><td></td><td>b. Alamat Wali</td><td>:</td><td><?php echo $ds['wali_alamat']; ?></td></tr>
				<tr><td></td><td>c. Telepon/HP</td><td>:</td><td><?php echo $ds['notelp_rumah']; ?></td></tr>
				<tr><td></td><td>d. Pekerjaan Wali</td><td>:</td><td><?php echo $ds['wali_pkj']; ?></td></tr>
			</table>
		</td>
		</tr>		
	</table>
	<br><br>
	<div style="margin-left: 20%; display: inline; float: left; width: 3cm; height: 3.7cm; border: solid 1px #000"></div>
	<div style="margin-left: 120px; display: inline; float: left;">
		Sidoharjo, <?php echo tjs($ds['diterima_tgl'],"l"); ?><br>
		Kepala Madrasah<br>
		<br>
		<br>
		<br>
		<br>
		<b><u>Drs. Sukarlan</u></b><br>
		NIP. 19650422 200012 1 001
	</div>

	
</body>
</html>