<style type="text/css">
    .ctr {text-align: center}
    .nso {}
</style>
<div class="row">
    <div class="col-md-12">
        <p>
            <a href="<?php echo base_url(); ?>view_mapel" class="btn btn-info"><i class="fa fa-arrow-left"></i> Kembali</a>
            <a href="<?php echo base_url()."/".$url; ?>/cetak/<?php echo $this->uri->segment(3); ?>" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Cetak</a>
        </p>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Nilai Sikap Sosial </h4>
            </div>
            <div class="content">  
                <table class="table table-condensed table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%" rowspan="2">No</th>
                            <th width="35%" rowspan="2">Nama</th>
                            <th width="40%" colspan="<?php echo $jmlh_kd; ?>">Selalu Dilakukan</th>
                            <th width="20%" rowspan="2">Mulai Meningkat</th>
                        </tr>
                        <tr>
                            <?php 
                            if (!empty($list_kd)) {
                                foreach ($list_kd as $k) {
                                    echo '<th>'.$k['nama_kd'].'</th>';
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <form method="post" id="<?php echo $nama_form; ?>">
                        <input type="hidden" name="id_guru_mapel" value="<?php echo $this->uri->segment(3); ?>">
                        <input type="hidden" name="mode_form" value="<?php echo $mode_form; ?>">
                        <?php 
                        $no = 1;
                        if (!empty($siswa_kelas)) {
                            foreach ($siswa_kelas as $sk) {
                                $pc_selalu = explode(",", $sk['selalu']);
                        ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $sk['nama']; ?></td>
                                <input type="hidden" name="id_siswa_<?php echo $no; ?>" value="<?php echo $sk['id_siswa']; ?>">
                                <?php 
                                if (!empty($list_kd)) {
                                    foreach ($list_kd as $k) {
                                        if (in_array($k['id'], $pc_selalu)) {
                                            echo '<td class="text-center"><label class="nso"><input type="checkbox" name="selalu_'.$no.'[]" value="'.$k['id'].'" checked></label></td>';                                            
                                        } else {
                                            echo '<td class="text-center"><label class="nso"><input type="checkbox" name="selalu_'.$no.'[]" value="'.$k['id'].'"></label></td>';
                                        }
                                    }
                                }
                                ?>
                                <td>
                                    <?php echo form_dropdown('meningkat_'.$no,$dropdown_kd,$sk['mulai_meningkat'],'class="form-control" required id="meningkat"'); ?>
                                </td>
                            </tr>
                        <?php 
                                $no++;
                            }
                        } else {
                            echo '<tr><td colspan="5">Belum ada data siswa</td></tr>';
                        }
                        ?>
                        
                        
                    </tbody>
                    
                </table>
                <input type="hidden" name="jumlah" value="<?php echo $no; ?>">
                <button type="submit" class="btn btn-success" id="tbsimpan"><i class="fa fa-check"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on("ready", function() {
        
        $("#<?php echo $nama_form; ?>").on("submit", function() {
                
            var data    = $(this).serialize();
            $.ajax({
                type: "POST",
                data: data,
                url: base_url+"<?php echo $url; ?>/simpan_nilai",
                beforeSend: function(){
                    $("#tbsimpan").attr("disabled", true);
                },
                success: function(r) {
                    $("#tbsimpan").attr("disabled", false);
                    if (r.status == "ok") {
                        noti("success", r.data);
                    } else {
                        noti("danger", r.data);
                    }
                }
            });
            return false;
        });
    });
</script>