<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Upload Data Siswa</h4>

            </div>
            <div class="content">
                <div class="alert alert-success">
                    <h3>PETUNJUK IMPORT</h3>
                    <ul>
                        <li>Jangan ubah nama sheet</li>
                        <li>Kolom: NIS dan NISN, harap diketik dalam format cell = "TEXT"</li>
                        <li>Kolom: Jenis Kelamin, harap diisi L (jika Laki-laki) dan P (jika Perempuan)</li>
                        <li>Kolom: Tanggal lahir, Diterima Tgl, harap diisi dalam format cell = "TEXT" dengan format "YYYY-MM-DD"</li>
                        <li>Kolom: Status, diisi status anak, pilihanya "AK" jika Anak Kandung, dan "Anak Tiri" jika anak tiri</li>
                        <li>Kolom: Anak ke, harap diisi angka</li>
                        <li>Kolom: Diterima_smt diisi angka 1 atau 2, sesuai semester diterima masuk</li>
                        <li>Kolom: Tahun Ijazah dan Tahun SKHUN diisi angka tahun 4 digit</li>
                        <li>Kolom: Diterima Tgl, diisi pilihan VII, VIII atau IX </li>
                        <li>Semua nomor telepon/HP diisi dalam format cell = "TEXT"</li>
                    </ul>
                </div>
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