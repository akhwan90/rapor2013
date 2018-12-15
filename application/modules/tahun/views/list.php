<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Tahun Aktif</h4>

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
                        <td width="10%">Tahun</td>
                        <td width="20%">Kepala Sekolah/ Madrasah</td>
                        <td width="15%">Tgl Raport</td>
                        <td width="15%">Tgl Raport Tingkat 3</td>
                        <td width="10%">Status</td>
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
                <h4 class="modal-title">Tahun</h4>
            </div>
            <form class="form-horizontal" method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>">
            <input type="hidden" name="_id" id="_id" value="">
            <input type="hidden" name="_mode" id="_mode" value="">

            <div class="modal-body">
                <div class="form-group">
                    <label for="tahun" class="col-sm-3 control-label">Tahun</label>
                    <div class="col-sm-9">
                        <?=form_dropdown('tahun',$p_tahun,'','class="form-control" id="tahun" required'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tahun" class="col-sm-3 control-label">Semester</label>
                    <div class="col-sm-9">
                        <?=form_dropdown('semester',$p_semester,'','class="form-control" id="semester" required'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tahun" class="col-sm-3 control-label">Kepala Sekolah</label>
                    <div class="col-sm-9">
                        <input type="text" name="nama_kepsek"  class="form-control" id="nama_kepsek" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tahun" class="col-sm-3 control-label">NIP Kepsek</label>
                    <div class="col-sm-9">
                        <input type="text" name="nip_kepsek"  class="form-control" id="nip_kepsek" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tahun" class="col-sm-3 control-label">Tgl TTD Raport</label>
                    <div class="col-sm-9">
                        <input type="date" name="tgl_raport"  class="form-control" id="tgl_raport" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tahun" class="col-sm-3 control-label">Tgl TTD Raport Kelas 3</label>
                    <div class="col-sm-9">
                        <input type="date" name="tgl_raport_kelas3"  class="form-control" id="tgl_raport_kelas3" required>
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
        pagination("datatabel", base_url+"tahun/datatable", []);

        $("#<?php echo $nama_form; ?>").on("submit", function() {

            var data    = $(this).serialize();
    
            $.ajax({
                type: "POST",
                data: data,
                url: base_url+"<?php echo $url; ?>/simpan",
                beforeSend: function() {
                    $('input, button').attr('disabled', true);
                },
                success: function(r) {
                    $('input, button').attr('disabled', false);
                    if (r.status == "gagal") {
                        noti("danger", r.data);
                    } else {
                        $("#modal_data").modal('hide');
                        noti("success", r.data);
                        pagination("datatabel", base_url+"tahun/datatable", []);
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
            url: base_url+"tahun/edit/"+id,
            beforeSend: function() {
                $('input, button').attr('disabled', true);
            },
            success: function(data) {
                $("#_id").val(data.data.id);
                $("#tahun").val(data.data.tahun);
                $("#semester").val(data.data.semester);
                $("#nama_kepsek").val(data.data.nama_kepsek);
                $("#nip_kepsek").val(data.data.nip_kepsek);
                $("#tgl_raport").val(data.data.tgl_raport);
                $("#tgl_raport_kelas3").val(data.data.tgl_raport_kelas3);
                $('input, button').attr('disabled', false);
            }
        });
        return false;
    }

    function hapus(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 

        $.ajax({
            type: "GET",
            url: base_url+"tahun/hapus/"+id,
            success: function(data) {
                noti("success", "Berhasil dihapus...!");
                pagination("datatabel", base_url+"tahun/datatable", []);
            }
        });
        return false;
    }

    function aktifkan(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 

        $.ajax({
            type: "GET",
            url: base_url+"tahun/aktifkan/"+id,
            success: function(data) {
                noti("success", data.data);
                pagination("datatabel", base_url+"tahun/datatable", []);
            }
        });
        return false;
    }

</script>
