<div class="card">
    <div class="header">
        <h4 class="title">Data Siswa</h4>

    </div>
    <div class="content">

        <div class="panel">
            <div class="panel-body">
                <a href="<?php echo base_url().$url; ?>/edit/0" class="btn btn-success pull-left">Tambah</a> &nbsp;

                <div class="btn-group">
                    <a href="<?php echo base_url('aset/import_siswa.xls'); ?>" class="btn btn-info">Download Format Import</a>
                    <a href="<?php echo base_url('data_siswa/upload'); ?>" class="btn btn-info">Import Data Siswa</a>
                </div>
            </div>
        </div>

        <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
            <thead>
                <td width="10%">No</td>
                <td width="20%">NISN</td>
                <td width="40%">Nama</td>
                <td width="30%">Aksi</td>
            </thead>

        </table>
    </div>
</div>

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
                    } else {
                        noti("success", r.data);
                    }
                }
            });

            return false;
        });
    });


    function hapus(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 



        if (confirm('Anda yakin..? ')) {

            $.ajax({
                type: "GET",
                url: base_url+"data_siswa/hapus/"+id,
                success: function(data) {
                    noti("success", "Berhasil dihapus...!");
                    pagination("datatabel", base_url+"data_siswa/datatable", []);
                }
            });

        }

        return false;
    }

    function aktifkan(id) {

        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 

        if (confirm('Anda yakin..? Username dan password adalah NIS..!')) {

            $.ajax({
                type: "GET",
                url: base_url+"<?php echo $url; ?>/aktifkan/"+id,
                success: function(data) {
                    noti("success", data.data);
                    pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
                }
            });
        }
        return false;
    }
</script>