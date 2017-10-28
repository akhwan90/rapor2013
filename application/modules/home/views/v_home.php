<div class="card">
    <div class="header">
        <h4 class="title">Selamat datang di Aplikasi Raport Online</h4>
    </div>
    <div class="content">
        <?php 
        $wali_kelas = $this->session->userdata('app_rapot_walikelas');
        $is_wali = $wali_kelas['is_wali'];
        ?>
        <div style="display: inline; float: left"><i class="fa fa-user fa-5x"></i></div> 
        <div style="display: inline; float: left; margin-left: 50px; margin-top: 5px">
            Nama <b>: <?php echo $this->session->userdata('app_rapot_nama'); ?></b><br>
            NIP <b>: <?php echo $this->session->userdata('app_rapot_nip'); ?></b><br>
            Wali Kelas <b>: <?php echo $wali = $is_wali == true ? "Ya, kelas : ".$wali_kelas['nama_walikelas'] : "Tidak"; ?></b><br>
        </div>
    </div>
</div>

<div class="card col-md-6">
    <div class="header">
        <h4 class="title">Statistik</h4>
    </div>
    <div class="content">
        <table class="table table-bordered table-stripped">
            <tr><td>Jumlah Siswa Laki-laki</td><td class="ctr"><?php echo $jml_siswa['jml_l']; ?></td></tr>
            <tr><td>Jumlah Siswa Perempuan</td><td class="ctr"><?php echo $jml_siswa['jml_p']; ?></td></tr>
            <tr><td>Jumlah Total Siswa</td><td class="ctr"><?php echo ($jml_siswa['jml_l']+$jml_siswa['jml_p']); ?></td></tr>
            <tr><td>Jumlah Guru</td><td class="ctr"><?php echo ($jml_guru['jml']); ?></td></tr>
        </table>
    </div>
</div>

<div class="col-md-1"></div>
<?php 
if (!empty($stat_kelas)) {
?>
<div class="card col-md-5">
    <div class="header">
        <h4 class="title">Statistik Kelas : <?php echo $wali_kelas['nama_walikelas']; ?></h4>
    </div>
    <div class="content">
    	<table class="table table-bordered table-stripped">
            <tr><td>Jumlah Siswa Laki-laki</td><td class="ctr"><?php echo $stat_kelas['jmlk_l']; ?></td></tr>
            <tr><td>Jumlah Siswa Perempuan</td><td class="ctr"><?php echo $stat_kelas['jmlk_p']; ?></td></tr>
            <tr><td>Jumlah Total Siswa</td><td class="ctr"><?php echo ($stat_kelas['jmlk_l']+$stat_kelas['jmlk_p']); ?></td></tr>
        </table>
    </div>
</div>
<?php 
}
?>  
