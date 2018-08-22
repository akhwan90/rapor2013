<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Set Kelas</h4>

            </div>
            <div class="content">

                <form method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>" action="<?php echo base_url().$url; ?>/simpan">

                <div class="row">
                    <div class="col-md-4">
                        <label>Pilih Kelas</label>
                        <select name="kelas" id="kelas" class="form-control" required="true">
                            <option value=""></option>
                            <?php 
                            if (!empty($kelas)) {
                                foreach ($kelas as $k) {
                                    echo '<option value="'.$k['id'].'">'.$k['nama'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="">
                    <div class="col-md-12">
                        <label>Pilih Siswa (*) Untuk memilih satu persatu, gunakan Ctrl+Klik, Untuk memilih Semua gunakan Ctrl+A</label>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <select name="siswa_semua" size="20" multiple id="siswa_semua" class="form-control">
                            <?php 
                            if (!empty($siswa_asal)) {
                                foreach ($siswa_asal as $sa) {
                                    echo '<option value="'.$sa['id'].'">'.$sa['nama'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <center>
                        <button type="button" class="btn btn-success" id="tambah"><i class="fa fa-chevron-right"></i> </button>
                        <button type="button" class="btn btn-success" id="kurang"><i class="fa fa-chevron-left"></i> </button>
                        </center>
                    </div>
                    <div class="col-md-4">
                        <select name="siswa_pilih[]" size="20" multiple id="siswa_pilih" class="form-control" required="true">
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo base_url().$url; ?>" class="btn btn-default">Kembali</a>
                <div class="clearfix"></div>
            
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).on("ready", function() {
        $('#siswa_semua').pairMaster();

        $('#tambah').click(function(){
            $('#siswa_semua').addSelected('#siswa_pilih');
        });

        $('#kurang').click(function(){
            $('#siswa_pilih').removeSelected('#siswa_semua'); 
        });
    });

</script>