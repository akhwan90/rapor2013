<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Upload Data Siswa</h4>

            </div>
            <div class="content">
                <?php echo $this->session->flashdata('k'); ?>
                <form method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>" action="<?php echo base_url().$url; ?>/import_siswa" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-4">
                        <label>Pilih File</label>
                        <input type="file" class="form-control" name="import_excel" required="true">
                    </div>
                </div>
                
                <p>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo base_url().$url; ?>" class="btn btn-default">Kembali</a>
                <div class="clearfix"></div>
                </p>

                </form>
            </div>
        </div>
    </div>
</div>