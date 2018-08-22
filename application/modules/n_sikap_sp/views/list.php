<div class="row">
    <div class="col-md-12">
        <p>
            <a href="<?php echo base_url(); ?>view_mapel" class="btn btn-info"><i class="fa fa-arrow-left"></i> Kembali</a>
            <a href="<?php echo base_url(); ?>n_sikap_sp/cetak/<?php echo $this->uri->segment(3); ?>" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Cetak</a>
        </p>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Nilai Sikap Spiritual </h4>
            </div>
            <div class="content">  
                <table class="table table-condensed table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama</th>
                            <th width="50%" colspan="2">Selalu Dilakukan</th>
                            <th width="20%">Mulai Meningkat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <form method="post" id="<?php echo $nama_form; ?>">
                        <input type="hidden" name="id_guru_mapel" value="<?php echo $this->uri->segment(3); ?>">
                        <input type="hidden" name="mode_form" value="<?php echo $mode_form; ?>">
                        <?php 
                        $no = 1;
                        if (!empty($siswa_kelas)) {
                            $opsyen = array();
                            foreach ($list_kd as $l) {
                                $idx = $l['id'];
                                $val = $l['nama_kd'];
                                $opsyen[$idx] = $val;
                            }
                            foreach ($siswa_kelas as $sk) {
                                $pc_selalu = explode("-", $sk['selalu']);
                                $mm = $sk['mulai_meningkat'];
                        ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $sk['nama']; ?></td>
                                <td>
                                    <input type="hidden" name="id_siswa_<?php echo $no; ?>" value="<?php echo $sk['id_siswa']; ?>">
                                    <?php echo form_dropdown('ssp1_'.$no,$opsyen,$pc_selalu[0],'class="form-control" required id="ssp1_'.$no.'"'); ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('ssp2_'.$no,$opsyen,$pc_selalu[1],'class="form-control" required id="ssp2_'.$no.'"'); ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('ssp3_'.$no,$opsyen,$mm,'class="form-control" required id="ssp3_'.$no.'"'); ?>
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
            var jml_data = <?php echo $no; ?>;
            var teks_error = "";
            
            for (var i = 1; i < jml_data; i++) {
                var ssp1 = $("#ssp1_"+i).val();
                var ssp2 = $("#ssp2_"+i).val();
                var ssp3 = $("#ssp3_"+i).val();
                if ((ssp1 == ssp2) || (ssp1 == ssp3) || (ssp2 == ssp3)) {
                    teks_error += 'Baris '+i+' ada isian sama<br>';
                }
            }
            if (teks_error != "") {
                noti("danger", teks_error);
            } else {
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
                            noti("danger", "Data gagal disimpan...");
                        }
                    }
                });
            }
            return false;
        });
    });
</script>