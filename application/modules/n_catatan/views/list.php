<style type="text/css">
    .ctr {text-align: center}
    .nso {}
</style>
<div class="card">
    <div class="header">
        <h4 class="title">Status Naik Kelas dan Catatan Wali</h4>
    </div>
    <div class="content">  

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Nama</th>
                    <th width="10%">Naik Kelas</th>
                    <th width="50%">Catatan</th>
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
                            <?php 
                            echo form_dropdown("naik_".$no,$p_naik,$sk['naik'],'class="form-control input-sm" required id="naik_'.$no.'"');
                            ?>
                        </td>
                        <td>
                            <input type="text" name="catatan_<?php echo $no; ?>" value="<?php echo $sk['catatan_wali']; ?>" class="form-control input-sm" id="catatan_<?php echo $no; ?>">
                        </td>
                    </tr>
                <?php 
                        $no++;
                    }
                } else {
                    echo '<tr><td colspan="4">Belum ada data siswa</td></tr>';
                }
                ?>

                
                
            </tbody>
            
        </table>

        <input type="hidden" name="jumlah" value="<?php echo $no; ?>">
        <button type="submit" class="btn btn-success" id="tbsimpan"><i class="fa fa-check"></i> Simpan</button>
        </form>
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