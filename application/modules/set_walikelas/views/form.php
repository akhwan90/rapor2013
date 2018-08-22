<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Set Mata Pelajaran Guru</h4>

            </div>
            <div class="content">

                <form method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>" action="<?php echo base_url().$url; ?>/simpan">

                <div class="row">
                    <div class="col-md-4">
                        <label>Pilih Guru</label>
                        <select name="guru" id="guru" class="form-control" required="true">
                            <option value=""></option>
                            <?php 
                            if (!empty($r_guru)) {
                                foreach ($r_guru as $g) {
                                    echo '<option value="'.$g['id'].'">'.$g['nama'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Pilih Mapel</label>
                        <select name="mapel" id="mapel" class="form-control" required="true">
                            <option value=""></option>
                            <?php 
                            if (!empty($r_mapel)) {
                                foreach ($r_mapel as $m) {
                                    echo '<option value="'.$m['id'].'">'.$m['nama'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="">
                    <div class="col-md-12">
                        <label>Pilih Kelas (*) Untuk memilih satu persatu, gunakan Ctrl+Klik, Untuk memilih Semua gunakan Ctrl+A</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <select name="data_semua" size="10" multiple id="data_semua" class="form-control">
                            <?php 
                            if (!empty($r_kelas)) {
                                foreach ($r_kelas as $k) {
                                    echo '<option value="'.$k['id'].'">'.$k['nama'].'</option>';
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
                        <select name="data_pilih[]" size="10" multiple id="data_pilih" class="form-control" required="true">
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
        $('#data_semua').pairMaster();

        $('#tambah').click(function(){
            $('#data_semua').addSelected('#data_pilih');
        });

        $('#kurang').click(function(){
            $('#data_pilih').removeSelected('#data_semua'); 
        });
    });

</script>