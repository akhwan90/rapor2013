<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning" style="color: #000">
            <b>Petunjuk : </b><br>
            <ul>
                <li>Menu ini digunakan untuk menginput nilai keterampilan pada mata pelajaran <b><i><?php echo $detil_mp['nmmapel'].", kelas ".$detil_mp['nmkelas']; ?>.</i></b> </li>
                <li>Jika kompetensi dasar belum ada, silakan klik tombol <b><i>Tambah KD</i></b>. Untuk mengubah atau menghapus nama KD, silakan klik tombol "<i class="fa fa-pencil"></i>" atau "<i class="fa fa-remove"></i>". </li>
                <li>Untuk mengisikan nilai keterampilan pada masing-masing KD, silakan klik nama KD, dan akan muncul daftar siswa serta isian nilai. Nilai dalam <b><i>skala 1-100</i></b>. Jangan lupa klik tombol <b><i>Simpan</i></b> di sebelah bawah.</li>
            </ul>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <a href="<?php echo base_url(); ?>view_mapel" class="btn btn-info"><i class="fa fa-arrow-left"></i> Kembali</a>
                <a href="<?php echo base_url(); ?>n_keterampilan/cetak/<?php echo $detil_mp['id_mapel']."-".$detil_mp['id_kelas']; ?>" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Cetak</a>
                <a href="<?php echo base_url(); ?>n_keterampilan/import/<?php echo $detil_mp['id_mapel']."-".$detil_mp['id_kelas']; ?>" class="btn btn-danger"><i class="fa fa-download"></i> Download File Excel</a>
                <a href="<?php echo base_url(); ?>n_keterampilan/upload/<?php echo $detil_mp['id_mapel']."-".$detil_mp['id_kelas']; ?>" class="btn btn-success"><i class="fa fa-upload"></i> Upload File Excel</a>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="header">
                <h5 class="title">Nilai Keterampilan &raquo; <?php echo $detil_mp['nmmapel']." - ".$detil_mp['nmkelas']; ?></h5>
            </div>
            <div class="content">
                <p>
                    <a href="#" onclick="return edit(0);" class="btn btn-info"><i class="fa fa-plus-circle"></i> Tambah KD</a>
                </p>
                <ul class="list-group" id="list_kd">
                    <?php 
                    if (!empty($list_kd)) {
                        foreach ($list_kd as $lk) {
                    ?>
                    <li class="list-group-item"><a href="#" onclick="return view_kd(<?php echo $lk['id'].", ".$detil_mp['id_kelas']; ?>);"><?php echo "(".$lk['no_kd'].") ".$lk['nama_kd'].""; ?></a>
                        <div class="pull-right">
                            <a href="#" onclick="return edit(<?php echo $lk['id']; ?>);" class="btn btn-xs btn-success"><i class="fa fa-pencil"></i></a>
                            <a href="#" onclick="return hapus(<?php echo $lk['id']; ?>);" class="btn btn-xs btn-danger"><i class="fa fa-remove"></i></a>
                        </div>
                    </li>
                    <?php 
                        }
                    } else {
                        echo '<div class="alert alert-info">Belum ada KD diinputkan</div>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="header">
                <h4 class="title">Input Nilai </h4>
            </div>
            <div class="content">
                <form name="f_input_nilai" method="post" action="#" id="f_input_nilai">
                    <input type="hidden" name="id_guru_mapel" id="id_guru_mapel" value="<?php echo $detil_mp['id']; ?>">
                    <input type="hidden" name="id_mapel_kd" id="id_mapel_kd" value="">
                    <input type="hidden" name="jenis" id="jenis" value="">
                    <div id="load_nilai">
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal_data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Set KD</h4>
            </div>
            <form class="form-horizontal" method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>" onsubmit="return simpan_kd();">
            <input type="hidden" name="_id" id="_id" value="">
            <input type="hidden" name="_mode" id="_mode" value="">
            <input type="hidden" name="id_guru" id="id_guru" value="<?php echo $detil_mp['id_guru']; ?>">
            <input type="hidden" name="id_mapel" id="id_mapel" value="<?php echo $detil_mp['id_mapel']; ?>">
            <input type="hidden" name="tingkat" id="tingkat" value="<?php echo $detil_mp['tingkat']; ?>">
            <input type="hidden" name="jenis" id="jenis" value="K">
            <div class="modal-body">
                <div class="form-group">
                    <label for="nama" class="col-sm-3 control-label">Kode</label>
                    <div class="col-sm-9">
                        <input type="text" name="kode" class="form-control" autofocus="true" id="kode" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama" class="col-sm-3 control-label">Nama</label>
                    <div class="col-sm-9">
                        <input type="text" name="nama" class="form-control" id="nama" required>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="tbSimpanKd">Simpan</button>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
    id_guru_mapel = <?php echo $this->uri->segment(3); ?>;
    $(document).on("ready", function() {
        view_kd(0,0);
        $('#list_kd li').on('click', function(){
            $('li.active').removeClass('active');
            $(this).addClass('active');
        });
        $("#f_input_nilai").on("submit", function() {
            var data    = $(this).serialize();
    
            $.ajax({
                type: "POST",
                data: data,
                url: base_url+"<?php echo $url; ?>/simpan_nilai",
                beforeSend: function(){
                    $("#tbSimpan").attr("disabled", true);
                },
                success: function(r) {
                    $("#tbSimpan").attr("disabled", false);

                    if (r.status == "gagal") {
                        noti("danger", r.data);
                    } else {
                        $("#modal_data").modal('hide');
                        noti("success", r.data);
                        pagination("datatabel", base_url+"data_guru/datatable", []);
                    }
                }
            });
            return false;
        });
    });
    function view_kd(id, kelas) {
        
        if (id == 0 && kelas == 0) {
            $("#load_nilai").html('<div class="alert alert-warning">Silakan pilih KD di samping</div>');
        } else {
            $("#id_mapel_kd").val(id);
            
            $("#load_nilai").html("Loading...");
            $.getJSON(base_url+"<?php echo $url; ?>/ambil_siswa/"+kelas+"/"+id, function(data) {
                $("#load_nilai").show('slow');
                html = '<table class="table table-condensed table-bordered"><thead><tr><th width="10%">No</th><th width="60%">Nama</th><th width="30%">Nilai</th></tr></thead><tbody>';
                var i = 1;
                $.each(data.data, function(k, v) {
                    html += '<tr><td>'+i+'</td><td>'+v.nama+'</td><td><input name="id_siswa[]" type="hidden" value="'+v.ids+'"><input name="nilai[]" type="number" min="0" max="100" class="form-control input-sm" value="'+v.nilai+'" required></td></tr>';
                    i++;
                }); 
                html += '</tbody></table><p><button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Simpan</button> &nbsp; <a href="#" onclick="return view_kd(0, 0);" class="btn btn-warning"><i class="fa fa-minus-circle"></i> Batal</a></p>';
                $("#load_nilai").html(html);
            });
            
        }
        return false;
    }
    function edit(id) {
        if (id == 0) {
            $("#_mode").val('add');
        } else {
            $("#_mode").val('edit');
        }
        $("#kode").prop("readonly",true);
        $("#nama").prop("readonly",true);
        $("#tbSimpan").prop("disabled",true);
        $("#kode").val('');
        $("#nama").val('');
        $("#modal_data").modal('show');
        $.ajax({
            type: "GET",
            url: base_url+"set_kd/edit/"+id,
            success: function(data) {
                $("#_id").val(data.data.id);
                $("#mapel").val(data.data.id_mapel+"-"+data.data.tingkat);
                $("#jenis").val(data.data.jenis);
                $("#kode").val(data.data.no_kd);
                $("#nama").val(data.data.nama_kd);
                $("#kode").prop("readonly",false);
                $("#nama").prop("readonly",false);
                $("#tbSimpan").prop("disabled",false);
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
                    url: base_url+"set_kd/hapus/"+id,
                    success: function(data) {
                        noti("success", "Berhasil dihapus...!");
                        window.location.assign(base_url+"n_keterampilan/index/"+id_guru_mapel);
                    }
                });                
            }
        }
        return false;
    }
    function simpan_kd() {
        var data    = $("#f_setmapel").serialize();
    
        $.ajax({
            type: "POST",
            data: data,
            url: base_url+"set_kd/simpan",
            beforeSend: function(){
                $("#tbSimpanKd").attr("disabled", true);
            },
            success: function(r) {
                if (r.status == "gagal") {
                    noti("danger", r.data);
                } else {
                    $("#modal_data").modal('hide');
                    noti("success", r.data);
                    window.location.assign(base_url+"n_keterampilan/index/"+id_guru_mapel);
                }
            }
        });
        return false;
    }
</script>