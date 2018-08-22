<!DOCTYPE html>
<html>
<head>
	<title>Cetak Prestasi dan Catatan Wali Kelas</title>
	<style type="text/css">
		body {font-family: arial; font-size: 11pt; width: 8.5in}
		.table {border-collapse: collapse; border: solid 1px #999; width:100%}
		.table tr td, .table tr th {border:  solid 1px #000; padding: 3px;}
		.table tr th {font-weight: bold; text-align: center}
		.rgt {text-align: right;}
		.ctr {text-align: center;}
		.tbl {font-weight: bold}

		table tr td {vertical-align: top}
		.font_kecil {font-size: 12px}
	</style>
    <script type="text/javascript">
        function PrintWindow() {                    
           window.print();            
           CheckWindowState();
        }
    
        function CheckWindowState()    {           
            if(document.readyState=="complete") {
                window.close(); 
            } else {           
                setTimeout("CheckWindowState()", 1000)
            }
        }
        PrintWindow();
    </script> 
</head>
<body>
<h4>E. PRESTASI</h4>
<table class="table" style="width: 96%">
	<thead>
		<tr>
			<th>No</th>
			<th>Jenis Prestasi</th>
			<th>Keterangan</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if (!empty($prestasi)) {
			$no = 1;
			foreach ($prestasi as $p) {
		?>
			<tr>
				<td><?php echo $no; ?></td>
				<td><?php echo $p['jenis']; ?></td>
				<td><?php echo $p['keterangan']; ?></td>
			</tr>
		<?php 
				$no++;
			}
		} else {
			echo '<tr><td colspan="3">-</td></tr>';
		}
		?>
	</tbody>
</table>

<h4>F. CATATAN WALI KELAS</h4>
<div style="border: solid 1px #000; padding: 20px 10px; width: 95%">
	<?php echo $catatan['catatan_wali']; ?>
</div>


<h4>G. TANGGAPAN ORANGTUA/WALI</h4>
<div style="border: solid 1px #000; padding: 20px 10px; height: 200px; width: 95%">
	
</div>

</body>

</html>