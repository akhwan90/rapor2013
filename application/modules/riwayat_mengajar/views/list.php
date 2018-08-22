<div class="card">
    <div class="header">
        <h4 class="title">Data Riwayat Mengajar</h4>

    </div>
    <div class="content">

        <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
            <thead>
                <td width="10%">No</td>
                <td width="10%">Tahun Ajaran</td>
                <td width="30%">Mapel</td>
                <td width="20%">Kelas</td>
                <td width="30%">Aksi</td>
            </thead>
            
            <tbody>
                <?php 
                $no = 1;
                if (!empty($history_mengajar)) {
                    foreach ($history_mengajar as $h) {
                ?>
                
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $h['tasm']; ?></td>
                    <td><?php echo $h['nmmapel']; ?></td>
                    <td><?php echo $h['nmkelas']; ?></td>
                    <td>
                        <a href="<?php echo base_url('n_pengetahuan/cetak/'.$h['id']); ?>" class="btn btn-info btn-xs" target="_blank">Cetak NP</a>
                        <a href="<?php echo base_url('n_keterampilan/cetak/'.$h['id_mapel'].'-'.$h['id_kelas'].'/'.$h['tasm']); ?>" class="btn btn-info btn-xs" target="_blank">Cetak NK</a>
                    </td>
                </tr>
                <?php 
                        $no++;
                    }
                }
                ?>
            </tbody>

        </table>
    </div>
</div>