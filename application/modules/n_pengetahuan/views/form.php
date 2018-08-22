<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Upload Data Nilai <?php echo $bawa['nmmapel']."-".$bawa['nmkelas']."-".$bawa['nmguru']; ?></h4>

            </div>
            <div class="content">

                <form method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>" action="<?php echo base_url().$url; ?>/import_nilai" enctype="multipart/form-data">
                <input type="hidden" name="id_guru_mapel" value="<?php echo $bawa['id']; ?>">
                <input type="hidden" name="id_kelas" value="<?php echo $id_kelas; ?>">

                <div class="row">
                    <div class="col-md-4">
                        <label>Pilih File</label>
                        <input type="file" class="form-control" name="import_excel" required="true">
                    </div>
                </div>
                
                <p>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo base_url().$url."/index/".$bawa['id']; ?>" class="btn btn-default">Kembali</a>
                <div class="clearfix"></div>
                </p>

                </form>
            </div>
        </div>
    </div>
</div>
