<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Set Mata Pelajaran Guru</h4>

            </div>
            <div class="content">

                <div class="panel">
                    <div class="panel-body">
                    <a href="<?php echo base_url().$url; ?>/edit/0" class="btn btn-success">Tambah</a> &nbsp;
                    </div>
                </div>

                <?php echo $this->session->flashdata('k'); ?>
                
                <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
                    <thead>
                        <td width="10%">No</td>
                        <td width="20%">Nama Guru</td>
                        <td width="50%">Mata Pelajaran - Kelas</td>
                        <td width="20%">Aksi</td>
                    </thead>

                </table>
            </div>
        </div>
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
                        $("#modal_data").modal('hide');
                        noti("success", r.data);
                        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
                    }
                }
            });

            return false;
        });
    });

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
                        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
                    }
                });                
            }
        }

        return false;
    }

</script>