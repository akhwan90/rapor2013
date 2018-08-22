<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Data Kelas</h4>
            </div>
            <div class="content">
                
                <div class="panel">
                    <div class="panel-body">
                        <div class="pull-left">
                            <a href="<?php echo base_url().$url; ?>/edit/0" class="btn btn-success">Tambah</a> &nbsp;
                        </div>
                    </div>
                </div>
                
                <?php echo $tampil; ?>
                
                <!--
                <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
                    <thead>
                        <td width="10%">No</td>
                        <td width="20%">Kelas</td>
                        <td width="50%">Nama Siswa</td>
                        <td width="20%">Aksi</td>
                    </thead>

                </table>
                -->
            </div>
        </div>
    </div>
</div>



<div class="modal" id="modal_data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Setting Header</h4>
            </div>
            <form class="form-horizontal" method="get" id="f_header" name="f_header">

                <div class="modal-body">

                    <div class="form-group">
                        <label for="kode" class="col-sm-3 control-label">Header 1</label>
                        <div class="col-sm-9">
                            <input type="text" name="header1"  class="form-control" id="header1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nama" class="col-sm-3 control-label">Header 2</label>
                        <div class="col-sm-9">
                            <input type="text" name="header2" class="form-control" id="header2" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="isbk" class="col-sm-3 control-label">Header 3</label>
                        <div class="col-sm-9">
                            <input type="text" name="header3" class="form-control" id="header3" required>
                        </div>
                    </div>
                </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Cetak</button>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    $(document).on("ready", function() {
        $("#f_header").on("submit", function() {

            var h1 = $("#header1").val();
            var h2 = $("#header2").val();
            var h3 = $("#header3").val();

            window.open("http://nilai.mtsn-sidoharjo.sch.id/cetak_kartu?h1="+h1+"&h2="+h2+"&h3="+h3, '_blank');

            return false;
        });
    });

    function hapus(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } else {
            if (confirm('Data siswa ini mungkin sudah mempunyai nilai yang dicatatkan. Data tersebut akan terhapus. Lanjutkan..?')) {
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

    function buat_header() {
        $("#modal_data").modal('show');

        /*

        var h1 = promt("Masukkan header 1..!");
        var h2 = promt("Masukkan header 2..!");
        var h3 = promt("Masukkan header 3..!");

        window.open("http://nilai.mtsn-sidoharjo.sch.id/cetak_kartu?h1="+h1+"&h2="+h2+"&h3="+h3, '_blank');

        return false;
        */
    }

</script>