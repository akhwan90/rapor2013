<div class="row">
    
    <div class="col-md-12">
        <div class="alert alert-warning" style="color: #000">
            <b>Petunjuk : </b><br>
            Menu ini digunakan untuk menginput nilai pada setiap masing-masing mata pelajaran diampu. Silakan klik menu <b><i>Nilai Pengetahuan</i></b> untuk menginput nilai pengetahuan, dan <b><i>Nilai Keterampilan</i></b> untuk menginput nilai keterampilan.
        </div>
    </div>
    <?php 
    if (!empty($list_mapelkelas)) {
        foreach ($list_mapelkelas as $mk) {
    ?>
    <div class="col-md-4">
        <div class="card">
            <div class="header">
                <h4 class="title"><?php echo $mk['nmmapel']." - ".$mk['nmkelas']; ?>    
                <!--
                <a href="#" class="btn btn-success btn-xs pull-right"><i class="fa fa-check"></i> </a>
                -->
                </h4>
            </div>
            <div class="content">
                <ul class="list-group">
                    <li class="list-group-item"><a href="<?php echo base_url()."n_pengetahuan/index/".$mk['id']; ?>"><i class="fa fa-chevron-right"></i>  Nilai Pengetahuan</a> <!--<a href="#" class="badge pull-right"><i class="fa fa-print"></i> --></li>
                    <li class="list-group-item"><a href="<?php echo base_url()."n_keterampilan/index/".$mk['id']; ?>"><i class="fa fa-chevron-right"></i>  Nilai Keterampilan</a></li><!-- 
                    <li class="list-group-item"><a href="<?php echo base_url()."view_mapel/cetak_absensi/".$mk['id']; ?>" target="_blank"><i class="fa fa-chevron-right"></i>  Cetak Presensi</a></li> -->
                    <!--
                    <?php 
                    if ($mk['is_sikap'] == "1") {
                    ?>
                    <li class="list-group-item"><a href="<?php echo base_url()."n_sikap_sp/index/".$mk['id']; ?>">Nilai Sikap Spritual</a></li>
                    <li class="list-group-item"><a href="<?php echo base_url()."n_sikap_so/index/".$mk['id']; ?>">Nilai Sikap Sosial</a></li>
                    <?php 
                    }
                    ?>
                    -->
                </ul>
            </div>
        </div>
    </div>
    <?php 
        }
    } else {
        echo '<div class="alert alert-info">Belum ada mapel yang diampu..</div>';
    }
    ?>
</div>