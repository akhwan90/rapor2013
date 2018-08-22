<div class="card">
    <div class="header">
        <h4 class="title">Data Siswa</h4>

    </div>
    <div class="content">
        <?php echo form_open_multipart(base_url($url.'/simpan')); ?>
        <input type="hidden" name="_id" id="_id" value="<?php echo $data['id']; ?>">
        <input type="hidden" name="_mode" id="_mode" value="<?php echo $data['mode']; ?>">

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="kode" class="control-label">NIS</label>
                    <input type="text" name="nis" value="<?php echo $data['nis']; ?>" class="form-control" id="nis" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">NISN</label>
                    <input type="text" name="nisn" value="<?php echo $data['nisn']; ?>" class="form-control" id="nisn" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="kode" class="control-label">Nama</label>
                    <input type="text" name="nama" value="<?php echo $data['nama']; ?>" class="form-control" id="nama" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="kode" class="control-label">Jns Kel</label>
                    <?php echo form_dropdown('jk', $p_jk, $data['jk'], 'class="form-control" id="jk" required'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="kode" class="control-label">Tempat Lahir</label>
                    <input type="text" name="tmp_lahir" value="<?php echo $data['tmp_lahir']; ?>" class="form-control" id="tmp_lahir" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Tgl Lahir</label>
                    <input type="date" name="tgl_lahir" value="<?php echo $data['tgl_lahir']; ?>" class="form-control" id="tgl_lahir" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Agama</label>
                    <?php echo form_dropdown('agama', $p_agama, $data['agama'], 'class="form-control" id="agama" required'); ?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Status Anak</label>
                    <?php echo form_dropdown('status', $p_status_anak, $data['status'], 'class="form-control" id="status" required'); ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="kode" class="control-label">Anak Ke</label>
                    <input type="number" name="anakke" value="<?php echo $data['anakke']; ?>" class="form-control" id="anakke">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <label for="kode" class="control-label">Alamat</label>
                    <input type="text" name="alamat" value="<?php echo $data['alamat']; ?>" class="form-control" id="alamat">
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="kode" class="control-label">No. Telp</label>
                    <input type="text" name="notelp" value="<?php echo $data['notelp']; ?>" class="form-control" id="notelp">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <label for="kode" class="control-label">Sekolah Asal</label>
                    <input type="text" name="sek_asal" value="<?php echo $data['sek_asal']; ?>" class="form-control" id="sek_asal">
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="kode" class="control-label">Alamat Sekolah Asal</label>
                    <input type="text" name="sek_asal_alamat" value="<?php echo $data['sek_asal_alamat']; ?>" class="form-control" id="sek_asal_alamat">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Diterima di kelas</label>
                    <?php echo form_dropdown('diterima_kelas', $p_diterima_kelas, $data['diterima_kelas'], 'class="form-control" id="diterima_kelas" required'); ?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Diterima Tgl</label>
                    <input type="date" name="diterima_tgl" value="<?php echo $data['diterima_tgl']; ?>" class="form-control" id="diterima_tgl">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="kode" class="control-label">No. Ijazah</label>
                    <input type="text" name="ijazah_no" value="<?php echo $data['ijazah_no']; ?>" class="form-control" id="ijazah_no">
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label for="kode" class="control-label">Thn Ijazah</label>
                    <input type="text" name="ijazah_thn" value="<?php echo $data['ijazah_thn']; ?>" class="form-control" id="ijazah_thn">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="kode" class="control-label">No. SKHUN</label>
                    <input type="text" name="skhun_no" value="<?php echo $data['skhun_no']; ?>" class="form-control" id="skhun_no">
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label for="kode" class="control-label" style="font-size: 10px">Thn SKHUN</label>
                    <input type="text" name="skhun_thn" value="<?php echo $data['skhun_thn']; ?>" class="form-control" id="skhun_thn">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="kode" class="control-label">Nama Ayah</label>
                    <input type="text" name="ortu_ayah" value="<?php echo $data['ortu_ayah']; ?>" class="form-control" id="ortu_ayah">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="kode" class="control-label">Nama Ibu</label>
                    <input type="text" name="ortu_ibu" value="<?php echo $data['ortu_ibu']; ?>" class="form-control" id="ortu_ibu">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="kode" class="control-label">Alamat Ortu</label>
                    <input type="text" name="ortu_alamat" value="<?php echo $data['ortu_alamat']; ?>" class="form-control" id="ortu_alamat">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Telp Ortu</label>
                    <input type="text" name="ortu_notelp" value="<?php echo $data['ortu_notelp']; ?>" class="form-control" id="ortu_notelp">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Pekerjaan Ayah</label>
                    <input type="text" name="ortu_ayah_pkj" value="<?php echo $data['ortu_ayah_pkj']; ?>" class="form-control" id="ortu_ayah_pkj">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Pekerjaan Ibu</label>
                    <input type="text" name="ortu_ibu_pkj" value="<?php echo $data['ortu_ibu_pkj']; ?>" class="form-control" id="ortu_ibu_pkj">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Nama wali</label>
                    <input type="text" name="wali" value="<?php echo $data['wali']; ?>" class="form-control" id="wali">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Alamat Wali</label>
                    <input type="text" name="wali_alamat" value="<?php echo $data['wali_alamat']; ?>" class="form-control" id="wali_alamat">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">No. Telp rumah</label>
                    <input type="text" name="notelp_rumah" value="<?php echo $data['notelp_rumah']; ?>" class="form-control" id="notelp_rumah">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Pekerjaan Wali</label>
                    <input type="text" name="wali_pkj" value="<?php echo $data['wali_pkj']; ?>" class="form-control" id="wali_pkj">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="kode" class="control-label">Foto Siswa</label>
                    <input type="file" name="userfile" class="form-control" id="userfile">
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?php echo base_url().$url; ?>" class="btn btn-default" data-dismiss="modal">Kembali</a>
        <div class="clearfix"></div>
    
        </form>
    </div>
</div>


<script type="text/javascript">
    $(document).on("ready", function() {
        pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);

        $("#<?php echo $status_form; ?>").on("submit", function() {

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

</script>