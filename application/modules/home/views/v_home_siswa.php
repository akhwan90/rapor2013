
<div class="card">
    <div class="header">
        <h4 class="title">Selamat datang di Aplikasi Raport Online</h4>
    </div>
    <div class="content">
        <div style="display: inline; float: left"><i class="fa fa-user fa-5x"></i></div> 
        <div style="display: inline; float: left; margin-left: 50px; margin-top: 5px">
            Nama <b>: <?php echo $this->session->userdata('app_rapot_nama'); ?></b><br>
            NIS <b>: <?php echo $this->session->userdata('app_rapot_nip'); ?></b><br>
        </div>
    </div>
</div>
