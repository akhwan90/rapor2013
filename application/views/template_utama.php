<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="<?php echo base_url(); ?>aset/img/favicon.ico">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Aplikasi Raport Kurikulum 2013. Copyright &copy; nur-akhwan.web.id</title>
    <link href="<?php echo base_url(); ?>aset/css/bootstrap.min.css" rel="stylesheet" />
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <!-- Bootstrap core CSS     -->
    <!-- Animation library for notifications   -->
    <link href="<?php echo base_url(); ?>aset/css/animate.min.css" rel="stylesheet"/>
    <!--  Light Bootstrap Table core CSS    -->
    <link href="<?php echo base_url(); ?>aset/css/light-bootstrap-dashboard.css" rel="stylesheet"/>
    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="<?php echo base_url(); ?>aset/css/demo.css" rel="stylesheet" />
    <!--     Fonts and icons     
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    -->
    <link href="<?php echo base_url(); ?>aset/css/pe-icon-7-stroke.css" rel="stylesheet" />
    
    <!-- PLUGIN -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>aset/plugins/datatables/dataTables.bootstrap.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>aset/plugins/fa/css/font-awesome.min.css">
    <!-- Javascript Files -->
    <!--   Core JS Files   -->
    <script src="<?php echo base_url(); ?>aset/js/jquery-1.10.2.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>aset/js/bootstrap.min.js" type="text/javascript"></script>
    <!--  Checkbox, Radio & Switch Plugins -->
    <script src="<?php echo base_url(); ?>aset/js/bootstrap-checkbox-radio-switch.js"></script>
    <!--  Charts Plugin -->
    <script src="<?php echo base_url(); ?>aset/js/chartist.min.js"></script>
    <!--  Notifications Plugin    -->
    <script src="<?php echo base_url(); ?>aset/js/bootstrap-notify.js"></script>
    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
    <script src="<?php echo base_url(); ?>aset/js/light-bootstrap-dashboard.js"></script>
    <!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
    <script src="<?php echo base_url(); ?>aset/js/demo.js"></script>
    <script src="<?php echo base_url(); ?>aset/js/js.cookie.js"></script>
    <!--- PLUGINS -->
    <!-- datatables -->
    <script src="<?php echo base_url(); ?>aset/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>aset/plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>aset/plugins/pairselect/pair-select.min.js"></script>
    <script type="text/javascript">
        base_url = "<?php echo base_url(); ?>";   
        
        function noti(tipe, value) {
            $.notify({
                icon: 'pe-7s-info',
                message: '<strong>Informasi</strong><p>'+value+'</p>'
            },{
                type: tipe,
                timer: 1000
            });
            return true;
        } 
        function getFormData($form){
            var unindexed_array = $form.serializeArray();
            var indexed_array = {};
            $.map(unindexed_array, function(n, i){
                indexed_array[n['name']] = n['value'];
            });
            return indexed_array;
        }
        function pagination(indentifier, url, config) {
            $('#'+indentifier).DataTable({
                "language": {
                    "url": base_url+"<?php echo base_url(); ?>aset/plugins/datatables/Indonesian.json"
                },
                "ordering": false,
                "columnDefs": config,
                "bProcessing": true,
                "serverSide": true,
                "bDestroy" : true,
                "ajax":{
                    url : url, // json datasource
                    type: "post",  // type of method  , by default would be get
                    error: function(){  // error handling code
                        $("#"+indentifier).css("display","none");
                    }
                }
            }); 
        }
        function getFormData($form){
            var unindexed_array = $form.serializeArray();
            var indexed_array = {};
            $.map(unindexed_array, function(n, i){
                indexed_array[n['name']] = n['value'];
            });
            return indexed_array;
        }
    </script>
    <style type="text/css">
        #datatabel {width: 100%}
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar" data-color="blue">
    <!--
        Tip 1: you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple"
        Tip 2: you can also add an image using data-image tag
    -->
    	<div class="sidebar-wrapper">
            <div class="logo">
                <a href="<?php echo base_url(); ?>" class="simple-text">
                    <i class="pe-7s-study"></i> Raport K13
                </a>
            </div>
            <ul class="nav">
                <?php 
                $prefix = $this->config->item('session_name_prefix');
                
                $walikelas = $this->session->userdata($prefix."walikelas");
                echo generate_menu($admlevel,$walikelas['is_wali']);  
                ?>
            </ul>
            
            
    	</div>
    </div>
    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo base_url(); ?>"><i class="pe-7s-study"></i> Raport K13</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <?php 
                        
                        if ($this->session->userdata($prefix."valid") == true) {
                        ?>
                        <li><a href="#">Login Sebagai : <?php echo $this->session->userdata($prefix."user"); ?> </a></li>
                        <li>
                            <a href="<?php echo base_url(); ?>login/logout" onclick="return hilangkan_gambar();">
                                Log out
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="content">
            <div class="container-fluid">
                <?php $this->load->view($p); ?>
            </div>
        </div>
        <footer class="footer">
            <div class="container-fluid">
                <p class="copyright pull-left">
                    <b><?php echo $this->config->item('nama_sekolah'); ?></b>
                </p>
                <p class="copyright pull-right">
                    &copy; <a href="http://nur-akhwan.web.id/">Nur-Akhwan.Web.Id</a> @28 Oktober 2017.
                    Themes by <a href="https://www.creative-tim.com/">Light Bootstrap Dashboard</a>
                </p>
            </div>
        </footer>
    </div>
</div>
</body>
</html>
