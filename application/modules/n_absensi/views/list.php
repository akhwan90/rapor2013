<style type="text/css">
    .ctr {text-align: center}
    .nso {}
</style>
<div class="row">
    <div class="col-md-12">
        <p>
            <a href="<?php echo base_url().$url; ?>/cetak/<?php echo $id_kelas; ?>" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Cetak</a>
        </p>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Nilai Absensi </h4>
            </div>
            <div class="content">  

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="35%">Nama</th>
                            <th width="20%">Sakit</th>
                            <th width="20%">Izin</th>
                            <th width="20%">Tanpa Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                        <form method="post" id="<?php echo $url; ?>">
                        <input type="hidden" name="mode_form" value="<?php echo $mode_form; ?>">

                        <?php 

                        $no = 1;
                        if (!empty($siswa_kelas)) {
                            foreach ($siswa_kelas as $sk) {
                                echo '<input type="hidden" name="id_siswa_'.$no.'" value="'.$sk['id_siswa'].'">';
                        ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $sk['nama']; ?></td>
                                <td>
                                    <input type="number" min="0" max="100" name="s_<?php echo $no; ?>" value="<?php echo $sk['s']; ?>" class="form-control input-sm" required id="s_<?php echo $no; ?>">
                                </td>
                                <td>
                                    <input type="number" min="0" max="100" name="i_<?php echo $no; ?>" value="<?php echo $sk['i']; ?>" class="form-control input-sm" required id="i_<?php echo $no; ?>">
                                </td>
                                <td>
                                    <input type="number" min="0" max="100" name="a_<?php echo $no; ?>" value="<?php echo $sk['a']; ?>" class="form-control input-sm" required id="a_<?php echo $no; ?>">
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
        
        $("#<?php echo $url; ?>").on("submit", function() {
                
            var data    = $(this).serialize();


            $.ajax({
                type: "POST",
                data: data,
                url: base_url+"<?php echo $url; ?>/simpan",
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