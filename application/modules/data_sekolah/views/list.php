<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Data Sekolah</h4>

            </div>
            <div class="content">
                <form action="<?=site_url('data_sekolah/simpan');?>" id="frm_sekolah" method="post">
                    <div class="form-group">
                        <label for="">Nama Sekolah</label>
                        <?=form_input('nama_sekolah', $sekolah['nama_sekolah'], 'id="nama_sekolah" class="form-control" required');?>
                    </div>
                    <div class="form-group">
                        <label for="">Alamat Sekolah</label>
                        <?=form_input('alamat', $sekolah['alamat'], 'id="alamat" class="form-control" required');?>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).on("ready", function() {
        $("#frm_sekolah").on("submit", function() {
            let data = $(this).serialize();
            let uri = $(this).attr('action');

            $.ajax({
                type: "POST",
                data: data,
                url: uri,
                success: function(r) {
                    if (r.status == "gagal") {
                        noti("danger", r.data);
                    } else {
                        noti("success", r.data);
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