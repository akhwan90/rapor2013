<div class="card">
    <div class="header">
        <h4 class="title">Ubah Password</h4>
    </div>
    <div class="content">
        <form action="#" method="post" class="form" id="f_ubah_password">
            <div class="form-group">
                <label>Username</label>
                <?php echo form_input('username','','class="form-control col-md-4" required'); ?>
            </div>
            <div class="form-group">
                <label>Password Lama</label>
                <?php echo form_input('p1','','class="form-control col-md-4" required'); ?>
            </div>
            <div class="form-group">
                <label>Password Baru</label>
                <?php echo form_input('p2','','class="form-control col-md-4" required'); ?>
            </div>
            <div class="form-group">
                <label>Ulangi Password Baru</label>
                <?php echo form_input('p3','','class="form-control col-md-4" required'); ?>
            </div>
            <div class="form-group">
                <br><br>
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $("#f_ubah_password").on("submit", function() {

        var data    = $(this).serialize();

        $.ajax({
            type: "POST",
            data: data,
            url: base_url+"<?php echo $url; ?>/simpan_ubah_password",
            success: function(r) {
                if (r.status == "gagal") {
                    noti("danger", r.data);
                    //$("input").val('');
                } else {
                    $("#modal_data").modal('hide');
                    noti("success", r.data);
                    $("input").val('');
                }
            }
        });

        return false;
    });
</script>
