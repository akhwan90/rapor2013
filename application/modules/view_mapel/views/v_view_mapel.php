<div class="row">
    
    <div class="col-md-12">
        <div class="alert alert-warning" style="color: #000">
            <b>Petunjuk : </b><br>
            Menu ini digunakan untuk menginput nilai pada setiap masing-masing mata pelajaran diampu. Silakan klik menu <b><i>Nilai Pengetahuan</i></b> untuk menginput nilai pengetahuan, dan <b><i>Nilai Keterampilan</i></b> untuk menginput nilai keterampilan.
        </div>
    </div>
    <?php 
    if (!empty($list_mapelkelas)) {
        foreach ($list_mapelkelas as $mk) {
    ?>
    <div class="col-md-4">
        <div class="card">
            <div class="header">
                <h4 class="title"><?php echo $mk['nmmapel']." - ".$mk['nmkelas'].", <div class='pull-right'><a href='#' onclick=\"return detil_kkm(".$mk['id'].", '".$mk['nmmapel']."', '".$mk['nmkelas']."', ".$mk['kkm'].");\">KKM: ".$mk['kkm']."</a></div>"; ?>    
                <!--
                <a href="#" class="btn btn-success btn-xs pull-right"><i class="fa fa-check"></i> </a>
                -->
                </h4>
            </div>
            <div class="content">
                <ul class="list-group">
                    <li class="list-group-item"><a href="<?php echo base_url()."n_pengetahuan/index/".$mk['id']; ?>"><i class="fa fa-chevron-right"></i>  Nilai Pengetahuan</a> <!--<a href="#" class="badge pull-right"><i class="fa fa-print"></i> --></li>
                    <li class="list-group-item"><a href="<?php echo base_url()."n_keterampilan/index/".$mk['id']; ?>"><i class="fa fa-chevron-right"></i>  Nilai Keterampilan</a></li>
                    <li class="list-group-item"><a href="#" onclick="return ubah_kkm(<?=$mk['id'];?>, '<?=$mk['nmmapel'];?>', '<?=$mk['nmkelas'];?>', <?=$mk['kkm'];?>);"><i class="fa fa-chevron-right"></i> Ubah KKM</a></li>
                    
                </ul>
            </div>
        </div>
    </div>
    <?php 
        }
    } else {
        echo '<div class="alert alert-info">Belum ada mapel yang diampu..</div>';
    }
    ?>
</div>

<div class="modal" id="modal_kkm">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ubah KKM : <span id="modal_kkm_nama_mapel"></span> <span id="modal_kkm_nama_kelas"></span></h4>
            </div>
            <form class="form-horizontal" method="post" id="modal_kkm_form" name="modal_kkm_form">
            <input type="hidden" name="_id" id="_id" value="">
            <input type="hidden" name="_mode" id="_mode" value="">

            <div class="modal-body">
                <div class="form-group">
                    <label for="modal_kkm_value" class="col-sm-3 control-label">KKM</label>
                    <div class="col-sm-9">
                        <input type="text" name="nilai_kkm" class="form-control" id="modal_kkm_value" required>
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


<div class="modal" id="modal_kkm_detil_rentang">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Rentang KKM : <span id="modal_kkm_detil_rentang_nama_mapel"></span> <span id="modal_kkm_detil_rentang_nama_kelas"></span></h4>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <tr><td width="50%">KKM</td><td width="50%" id="modal_kkm_detil_rentang_kkm"></td></tr>
                    <tr><td>Predikat D</td><td id="modal_kkm_detil_d"></td></tr>
                    <tr><td>Predikat C</td><td id="modal_kkm_detil_c"></td></tr>
                    <tr><td>Predikat B</td><td id="modal_kkm_detil_b"></td></tr>
                    <tr><td>Predikat A</td><td id="modal_kkm_detil_a"></td></tr>
                </table>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Tutup</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="text/javascript">
    function detil_kkm(id_guru_mapel, nm_mapel, kelas, kkm) {
        $("#modal_kkm_detil_rentang_nama_mapel").html(nm_mapel);
        $("#modal_kkm_detil_rentang_nama_kelas").html(kelas);
        $("#modal_kkm_detil_rentang_kkm").html(kkm);

        $.ajax({
            type: "GET",
            url: base_url + "view_mapel/detil_rentang_kkm/"+kkm,
            success: function(r, textStatus, jqXHR) {    
                $("#modal_kkm_detil_d").html(r.keterangan.d);
                $("#modal_kkm_detil_c").html(r.keterangan.c);
                $("#modal_kkm_detil_b").html(r.keterangan.b);
                $("#modal_kkm_detil_a").html(r.keterangan.a);
            },
            error: function(xhr) {
                console.log(xhr)
            }
        });
        

        $("#modal_kkm_detil_rentang").modal('show');

        return false;
    }

    function ubah_kkm(id_guru_mapel, nm_mapel, kelas, kkm) {
        $("#modal_kkm_nama_mapel").html(nm_mapel);
        $("#modal_kkm_nama_kelas").html(kelas);
        $("#modal_kkm_value").val(kkm);
        $("#_id").val(id_guru_mapel);

        $("#modal_kkm").modal('show');

        return false;
    }

    $("#modal_kkm_form").on('submit', function(e) {
        e.preventDefault();
        let data = $("#modal_kkm_form").serialize();

        $.ajax({
            type: "POST",
            data: data,
            url: base_url + "index.php/view_mapel/simpan_kkm",
            beforeSend: function(){
                $("#modal_kkm_form input, select, button").attr("disabled", true);
            },
            success: function(r, textStatus, jqXHR) {    
                $("#modal_kkm_form input, select, button").attr("disabled", false);
                if (r.success == false) {
                    alert(r.message);
                } else {
                    $("#modal_kkm").modal('hide');
                    alert(r.message);
                    window.open(base_url + 'index.php/view_mapel', '_self');
                }
            },
            error: function(xhr) {
                $("#modal_kkm_form input, select, button").attr("disabled", false);
                console.log(xhr)
            }
        });
        

    });
</script>