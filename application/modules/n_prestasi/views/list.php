<style type="text/css">
    .ctr {text-align: center}
    .nso {}
</style>
<div class="row">
    <!--
    <div class="col-md-12">
        <p>
            <a href="<?php echo base_url().$url; ?>/cetak/<?php echo $id_kelas; ?>" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Cetak</a>
        </p>
    </div>
    -->

    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Daftar Prestasi di Kelas Ini</h4>
            </div>
            <div class="content">  
                <p>
                    <a href="#" id="tbl_input" class="btn btn-info"> Input Prestasi</a>
                </p>
                <form class="form" id="f_prestasi" style="display: none">
                    <div class="form-group">
                        <label>Nama Siswa</label>
                        <select name="id_siswa" class="form-control" required>
                            <option value="">-Pilih siswa-</option>
                            <?php 
                            if (!empty($siswa_kelas)) {
                                foreach ($siswa_kelas as $s) {
                                    echo '<option value="'.$s['id_siswa'].'">'.$s['nama'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <input type="text" name="jenis" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success" id="tbsimpan"> Simpan</button>
                    </div>
                </form>
                
                <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="35%">Nama Siswa</th>
                            <th width="15%">Jenis</th>
                            <th width="30%">Keterangan</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).on("ready", function() {
        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
        $("#f_prestasi").on("submit", function() {
                
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
                        $("#f_prestasi").hide();
                        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
                    } else {
                        noti("danger", r.data);
                    }
                }
            });

            return false;
        });

        $("#tbl_input").click(function(){
            $("#f_prestasi").toggle();
        });
    });


    function hapus(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 

        $.ajax({
            type: "GET",
            url: base_url+"<?php echo $url; ?>/hapus/"+id,
            success: function(data) {
                noti("success", data.data);
                pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
            }
        });
        return false;
    }

</script>