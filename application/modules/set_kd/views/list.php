<div class="row">
<?php 
if (!empty($mapel_diampu)) {
?>
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Set Kompetensi Dasar Mapel</h4>

            </div>
            <div class="content">

                <div class="panel">
                    <div class="panel-body">
                    <a href="#" onclick="return edit(0);" class="btn btn-success pull-left">Tambah</a>
                    </div>
                </div>

                <div class="form-group">
                    <label>Pilih Kelas</label>
                    <select class="form-control" id="ubah_kelas">
                        <option value="0-0">-Pilih Mapel-</option>
                        <?php 
                        if (!empty($mapel_diampu)) {
                            foreach ($mapel_diampu as $md) {
                                echo '<option value="'.$md['id'].'-'.$md['tingkat'].'">'.$md['nama'].' Kelas '.$md['tingkat'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
                    <thead>
                        <td width="10%">No</td>
                        <td width="10%">Jenis</td>
                        <td width="20%">Kode</td>
                        <td width="40%">Kompetensi Dasar</td>
                        <td width="20%">Aksi</td>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    
    <?php 
    } else {
        echo '<div class="alert alert-info">Belum ada mapel yang diampu..</div>';
    }
    ?>
</div>



<div class="modal" id="modal_data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Set Mapel</h4>
            </div>
            <form class="form-horizontal" method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>">
            <input type="hidden" name="_id" id="_id" value="">
            <input type="hidden" name="_mode" id="_mode" value="">

            <div class="modal-body">
                <div class="form-group">
                    <label for="kode" class="col-sm-3 control-label">Mapel</label>
                    <div class="col-sm-9">
                        <?php echo form_dropdown("mapel", $p_mapel_diampu, '', 'class="form-control" id="mapel"'); 
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="kode" class="col-sm-3 control-label">Jenis KD</label>
                    <div class="col-sm-9">
                        <?php echo form_dropdown("jenis", $p_jenis, '', 'class="form-control" id="jenis"'); 
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama" class="col-sm-3 control-label">Kode</label>
                    <div class="col-sm-9">
                        <input type="text" name="kode" class="form-control" id="kode" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama" class="col-sm-3 control-label">Nama</label>
                    <div class="col-sm-9">
                        <textarea name="nama" class="form-control" id="nama" required></textarea>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    _kode = '';

    $(document).on("ready", function() {

        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable/0-0", []);

        $("#ubah_kelas").on("change", function() {
            var kode = $(this).val();
            
            _kode = $(this).val();
            
            $("#mapel").val(kode);
            pagination("datatabel", base_url+"<?php echo $url; ?>/datatable/"+kode, []);
               
        });

        
        $("#<?php echo $nama_form; ?>").on("submit", function() {

            var data    = $(this).serialize();
    
            $.ajax({
                type: "POST",
                data: data,
                url: base_url+"<?php echo $url; ?>/simpan",
                success: function(r) {
                    if (r.status == "gagal") {
                        noti("danger", r.data);
                    } else {
                        $("#modal_data").modal('hide');
                        noti("success", r.data);
                        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable/"+_kode, []);
                    }
                }
            });

            return false;
        });
    });

    function edit(id) {
        if (id == 0) {
            $("#_mode").val('add');
        } else {
            $("#_mode").val('edit');
        }

        $("#modal_data").modal('show');

        $.ajax({
            type: "GET",
            url: base_url+"<?php echo $url; ?>/edit/"+id,
            success: function(data) {
                $("#_id").val(data.data.id);
                $("#mapel").val(data.data.id_mapel+"-"+data.data.tingkat);
                $("#jenis").val(data.data.jenis);
                $("#kode").val(data.data.no_kd);
                $("#nama").val(data.data.nama_kd);
            }
        });
        return false;
    }

    function hapus(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } else {
            if (confirm('Anda yakin...?')) {
                $.ajax({
                    type: "GET",
                    url: base_url+"<?php echo $url; ?>/hapus/"+id,
                    success: function(data) {
                        noti("success", "Berhasil dihapus...!");
                        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable/"+_kode, []);
                    }
                });                
            }
        }

        return false;
    }

</script>
