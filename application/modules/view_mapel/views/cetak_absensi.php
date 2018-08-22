<!DOCTYPE html>
<html>
<head>
	<title>Cetak Absensi</title>
	<style type="text/css">
		body {font-family: arial; font-size: 12pt}
		.table {border-collapse: collapse; border: solid 1px #000; width:100%}
		.table tr td, .table tr th {border:  solid 1px #000; padding: 1px; font-size: 12px}
		.tablef {border-collapse: collapse; border: none; width:100%}
		.tablef tr td, .table tr th {border:  none; padding: 1px; font-size: 12px}
		.rgt {text-align: right;}
		.ctr {text-align: center;}
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

	<?php 
	echo $html;
	?>
	

</body>
</html>