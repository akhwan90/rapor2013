<div class="card">
    <div class="content">
        <h4>Cetak Raport</h4>
        <form action="<?php echo base_url('home/cetak_rapot_ok'); ?>" method="post" class="form" id="f_ubah_password" target="_blank">
            <div class="form-group">
                <label>Nama Siswa</label>
                <select class="form-control col-md-4 js-example-basic-single"  required name="id_siswa">
                    <option value="">-- Nama Siswa --</option>
                    <?php 
                    if (!empty($siswa)) {
                        foreach ($siswa as $s) {
                            echo '<option value="'.$s['id'].'">'.$s['nama'].'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tahun / Semester</label>
                <select class="form-control col-md-4" required name="tahun">
                    <option value="">-- Tahun / Semester --</option>
                    <?php 
                    if (!empty($tahun)) {
                        foreach ($tahun as $t) {
                            echo '<option value="'.$t['tahun'].'">'.$t['tahun'].'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <br><br>
                <button type="submit" class="btn btn-success"><i class="fa fa-print"></i> Cetak</button>
            </div>
        </form>
        
        
        <h4>Cetak Leger</h4>
        <div class="alert alert-info">Coming soon...</div>
    </div>
</div>