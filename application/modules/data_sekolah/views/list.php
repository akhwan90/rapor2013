<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Data Sekolah</h4>

            </div>
            <div class="content">
                <form action="<?=site_url('data_sekolah/simpan');?>" id="frm_sekolah" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <img src="<?=base_url('upload/logo/'.$sekolah['logo']);?>" style="width: 150px">
                    </div>
                    <div class="form-group">
                        <label for="">Ganti Logo</label>
                        <?=form_upload('logo');?>
                    </div>
                    <div class="form-group">
                        <label for="">Nama Sekolah</label>
                        <?=form_input('nama_sekolah', $sekolah['nama_sekolah'], 'id="nama_sekolah" class="form-control" required');?>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Alamat Sekolah</label>
                                <?=form_input('alamat', $sekolah['alamat'], 'id="alamat" class="form-control" required');?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Desa/Kelurahan</label>
                                <?=form_input('desa', $sekolah['desa'], 'id="desa" class="form-control" required');?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Kecamatan</label>
                                <?=form_input('kec', $sekolah['kec'], 'id="kec" class="form-control" required');?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Kabupaten</label>
                                <?=form_input('kab', $sekolah['kab'], 'id="kab" class="form-control" required');?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Provinsi</label>
                                <?=form_input('prov', $sekolah['prov'], 'id="prov" class="form-control" required');?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Telepon</label>
                                <?=form_input('telp', $sekolah['telp'], 'id="telp" class="form-control" required');?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Email</label>
                                <?=form_input('email', $sekolah['email'], 'id="email" class="form-control" required');?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Website</label>
                                <?=form_input('web', $sekolah['web'], 'id="web" class="form-control" required');?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Kode Pos</label>
                                <?=form_input('kodepos', $sekolah['kodepos'], 'id="kodepos" class="form-control" required');?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Sebutan Kepala Sekolah</label>
                        <?=form_input('sebutan_kepala', $sekolah['sebutan_kepala'], 'id="sebutan_kepala" class="form-control" required');?>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">NSS</label>
                                <?=form_input('nss', $sekolah['nss'], 'id="nss" class="form-control" required');?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">NPSN</label>
                                <?=form_input('npsn', $sekolah['npsn'], 'id="npsn" class="form-control" required');?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Kop Raport 1</label>
                                <?=form_input('kop_1', $sekolah['kop_1'], 'id="kop_1" class="form-control" required');?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Kop Raport 2</label>
                                <?=form_input('kop_2', $sekolah['kop_2'], 'id="kop_2" class="form-control" required');?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success" id="tbSave">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).on("ready", function() {
        $("#frm_sekolah").on("submit", function(e) {
            e.preventDefault();
            let data = new FormData(this);
            let uri = $(this).attr('action');

            $.ajax({
                type: "POST",
                url: uri,
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#tbSave").attr("disabled", true);
                },
                success: function (r){
                    $("#tbSave").attr("disabled", false);
                    if (r.status == "gagal") {
                        noti("danger", r.data);
                    } else {
                        noti("success", r.data);
                        window.open(base_url + 'data_sekolah', '_self');
                    }
                },
                error: function(xhr){
                    $("#tbSave").attr("disabled", false);
                }
            });
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
                $("#nama").val(data.data.nama);
            }
        });
        return false;
    }

    function hapus(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 


        if (confirm('Anda yakin..? ')) {

            $.ajax({
                type: "GET",
                url: base_url+"<?php echo $url; ?>/hapus/"+id,
                success: function(data) {
                    noti("success", "Berhasil dihapus...!");
                    pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
                }
            });
        }
        return false;
    }

</script>