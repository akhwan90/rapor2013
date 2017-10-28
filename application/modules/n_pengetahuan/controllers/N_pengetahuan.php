<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class N_pengetahuan extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');
        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['url'] = "n_pengetahuan";
        $this->d['idnya'] = "setmapel";
        $this->d['nama_form'] = "f_setmapel";
        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['semester'] = substr($this->d['tasm'], -1, 1);

        $this->kolom_xl = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    }
    public function cetak($bawa) {
        $pc_bawa = explode("-", $bawa);
        $detil_guru = $this->db->query("SELECT 
                                         b.nama nmmapel, c.nama nmkelas, d.nama nmguru
                                         FROM t_guru_mapel a
                                         INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                         INNER JOIN m_kelas c ON a.id_kelas = c.id
                                         INNER JOIN m_guru d ON a.id_guru = d.id 
                                         WHERE b.id = ".$pc_bawa[0]." AND c.id = ".$pc_bawa[1]."")->row_array();
        $q_nilai_harian = $this->db->query("SELECT 
                                d.nama nmsiswa, a.jenis, a.id_mapel_kd, a.id_siswa, a.nilai
                                FROM t_nilai a
                                LEFT JOIN t_mapel_kd b ON a.id_mapel_kd = b.id
                                LEFT JOIN t_kelas_siswa c ON CONCAT(a.id_siswa,LEFT(a.tasm,4)) = CONCAT(c.id_siswa,c.ta)
                                LEFT JOIN m_siswa d ON c.id_siswa = d.id
                                WHERE c.id_kelas = ".$pc_bawa[1]." AND b.id_mapel = ".$pc_bawa[0]."
                                AND a.tasm = '".$this->d['tasm']."'
                                ORDER BY d.id")->result_array();

        $q_nilai_ta = $this->db->query("SELECT 
                                d.nama nmsiswa, a.jenis, a.id_siswa, a.nilai
                                FROM t_nilai a
                                LEFT JOIN t_guru_mapel b ON a.id_guru_mapel = b.id 
                                LEFT JOIN t_kelas_siswa c ON CONCAT(a.id_siswa,LEFT(a.tasm,4)) = CONCAT(c.id_siswa,c.ta)
                                LEFT JOIN m_siswa d ON c.id_siswa = d.id
                                WHERE c.id_kelas = ".$pc_bawa[1]." AND b.id_mapel = ".$pc_bawa[0]." AND a.jenis != 'h'
                                AND a.tasm = '".$this->d['tasm']."'
                                ORDER BY d.id")->result_array();

        $q_kd_guru_ini = $this->db->query("SELECT a.* 
                                    FROM t_mapel_kd a
                                    LEFT JOIN m_kelas b ON a.tingkat = b.tingkat
                                    WHERE a.id_guru = '".$this->d['admkonid']."'
                                    AND a.id_mapel = '".$pc_bawa[0]."'
                                    AND b.id = ".$pc_bawa[1]."
                                    AND a.semester = '".$this->d['semester']."'
                                    AND a.jenis = 'P'")->result_array();

        //echo $this->db->last_query();
        
        /*                           
        $q_kd_guru_ini = $this->db->query("SELECT 
                                a.id, a.no_kd, a.nama_kd
                                FROM t_mapel_kd a
                                LEFT JOIN m_kelas b ON a.tingkat = b.tingkat
                                WHERE a.id_mapel = ".$pc_bawa[0]." AND b.id = ".$pc_bawa[1]." AND a.jenis = 'P'")->result_array();
        */
        /// mulai
        $d_kd = array();
        if (!empty($q_kd_guru_ini)) {
            foreach ($q_kd_guru_ini as $v) {
                $idx = $v['id'];
                $d_kd[$idx]['kode'] = $v['no_kd'];
                $d_kd[$idx]['nama_kd'] = $v['nama_kd'];
            }
        }
        $d_nilai = array();
        if (!empty($q_nilai_harian)) {
            foreach ($q_nilai_harian as $d) {
                $idx1 = $d['id_siswa'];
                $idx2 = $d['id_mapel_kd'];
                $d_nilai[$idx1]['nama'] = $d['nmsiswa'];
                $d_nilai[$idx1]['h'][$idx2]['nilai_huruf'] = nilai_huruf($d['nilai']);
                $d_nilai[$idx1]['h'][$idx2]['nilai_pre'] = nilai_pre($d['nilai']);
                $d_nilai[$idx1]['h'][$idx2]['nilai_angka'] = $d['nilai'];
            }
        } 
        if (!empty($q_nilai_ta)) {
            foreach ($q_nilai_ta as $e) {
                $idx1 = $e['id_siswa'];
                
                if ($e['jenis'] == "t") {
                    $d_nilai[$idx1]['uts'] = $e['nilai'];
                } else if ($e['jenis'] == "a") {
                    $d_nilai[$idx1]['uas'] = $e['nilai'];
                }
            }
        } 
        $jml_kd = sizeof($d_kd);

        $html = '<p align="left"><b>REKAP NILAI PENGETAHUAN</b>
                <br>
                Mata Pelajaran : '.$detil_guru['nmmapel'].', Kelas : '.$detil_guru['nmkelas'].', Guru : '.$detil_guru['nmguru'].'. Tahun Pelajaran '.$this->d['tasm'].'<hr style="border: solid 1px #000; margin-top: -10px"></p>
                <table class="table"><thead><tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama</th>
                <th colspan="'.$jml_kd.'">Nama</th>
                <th rowspan="2">Rata-rata UH</th>
                <th rowspan="2">UTS</th>
                <th rowspan="2">UAS</th>
                <th colspan="3">Nilai Akhir</th>
                </tr>
                <tr>';
        if (!empty($d_kd)) {
            foreach ($d_kd as $kd) {
                $html .= '<th>'.$kd['kode'].'</th>';
            }
        }
        $html .= '<th>Nilai</th><th>Predikat</th><th>Deskripsi</th></tr></thead><tbody>';
        if (!empty($d_nilai)) {
            $no = 1;
            foreach ($d_nilai as $ke => $dn) {
                
                $html .= '<tr><td>'.$no.'</td><td>'.$dn['nama'].'</td>';
                $jml_nilai_tugas = 0;
                $array_kurang = array();
                $array_cukup = array();
                $array_baik = array();
                $array_sangat_baik = array();
                $array_undefined = array();
                $kurang = "";
                $cukup = "";
                $baik = "";
                $sangat_baik = "";
                $undefined = "";
                
                if (!empty($d_kd)) {
                    foreach ($d_kd as $k => $v) {
                        $id_siswa = $ke;
                        $id_kd = $k;
                        $nil_huruf = empty($dn["h"][$id_kd]['nilai_huruf']) ? "" : $dn["h"][$id_kd]['nilai_huruf'];
                        if ($nil_huruf == "D") {
                            $array_kurang[] = $v['nama_kd'];
                        } else if ($nil_huruf == "C") {
                            $array_cukup[] = $v['nama_kd'];
                        } else if ($nil_huruf == "B") {
                            $array_baik[] = $v['nama_kd'];
                        } else if ($nil_huruf == "A") {
                            $array_sangat_baik[] = $v['nama_kd'];
                        } else {
                            $array_undefined[] = "un";
                        }
                        $nilai_uh = empty($dn["h"][$id_kd]["nilai_angka"]) ? '' : $dn["h"][$id_kd]["nilai_angka"];
                        //$dn[119]["h"][45]["nilai_angka"]
                        $html .= '<td class="ctr">'.$nilai_uh.'</td>';
                        $jml_nilai_tugas += $nilai_uh;
                        //$html .= '<td>'.var_dump($dn).'</td>';
                    }
                }
                $jumlah_porsi = $this->config->item('pnp_h')+$this->config->item('pnp_t')+$this->config->item('pnp_a');
                $porsi_h = (($this->config->item('pnp_h') / $jumlah_porsi) * 100) / 100;
                $porsi_t = (($this->config->item('pnp_t') / $jumlah_porsi) * 100) / 100;
                $porsi_a = (($this->config->item('pnp_a') / $jumlah_porsi) * 100) / 100;
                $n_h    = number_format($jml_nilai_tugas / $jml_kd);
                $n_uts  = empty($dn["uts"]) ? '' : number_format($dn["uts"]);
                $n_uas  = empty($dn["uas"]) ? '' : number_format($dn["uas"]);
                $nilai_akhir = number_format(($porsi_h*$n_h)+($porsi_t*$n_uts)+($porsi_a*$n_uas));
                $kurang = empty($array_kurang) ? "" : "KURANG, pada : ".implode(", ", $array_kurang)."; ";
                $cukup = empty($array_cukup) ? "" : "CUKUP, pada : ".implode(", ", $array_cukup)."; ";
                $baik = empty($array_baik) ? "" : "BAIK, pada : ".implode(", ", $array_baik)."; ";
                $sangat_baik = empty($array_sangat_baik) ? "" : "SANGAT BAIK, pada : ".implode(", ", $array_sangat_baik)."; ";
                
                $html .= '<td>'.$n_h.'</td><td>'.$n_uts.'</td><td>'.$n_uas.'</td><td>'.$nilai_akhir.'</td><td>'.nilai_huruf($nilai_akhir).'</td><td>'.$kurang.$cukup.$baik.$sangat_baik.'</td>';
                $no++;
            }
        } else {
            $td_colspan_kosong = 9 + $jml_kd;
            $html .= '<tr><td colspan="'.$td_colspan_kosong.'">Belum ada data</td></tr>';
        }
        $this->d['html'] = $html;
        $this->load->view('cetak', $this->d);
    }

    public function import($bawa) {
        $this->load->library('excel');
        
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Nur Akhwan")
                             ->setLastModifiedBy("Aplikasi Rapor Kurikulum 2013")
                             ->setTitle("Office 2007 XLSX Test Document")
                             ->setSubject("Office 2007 XLSX Test Document")
                             ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                             ->setKeywords("office 2007 openxml php")
                             ->setCategory("Test result file");
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Hanya isi pada cell dengan background HIJAU');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Cukup isikan di isian nilai Per KD, UTS dan UAS');

        $objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

        //query utama import
        $pc_bawa = explode("-", $bawa);
        $detil_guru = $this->db->query("SELECT 
                                         a.id_guru, b.nama nmmapel, c.nama nmkelas, d.nama nmguru
                                         FROM t_guru_mapel a
                                         INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                         INNER JOIN m_kelas c ON a.id_kelas = c.id
                                         INNER JOIN m_guru d ON a.id_guru = d.id 
                                         WHERE b.id = ".$pc_bawa[0]." AND c.id = ".$pc_bawa[1]."")->row_array();
        $q_nilai_harian = $this->db->query("SELECT 
                                d.nama nmsiswa, a.jenis, a.id_mapel_kd, a.id_siswa, a.nilai
                                FROM t_nilai a
                                LEFT JOIN t_mapel_kd b ON a.id_mapel_kd = b.id
                                LEFT JOIN t_kelas_siswa c ON CONCAT(a.id_siswa,LEFT(a.tasm,4)) = CONCAT(c.id_siswa,c.ta)
                                LEFT JOIN m_siswa d ON c.id_siswa = d.id
                                WHERE c.id_kelas = ".$pc_bawa[1]." AND b.id_mapel = ".$pc_bawa[0]."
                                AND a.tasm = '".$this->d['tasm']."'
                                ORDER BY d.id")->result_array();

        $q_nilai_ta = $this->db->query("SELECT 
                                d.nama nmsiswa, a.jenis, a.id_siswa, a.nilai
                                FROM t_nilai a
                                LEFT JOIN t_guru_mapel b ON a.id_guru_mapel = b.id 
                                LEFT JOIN t_kelas_siswa c ON CONCAT(a.id_siswa,LEFT(a.tasm,4)) = CONCAT(c.id_siswa,c.ta)
                                LEFT JOIN m_siswa d ON c.id_siswa = d.id
                                WHERE c.id_kelas = ".$pc_bawa[1]." AND b.id_mapel = ".$pc_bawa[0]." AND a.jenis != 'h'
                                AND a.tasm = '".$this->d['tasm']."'
                                ORDER BY d.id")->result_array();
        $q_kd_guru_ini = $this->db->query("SELECT a.* 
                                    FROM t_mapel_kd a
                                    LEFT JOIN m_kelas b ON a.tingkat = b.tingkat
                                    WHERE a.id_guru = '".$this->d['admkonid']."'
                                    AND a.id_mapel = '".$pc_bawa[0]."'
                                    AND b.id = ".$pc_bawa[1]." 
                                    AND a.semester = '".$this->d['semester']."'
                                    AND a.jenis = 'P'")->result_array();
        $q_siswa_kelas = $this->db->query("SELECT a.id_siswa, b.nama nmsiswa FROM t_kelas_siswa a LEFT JOIN m_siswa b ON a.id_siswa = b.id WHERE a.id_kelas = '".$pc_bawa[1]."'")->result_array();

        $d_kd = array();
        if (!empty($q_kd_guru_ini)) {
            foreach ($q_kd_guru_ini as $v) {
                $idx = $v['id'];
                $d_kd[$idx]['kode'] = $v['no_kd'];
            }
        }
        $d_nilai = array();
        if (!empty($q_nilai_harian)) {
            foreach ($q_nilai_harian as $d) {
                $idx1 = $d['id_siswa'];
                $idx2 = $d['id_mapel_kd'];
                $d_nilai[$idx1]['nama'] = $d['nmsiswa'];
                $d_nilai[$idx1]['h'][$idx2]['nilai_angka'] = $d['nilai'];
            }
        } else {
            foreach ($q_siswa_kelas as $dk) {
                $idx1 = $dk['id_siswa'];

                foreach ($q_kd_guru_ini as $kg) {
                    $idx2 = $kg['id'];
                    $d_nilai[$idx1]['nama'] = $dk['nmsiswa'];
                    $d_nilai[$idx1]['h'][$idx2]['nilai_angka'] = 0;
                }
            }
        }

        if (!empty($q_nilai_ta)) {
            foreach ($q_nilai_ta as $e) {
                $idx1 = $e['id_siswa'];
                
                if ($e['jenis'] == "t") {
                    $d_nilai[$idx1]['uts'] = $e['nilai'];
                } else if ($e['jenis'] == "a") {
                    $d_nilai[$idx1]['uas'] = $e['nilai'];
                }
            }
        } else {
            foreach ($q_siswa_kelas as $dk) {
                $idx1 = $dk['id_siswa'];
                
                $d_nilai[$idx1]['uts'] = 0;
                $d_nilai[$idx1]['uas'] = 0;
            }
        }

        $jml_kd = sizeof($d_kd);
        //akhir ambil variabel db

        /// mulai
        $objPHPExcel->getActiveSheet()->setCellValue('A4', 'ID Mapel');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', $pc_bawa[0]);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', ": ".$detil_guru['nmmapel']);
        $objPHPExcel->getActiveSheet()->setCellValue('A5', 'ID Guru');
        $objPHPExcel->getActiveSheet()->setCellValue('B5', $detil_guru['id_guru']);
        $objPHPExcel->getActiveSheet()->setCellValue('C5', ": ".$detil_guru['nmguru']);
        $objPHPExcel->getActiveSheet()->setCellValue('A6', 'ID Kelas');
        $objPHPExcel->getActiveSheet()->setCellValue('B6', $pc_bawa[1]);
        $objPHPExcel->getActiveSheet()->setCellValue('C6', ": ".$detil_guru['nmkelas']);

        $objPHPExcel->getActiveSheet()->getStyle('C4:C6')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B4:B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B4:B6')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle('B4:B6')->getFont()->getColor()->setARGB('ffffff');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getStyle('7')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('7')->getFont()->setSize(11);

        $objPHPExcel->getActiveSheet()->setCellValue('A7', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('B7', 'Nama');

        $kolom_awal = 2;
        $kolom = $kolom_awal;
        foreach($d_kd as $k) {
            $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', $k['kode']);
            $objPHPExcel->getActiveSheet()->getColumnDimension($this->kolom_xl[$kolom])->setWidth(10);
            $kolom++;
        }
        
        $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', 'UTS');
        $kolom++;
        $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', 'UAS');
        $kolom++;

        foreach($d_kd as $k) {        
            //$objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', $kolom);
            if (!empty($this->kolom_xl[$kolom])) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($this->kolom_xl[$kolom])->setVisible(FALSE);
                $kolom++;
                $objPHPExcel->getActiveSheet()->getColumnDimension($this->kolom_xl[$kolom])->setVisible(FALSE);
                $kolom++;
            }
        }
        
        //bds = baris mulai data nilai
        $bds = 8;


        if (!empty($d_nilai)) {
            $no = 1;
            foreach ($d_nilai as $ke => $dn) {
                $ke = empty($ke) ? "" : $ke;
                $nama = empty($dn['nama']) ? "" : $dn['nama'];
                
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$bds, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$bds, $nama);
                
                $klm = $kolom_awal;
                if (!empty($d_kd)) {
                    foreach ($d_kd as $k => $v) {
                        $id_siswa = $ke;
                        $id_kd = $k;
                        
                        $nilai_uh = empty($dn["h"][$id_kd]["nilai_angka"]) ? '' : $dn["h"][$id_kd]["nilai_angka"];
                        $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $nilai_uh);
                        $klm++;
                    }
                } else {
                    exit("KD masih kosong. Silakan diisi");
                }

                $n_uts  = empty($dn["uts"]) ? '' : number_format($dn["uts"]);
                $n_uas  = empty($dn["uas"]) ? '' : number_format($dn["uas"]);
                
                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $n_uts);
                $klm++;
                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $n_uas);
                $klm++;
                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $id_siswa);
                $klm++;
                
                foreach ($d_kd as $k => $v) {
                    $id_siswa = $ke;
                    $id_kd = $k;
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, "h-".$id_kd);                        
                    $klm++;
                }

                $bds++;
                $no++;
                
            }
        }

        $koordinat_awal = "C8";
        $koordinat_akhir = $this->kolom_xl[(($kolom-2)-$jml_kd)].($bds-1);

        $objPHPExcel->getActiveSheet()->getStyle($koordinat_awal.':'.$koordinat_akhir)->applyFromArray(
        array('fill'    => array(
                                    'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color'     => array('argb' => '01ff80')
                                )
             )
        );    
        //proteksi cell
        $objPHPExcel->getActiveSheet()->getProtection()->setPassword('super90');
        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
        $objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
        $objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
        $objPHPExcel->getActiveSheet()->getProtection()->setInsertColumns(true);
        $objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
        //proteksi kecuali untuk sheet
        $objPHPExcel->getActiveSheet()->getStyle($koordinat_awal.":".$koordinat_akhir)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="NP_'.str_replace(" ","",$detil_guru['nmkelas'])."_".str_replace(" ", "_", $detil_guru['nmmapel']).'_'.str_replace(" ", "_", $detil_guru['nmguru']).'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function upload($bawa) {
        $pc_bawa = explode("-", $bawa);
        
        $detil_guru = $this->db->query("SELECT 
                                         a.id, a.id_guru, b.nama nmmapel, c.nama nmkelas, d.nama nmguru
                                         FROM t_guru_mapel a
                                         INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                         INNER JOIN m_kelas c ON a.id_kelas = c.id
                                         INNER JOIN m_guru d ON a.id_guru = d.id 
                                         WHERE b.id = ".$pc_bawa[0]." AND c.id = ".$pc_bawa[1]."")->row_array();
        

        $this->d['bawa'] = $detil_guru;
        $this->d['id_kelas'] = $pc_bawa[1];
        $this->d['p'] = "form";
        $this->load->view("template_utama", $this->d);
    }

    public function import_nilai() {
        //queri init
        $id_guru_mapel = $this->input->post('id_guru_mapel');
        $id_kelas = $this->input->post('id_kelas');

        $detil_mp = $this->db->query("SELECT 
                                        a.*, b.nama nmmapel, c.nama nmkelas, c.tingkat tingkat
                                        FROM t_guru_mapel a
                                        INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                        INNER JOIN m_kelas c ON a.id_kelas = c.id 
                                        WHERE a.id  = '$id_guru_mapel'")->row_array();
        $list_kd = $this->db->query("SELECT * FROM t_mapel_kd 
                                    WHERE id_guru = '".$detil_mp['id_guru']."'
                                    AND id_mapel = '".$detil_mp['id_mapel']."'
                                    AND tingkat = '".$detil_mp['tingkat']."'
                                    AND jenis = 'P'
                                    AND semester = '".$this->d['semester']."'");
        $d_list_kd = $list_kd->result_array();
        $j_list_kd = $list_kd->num_rows();

        $q_siswa_kelas = $this->db->query("SELECT a.id_siswa FROM t_kelas_siswa a WHERE a.id_kelas = '".$id_kelas."'");
        $d_list_siswa = $q_siswa_kelas->result_array();        
        $j_list_siswa = $q_siswa_kelas->num_rows();        


        
        $idx_kolom_mulai = 2;
        $idx_kolom_selesai = ($idx_kolom_mulai + ((2*$j_list_kd) + 2)) - 1;
        $idx_baris_mulai = 8;
        $idx_baris_selesai = $idx_baris_mulai + $j_list_siswa;

        $idx_kolom_hide = $idx_kolom_mulai + $j_list_kd;
        //echo $idx_kolom_hide;


        $target_file = './upload/temp/';
        move_uploaded_file($_FILES["import_excel"]["tmp_name"], $target_file.$_FILES['import_excel']['name']);

        $file   = explode('.',$_FILES['import_excel']['name']);
        $length = count($file);

        if($file[$length -1] == 'xlsx' || $file[$length -1] == 'xls') {
            //jagain barangkali uploadnya selain file excel
            $tmp    = './upload/temp/'.$_FILES['import_excel']['name'];
            //Baca dari tmp folder jadi file ga perlu jadi sampah di server :-p
            
            $this->load->library('excel');//Load library excelnya
            $read   = PHPExcel_IOFactory::createReaderForFile($tmp);
            $read->setReadDataOnly(true);
            $excel  = $read->load($tmp);

            //echo $tmp;
    
            $_sheet = $excel->setActiveSheetIndexByName('Worksheet');//Kunci sheetnye biar kagak lepas :-p
            

            $x_id_mapel = $_sheet->getCell('B4')->getCalculatedValue();
            $x_id_guru = $_sheet->getCell('B5')->getCalculatedValue();
            $x_id_kelas = $_sheet->getCell('B6')->getCalculatedValue();

            //echo $x_id_mapel."/".$detil_mp['id_mapel']."-".$x_id_guru."/".$detil_mp['id_guru']."-".$x_id_kelas."/".$detil_mp['id_kelas'];

            if ($x_id_mapel != $detil_mp['id_mapel'] || $x_id_guru != $detil_mp['id_guru'] || $x_id_kelas != $detil_mp['id_kelas']) {
                echo "File Excel SALAH";
                exit;
            }

            $data = array();

            //ambil id_siwa mumet
            
            //var tetap
            $tasm = $this->d['tasm'];
            $id_guru_mapel = $id_guru_mapel;
            $id_mapel = $detil_mp['id_mapel'];


            for ($b = $idx_baris_mulai; $b < $idx_baris_selesai; $b++) {
                $idx_klm = $j_list_kd + 4;
                $va_xy_id_siswa = $_sheet->getCell($this->kolom_xl[$idx_klm].$b)->getCalculatedValue();
                $id_siswa = $va_xy_id_siswa;

                //ngitung mulai sik uas
                $idx_mulai_uts = $idx_kolom_mulai+($j_list_kd);

                $xy_nilai_uts = $_sheet->getCell($this->kolom_xl[($idx_mulai_uts)].$b)->getCalculatedValue();
                $xy_nilai_uas = $_sheet->getCell($this->kolom_xl[($idx_mulai_uts+1)].$b)->getCalculatedValue();
                
                $data[] = array("tasm"=>$tasm, "jenis"=>"t", "id_guru_mapel"=>$id_guru_mapel, "id_mapel_kd"=>$id_mapel, "id_siswa"=>$id_siswa, "nilai"=>$xy_nilai_uts);
                $data[] = array("tasm"=>$tasm, "jenis"=>"a", "id_guru_mapel"=>$id_guru_mapel, "id_mapel_kd"=>$id_mapel, "id_siswa"=>$id_siswa, "nilai"=>$xy_nilai_uas);
                
                //nilai kd
                $tmb = $j_list_kd + 3;

                for ($k = $idx_kolom_mulai; $k < ($idx_kolom_hide); $k++) {
                    $nilai = $_sheet->getCell($this->kolom_xl[$k].$b)->getCalculatedValue();
                    $hide = $_sheet->getCell($this->kolom_xl[($k+$tmb)].$b)->getCalculatedValue();
                    
                    $pc_hide = explode("-", $hide);
                    $id_mapel_kd = (int) $pc_hide[1];
                    
                    //echo $pc_hide[1];
                    /*
                    echo "Id_Siswa : ".$id_siswa.", ";
                    echo "NIlai : ".$nilai.", ";
                    echo "Hide : ".$id_mapel_kd;
                    */

                    $data[] = array("tasm"=>$tasm, "jenis"=>"h", "id_guru_mapel"=>$id_guru_mapel, "id_mapel_kd"=>$id_mapel_kd, "id_siswa"=>$id_siswa, "nilai"=>$nilai);
                    //echo "/";
                } 
                //echo "<br>";
            }

            //exit;

            $strq = "REPLACE INTO t_nilai (tasm, jenis, id_guru_mapel, id_mapel_kd, id_siswa, nilai) VALUES ";
            $arr_perdata = array();
            foreach ($data as $d) {
                $arr_perdata[] = "('".$d['tasm']."', '".$d['jenis']."', '".$d['id_guru_mapel']."', '".$d['id_mapel_kd']."', '".$d['id_siswa']."', '".$d['nilai']."')";
            }
            $strq .= implode(",", $arr_perdata).";";
            
            //echo $strq;
            //exit;

            $this->db->query($strq);

            @unlink('./upload/temp/form_upload_nilai_pindah.xlsx');
            
            $this->session->set_flashdata('k', '<div class="alert alert-success">Nilai berhasil diupload..</div>');
            redirect('n_pengetahuan/index/'.$id_guru_mapel);

        } else {
            exit('Buka File Excel...');//pesan error tipe file tidak tepat
        }
        redirect('n_pengetahuan/index/'.$id_guru_mapel);
    }

    public function ambil_siswa($kelas) {
        $id_kd = $this->uri->segment(4);
        $jenis = $this->uri->segment(5);
        $list_data = array();
        if ($jenis == "h") {
            $ambil_nilai = $this->db->query("SELECT
                                        b.id ids, 
                                        b.nama nama, 
                                        IFNULL(a.nilai,0) nilai
                                        FROM m_siswa b 
                                        INNER JOIN t_nilai a ON a.id_siswa = b.id
                                        INNER JOIN t_guru_mapel c ON a.id_guru_mapel = c.id
                                        INNER JOIN t_mapel_kd d ON a.id_mapel_kd = d.id 
                                        INNER JOIN m_guru e ON c.id_guru = e.id 
                                        INNER JOIN m_mapel f ON c.id_mapel = f.id
                                        INNER JOIN t_kelas_siswa g ON a.id_siswa = g.id_siswa
                                        INNER JOIN m_kelas h ON g.id_kelas = h.id
                                        WHERE h.id = $kelas AND a.id_mapel_kd = $id_kd
                                        AND a.jenis = '$jenis' 
                                        AND a.tasm = '".$this->d['tasm']."'
                                        ORDER BY b.nama ASC")->result_array();
        } else {
            $ambil_nilai = $this->db->query("SELECT
                                        b.id ids, 
                                        b.nama nama, 
                                        IFNULL(a.nilai,0) nilai
                                        FROM m_siswa b 
                                        INNER JOIN t_nilai a ON a.id_siswa = b.id
                                        INNER JOIN t_guru_mapel c ON a.id_guru_mapel = c.id
                                        INNER JOIN m_guru e ON c.id_guru = e.id 
                                        INNER JOIN m_mapel f ON c.id_mapel = f.id
                                        INNER JOIN t_kelas_siswa g ON a.id_siswa = g.id_siswa
                                        INNER JOIN m_kelas h ON g.id_kelas = h.id
                                        WHERE h.id = $kelas AND c.id_mapel = $id_kd
                                        AND a.jenis = '$jenis' 
                                        AND a.tasm = '".$this->d['tasm']."'
                                        ORDER BY b.nama ASC")->result_array();
            
        }
        
        //echo $this->db->last_query();
        //exit;
        
        if (empty($ambil_nilai)) {
            $list_data = $this->db->query("SELECT 
                                        b.id ids, b.nama, 0 nilai
                                        FROM t_kelas_siswa a 
                                        INNER JOIN m_siswa b ON a.id_siswa = b.id
                                        WHERE a.id_kelas = $kelas ORDER BY b.nama ASC")->result_array();
        } else {
            $list_data = $ambil_nilai;
        }

        //echo $this->db->last_query();

        $d['status'] = "ok";
        $d['data'] = $list_data;
        j($d);
    }
    public function simpan_nilai() {
        $p = $this->input->post();
        $jumlah_sudah = 0;
        $i = 0;
        
        $queri = array();
        
        foreach ($p['nilai'] as $s) {
            
            $cek = $this->db->query("SELECT id FROM t_nilai WHERE id_guru_mapel = '".$p['id_guru_mapel']."' AND id_mapel_kd = '".$p['id_mapel_kd']."' AND id_siswa = '".$p['id_siswa'][$i]."' AND jenis = '".$p['jenis']."' AND tasm = '".$this->d['tasm']."'")->num_rows();
            //$queri[] = $this->db->last_query();
            //exit;
            if ($cek > 0) {
                $jumlah_sudah ++;
                $this->db->query("UPDATE t_nilai SET nilai = '$s' WHERE id_guru_mapel = '".$p['id_guru_mapel']."' AND id_mapel_kd = '".$p['id_mapel_kd']."' AND id_siswa = '".$p['id_siswa'][$i]."' AND jenis = '".$p['jenis']."'");
                //$queri[] = "update";
            } else {
                $this->db->query("INSERT INTO t_nilai (tasm,jenis, id_guru_mapel, id_mapel_kd, id_siswa, nilai) VALUES ('".$this->d['tasm']."', '".$p['jenis']."', '".$p['id_guru_mapel']."', '".$p['id_mapel_kd']."', '".$p['id_siswa'][$i]."', '".$s."')");
                //$queri[] = "add";
            }
            $i++;
        }
        
        $d['status'] = "ok";
        $d['data'] = $i."Data berhasil disimpan ";
        j($d);
    }
    public function hapus($id) {
        $this->db->query("DELETE FROM t_guru_mapel WHERE id = '$id'");
        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }
    public function index($id) {

        $this->d['detil_mp'] = $this->db->query("SELECT 
                                        a.*, b.nama nmmapel, c.nama nmkelas, c.tingkat tingkat
                                        FROM t_guru_mapel a
                                        INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                        INNER JOIN m_kelas c ON a.id_kelas = c.id 
                                        WHERE a.id  = '$id'")->row_array();
        $this->d['list_kd'] = $this->db->query("SELECT * FROM t_mapel_kd 
                                    WHERE id_guru = '".$this->d['detil_mp']['id_guru']."'
                                    AND id_mapel = '".$this->d['detil_mp']['id_mapel']."'
                                    AND tingkat = '".$this->d['detil_mp']['tingkat']."'
                                    AND semester = '".$this->d['semester']."'
                                    AND jenis = 'P'")->result_array();
    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }
}