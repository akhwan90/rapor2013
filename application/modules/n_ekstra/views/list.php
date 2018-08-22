<div class="row">
    <!--
    <div class="col-md-12">
        <p>
            <a href="<?php echo base_url().$url; ?>/cetak" class="btn btn-warning" target="_blank">Cetak</a>
        </p>
    </div>
    -->
    <div class="col-md-5">
        <div class="card">
            <div class="header">
                <h4 class="title">Nilai Ekstrakurikuler</h4>
            </div>
            <div class="content">
                <ul class="list-group" id="list_kd">
                    <?php 
                    if (!empty($list_kd)) {
                        foreach ($list_kd as $lk) {
                    ?>
                    <li class="list-group-item" onclick="return view_kd(<?php echo $lk['id']; ?>, '<?php echo $lk['nama']; ?>');"><a href="#"><i class="fa fa-chevron-right"></i> <?php echo $lk['nama']; ?></a></li>
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
                <h4 class="title">Input Nilai Ekstrakurikuler</h4>
            </div>
            <div class="content">
                <form name="<?php echo $url; ?>" method="post" action="#" id="<?php echo $url; ?>">
                    <input type="hidden" name="id_ekstra" id="id_ekstra" value="">
                    <div id="load_nilai">
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    nama_ekstra = "";
    $(document).on("ready", function() {
        view_kd(0, "");
        $('#list_kd li').on('click', function(){
            $('li.active').removeClass('active');
            $(this).addClass('active');
        });
        $("#<?php echo $url; ?>").on("submit", function() {
            var data    = $(this).serialize();
    
            $.ajax({
                type: "POST",
                data: data,
                url: base_url+"<?php echo $url; ?>/simpan_nilai",
                beforeSend: function(){
                    $("#tbsimpan").attr("disabled", true);
                },
                success: function(r) {
                    $("#tbsimpan").attr("disabled", false);
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
    function view_kd(id, nama) {
        nama_ekstra = nama;
        
        if (id == 0) {
            $("#load_nilai").html('<div class="alert alert-warning">Silakan pilih KD di samping</div>');
        } else {
            $("#id_ekstra").val(id);
            
            $("#load_nilai").html("Mohon menunggu, sedang mengambil data dari server di Amerika sana, sehingga agak lama... :D");
            $.getJSON(base_url+"<?php echo $url; ?>/ambil_siswa/"+id, function(data) {
                $("#load_nilai").show('slow');
                html = '<table class="table table-bordered"><thead><tr><th width="10%" rowspan="2">No</th><th width="40%" rowspan="2">Nama</th><th width="50%" colspan="2">Nilai</th></tr><tr><th width="10%">Nilai</th><th width="40%">Deskripsi</th></thead><tbody>';
                var i = 1;
                $.each(data.data, function(k, v) {
                    var pnilai = ["-","A","B","C"];
                    html += '<tr><td>'+i+'</td><td>'+v.nmsiswa+'</td><td><input name="id_siswa[]" type="hidden" value="'+v.ids+'"><select name="nilai[]" id="nilai_'+i+'" onchange="return ganti_deskripsi('+i+');" style="padding: 5px" class="form-control input-sm" required>';
                        for (var x = 0; x < pnilai.length; x++) {
                            html += v.nilai == pnilai[x] ? '<option value="'+pnilai[x]+'" selected>'+pnilai[x]+'</option>' : '<option value="'+pnilai[x]+'">'+pnilai[x]+'</option>';
                        }
                        var ides = v.desk == "" ? "-" : v.desk;
                    html += '</select></td><td><input name="nilai_d[]" type="text" class="form-control input-sm desk" id="desk_'+i+'" value="'+ides+'"></td></tr>';
                    i++;
                }); 
                html += '</tbody></table><p><button type="submit" class="btn btn-success" id="tbsimpan"><i class="fa fa-check"></i> Simpan</button></p>';
                $("#load_nilai").html(html);
            });
            
        }
        return false;
    }
    function ganti_deskripsi(id) {
        var nilai = $("#nilai_"+id).val();
        var desk = "";
        if (nilai === "A") {
            desk = "Memuaskan, aktif megikuti kegiatan "+nama_ekstra+" mingguan";
        } else if (nilai === "B") {
            desk = "Cukup memuaskan, aktif mengikuti kegiatan "+nama_ekstra+" mingguan";            
        } else if (nilai === "C") {
            desk = "Kurang memuaskan, pasif mengikuti kegiatan "+nama_ekstra+" mingguan";            
        } else {
            desk = "-";
        }
        $("#desk_"+id).val(desk);
    }
</script>