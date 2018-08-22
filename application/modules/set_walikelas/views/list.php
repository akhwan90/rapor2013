<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Set Wali Kelas</h4>

            </div>
            <div class="content">

                <div class="panel">
                    <div class="panel-body">
                    <a href="#" class="btn btn-success pull-left" onclick="return edit(0);">Tambah</a>
                    </div>
                </div>

                <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
                    <thead>
                        <td width="10%">No</td>
                        <td width="20%">Nama Kelas</td>
                        <td width="50%">Nama Guru</td>
                        <td width="20%">Aksi</td>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal_data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Wali Kelas</h4>
            </div>
            <form class="form-horizontal" method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>">
            <input type="hidden" name="_id" id="_id" value="">
            <input type="hidden" name="_mode" id="_mode" value="">

            <div class="modal-body">
                <div class="form-group">
                    <label for="id_kelas" class="col-sm-3 control-label">Kelas</label>
                    <div class="col-sm-9">
                        <?php echo form_dropdown('id_kelas',$p_kelas,'','class="form-control" required id="id_kelas"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_guru" class="col-sm-3 control-label">Kelas</label>
                    <div class="col-sm-9">
                        <?php echo form_dropdown('id_guru',$p_guru,'','class="form-control" required id="id_guru"'); ?>
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
    $(document).on("ready", function() {
        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);

        $("#<?php echo $nama_form; ?>").on("submit", function() {

            var data    = $(this).serialize();
    
            $.ajax({
                type: "POST",
                data: data,
                url: base_url+"<?php echo $url; ?>/simpan",
                success: function(r) {
                    if (r.status == "gagal") {
                        noti("danger", r.data);
                        $("#modal_data").modal('hide');
                    } else {
                        $("#modal_data").modal('hide');
                        noti("success", r.data);
                        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
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
                $("#id_kelas").val(data.data.id_kelas);
                $("#id_guru").val(data.data.id_guru);
            }
        });
        return false;
    }

    function hapus(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } else {
            if (confirm('Anda yakin akan menghapus data ini..?')) {
                $.ajax({
                    type: "GET",
                    url: base_url+"<?php echo $url; ?>/hapus/"+id,
                    success: function(data) {
                        noti("success", "Berhasil dihapus...!");
                        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
                    }
                });
            }
        }
        return false;
    }


</script>