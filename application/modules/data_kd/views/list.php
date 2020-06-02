<div class="card">
    <div class="header">
        <h4 class="title"><?=$title;?></h4>

    </div>
    <div class="content">

        <div class="panel">
            <div class="panel-body">
            <a href="#" class="btn btn-success pull-left" onclick="return edit(0);">Tambah</a>
            </div>
        </div>
        
        <div class="panel">
            <div class="panel-body">     
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Jenis</label>
                            <?=form_dropdown('jenis', $p_jenis, '', 'class="form-control" id="jenis"');?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Mapel</label>
                            <?=form_dropdown('mapel', $p_mapel, '', 'class="form-control" id="mapel"');?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Kelas</label>
                            <?=form_dropdown('tingkat', $this->config->item('p_tingkat'), '', 'class="form-control" id="tingkat"');?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Semester</label>
                            <?=form_dropdown('semester', $p_semester, '', 'class="form-control" id="semester"');?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mt-1">
                            <button class="btn btn-success" onclick="return dt();">Lihat</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
            <thead>
                <td width="10%">No</td>
                <td width="70%">Nama KD</td>
                <td width="20%">Aksi</td>
            </thead>

        </table>
    </div>
</div>

<div class="modal" id="modal_data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=$title;?></h4>
            </div>
            <form class="form-horizontal" method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>">
            <input type="hidden" name="_id" id="_id" value="">
            <input type="hidden" name="_mode" id="_mode" value="">

            <div class="modal-body">
                <div class="form-group">
                    <label for="kode" class="col-sm-3 control-label">Jenis KD</label>
                    <div class="col-sm-9">
                        <?=form_dropdown('f_jenis', $p_jenis, '', 'class="form-control" id="f_jenis"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama" class="col-sm-3 control-label">Mata Pelajaran</label>
                    <div class="col-sm-9">
                        <?=form_dropdown('f_mapel', $p_mapel, '', 'class="form-control" id="f_mapel"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama" class="col-sm-3 control-label">Tingkat</label>
                    <div class="col-sm-9">
                        <?=form_dropdown('f_kelas', $this->config->item('p_tingkat'), '', 'class="form-control" id="f_kelas"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama" class="col-sm-3 control-label">Semester</label>
                    <div class="col-sm-9">
                        <?=form_dropdown('f_semester', $p_semester, '', 'class="form-control" id="f_semester"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama" class="col-sm-3 control-label">Nomor KD</label>
                    <div class="col-sm-9">
                        <?=form_input('f_nomor_kd', '', 'class="form-control" id="f_nomor_kd"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama" class="col-sm-3 control-label">Nama KD</label>
                    <div class="col-sm-9">
                        <?=form_textarea('f_nama_kd', '', 'class="form-control" id="f_nama_kd"');?>
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
    function dt() {
        let add_data = {
            jenis: $("#jenis").val(),
            mapel: $("#mapel").val(),
            tingkat: $("#tingkat").val(),
            semester: $("#semester").val(),
        };

        pagination("datatabel", base_url+"data_kd/datatable", [], add_data);
    }

    $(document).on("ready", function() {
        dt();
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
                        dt();
                    }
                }
            });

            return false;
        });
    });

    function edit(id) {
        if (id == 0) {
            $("#_mode").val('add');
            $("#_id").val(0);
            $("#f_mapel").val(($("#mapel").val()));
            $("#f_kelas").val(($("#tingkat").val()));
            $("#f_semester").val(($("#semester").val()));
            $("#f_nomor_kd").val('');
            $("#f_jenis").val(($("#jenis").val()));
            $("#f_nama_kd").val('');
        } else {
            $("#_mode").val('edit');
            $.ajax({
                type: "GET",
                url: base_url+"data_kd/edit/"+id,
                success: function(data) {
                    $("#_id").val(data.data.id);
                    $("#f_mapel").val(data.data.id_mapel);
                    $("#f_kelas").val(data.data.tingkat);
                    $("#f_semester").val(data.data.semester);
                    $("#f_nomor_kd").val(data.data.no_kd);
                    $("#f_jenis").val(data.data.jenis);
                    $("#f_nama_kd").val(data.data.nama_kd);
                }
            });
        }

        $("#modal_data").modal('show');

        
        return false;
    }

    function hapus(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 


        if (confirm('Anda yakin..? ')) {

            $.ajax({
                type: "GET",
                url: base_url+"data_guru/hapus/"+id,
                success: function(data) {
                    noti("success", "Berhasil dihapus...!");
                    pagination("datatabel", base_url+"data_guru/datatable", []);
                }
            });
        }
        
        return false;
    }

    function aktifkan(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 

        $.ajax({
            type: "GET",
            url: base_url+"data_guru/aktifkan/"+id,
            success: function(data) {
                noti("success", data.data);
                pagination("datatabel", base_url+"data_guru/datatable", []);
            }
        });
        return false;
    }

    function nonaktifkan(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 

        $.ajax({
            type: "GET",
            url: base_url+"data_guru/nonaktifkan/"+id,
            success: function(data) {
                noti("success", data.data);
                pagination("datatabel", base_url+"data_guru/datatable", []);
            }
        });
        return false;
    }

</script>