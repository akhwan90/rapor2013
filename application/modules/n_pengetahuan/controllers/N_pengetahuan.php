<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

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

        $this->d['tahun'] = substr($this->d['tasm'], 0, 4);

        $this->kolom_xl = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $this->d['id_guru_mapel'] = 0;
    }
    public function cetak($bawa) {
        $strq_detail_guru = "select 
                a.tasm, b.nama nmguru, c.nama nmkelas, d.nama nmmapel
                from t_guru_mapel a
                inner join m_guru b on a.id_guru = b.id
                inner join m_kelas c on a.id_kelas = c.id
                inner join m_mapel d on a.id_mapel = d.id
                where a.id = '".$bawa."'";
        $detil_guru = $this->db->query($strq_detail_guru)->row_array();
        $ta = substr($detil_guru['tasm'],0,4);

        $strq_np = "select 
                a.id_siswa, a.jenis, a.id_mapel_kd, a.nilai
                from t_nilai a 
                where a.id_guru_mapel = '".$bawa."'
                group by a.id_siswa, a.jenis, a.id_mapel_kd";
                
        
        $strq_kd = "select 
                b.id, b.no_kd
                from t_nilai a 
                inner join t_mapel_kd b on a.id_mapel_kd = b.id
                where a.id_guru_mapel = '".$bawa."' and b.jenis = 'P'
                group by a.id_mapel_kd";
        $strq_siswa = "select
                b.id_siswa, c.nama
                from t_guru_mapel a 
                inner join t_kelas_siswa b on a.id_kelas = b.id_kelas
                inner join m_siswa c on b.id_siswa = c.id
                where a.id = '".$bawa."' and b.ta = '".$ta."'";

        $queri_np = $this->db->query($strq_np)->result_array();
        $queri_kd = $this->db->query($strq_kd)->result_array();
        $jml_kd = $this->db->query($strq_kd)->num_rows();
        $queri_siswa = $this->db->query($strq_siswa)->result_array();

        $data_np = array();
        foreach ($queri_np as $a) {
            $idx1 = $a['id_siswa'];
            $idx2 = $a['jenis'];
            $idx3 = $a['id_mapel_kd'];
            if ($a['jenis'] == "t") {
                $data_np[$idx1][$idx2] = $a['nilai'];
            } else if ($a['jenis'] == "a") {
                $data_np[$idx1][$idx2] = $a['nilai'];
            } else {
                $data_np[$idx1][$idx2][$idx3] = $a['nilai'];
            }
        }

        $html = '<p align="left"><b>REKAP NILAI PENGETAHUAN</b>
                <br>
                Mata Pelajaran : '.$detil_guru['nmmapel'].', Kelas : '.$detil_guru['nmkelas'].', Guru : '.$detil_guru['nmguru'].'. Tahun Pelajaran '.$detil_guru['tasm'].'<hr style="border: solid 1px #000; margin-top: -10px"></p>';

        $html .= '<table class="table"><tr><td rowspan="2">Nama</td><td colspan="'.$jml_kd.'">NH</td><td rowspan="2">Rata-rata NH</td><td rowspan="2">UTS</td><td rowspan="2">UAS</td><td rowspan="2">Nilai Akhir</td></tr><tr>';
        foreach ($queri_kd as $k) {
            $html .= '<td>'.$k['no_kd'].'</td>';
        }
        $html .= '</tr>';

        foreach ($queri_siswa as $s) {
            $idxs = $s['id_siswa'];
            $html .= '<tr><td>'.$s['nama'].'</td>';
            $jml_nilai_kd = 0;
            foreach ($queri_kd as $k) {
                $idxk = $k['id'];
                $nilai_kd = !empty($data_np[$idxs]['h'][$idxk]) ? number_format($data_np[$idxs]['h'][$idxk]) : 0;
                $jml_nilai_kd += $nilai_kd;

                $html .= '<td>'.$nilai_kd.'</td>';
            }
            if ($jml_kd > 0) {
                $rata_rata_nilai_kd = number_format($jml_nilai_kd / $jml_kd);
            } else {
                $rata_rata_nilai_kd = 0;
            }
            $nilai_uts = !empty($data_np[$idxs]['t']) ? number_format($data_np[$idxs]['t']) : 0;
            $nilai_uas = !empty($data_np[$idxs]['a']) ? number_format($data_np[$idxs]['a']) : 0;
            $html .= '<td>'.$rata_rata_nilai_kd.'</td><td>'.$nilai_uts.'</td><td>'.$nilai_uas.'</td>';
            
            $p_h = $this->config->item('pnp_h');
            $p_t = $this->config->item('pnp_t');
            $p_a = $this->config->item('pnp_a');
            $jml = $p_h+$p_t+$p_a;

            $p_h = ($p_h / $jml) * 100; 
            $p_t = ($p_t / $jml) * 100; 
            $p_a = ($p_a / $jml) * 100; 
            
            $na_np = number_format((($rata_rata_nilai_kd * $p_h) + ($nilai_uts * $p_t) + ($nilai_uas * $p_a)) / 100);
            
            $html .= '<td>'.$na_np.'</td></tr>';

        }

        $html .= '</table>';

        //j($data_np);
        //exit;

        //echo $html;
        //exit;
        //j($data_np);
        //exit;
        /*
        $pc_bawa = explode("-", $bawa);
        $id_guru_mapel = $this->session->userdata('id_guru_mapel');

        $detil_guru = $this->db->query("SELECT 
                                         b.nama nmmapel, c.nama nmkelas, d.nama nmguru
                                         FROM t_guru_mapel a
                                         INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                         INNER JOIN m_kelas c ON a.id_kelas = c.id
                                         INNER JOIN m_guru d ON a.id_guru = d.id 
                                         WHERE b.id = ".$pc_bawa[0]." AND c.id = ".$pc_bawa[1]." 
                                         AND a.tasm = '".$this->d['tasm']."'")->row_array();


        /*
        $q_nilai_harian = $this->db->query("SELECT 
                                d.nama nmsiswa, a.jenis, a.id_mapel_kd, a.id_siswa, a.nilai
                                FROM t_nilai a
                                LEFT JOIN t_mapel_kd b ON a.id_mapel_kd = b.id
                                LEFT JOIN t_kelas_siswa c ON CONCAT(a.id_siswa,LEFT(a.tasm,4)) = CONCAT(c.id_siswa,c.ta)
                                LEFT JOIN m_siswa d ON c.id_siswa = d.id
                                WHERE c.id_kelas = ".$pc_bawa[1]." AND b.id_mapel = ".$pc_bawa[0]."
                                AND a.tasm = '".$this->d['tasm']."'
                                ORDER BY d.id")->result_array();
        */
                                /*
        $q_nilai_harian = $this->db->query("SELECT 
                                m_siswa.nama nmsiswa, t_nilai.jenis, t_nilai.id_mapel_kd, t_nilai.id_siswa, t_nilai.nilai
                                FROM t_nilai 
                                INNER JOIN t_guru_mapel ON t_nilai.id_guru_mapel = t_guru_mapel.id
                                INNER JOIN t_kelas_siswa ON t_nilai.id_siswa = t_kelas_siswa.id_siswa
                                INNER JOIN m_siswa ON t_nilai.id_siswa = m_siswa.id
                                WHERE t_nilai.tasm = '".$this->d['tasm']."' 
                                AND t_guru_mapel.id_mapel = ".$pc_bawa[0]."
                                AND t_kelas_siswa.id_kelas = ".$pc_bawa[1]."
                                AND t_nilai.id_guru_mapel = '".$id_guru_mapel."'
                                ORDER BY m_siswa.id ASC")->result_array();
        //echo $this->db->last_query();
        //exit;

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
                                /*
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
                        // /$html .= '<td>'.var_dump($dn).'</td>';
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
        */

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
                                         WHERE b.id = ".$pc_bawa[0]." AND c.id = ".$pc_bawa[1]." AND a.tasm = '".$this->d['tasm']."'")->row_array();
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
        $q_siswa_kelas = $this->db->query("SELECT a.id_siswa, b.nama nmsiswa FROM t_kelas_siswa a LEFT JOIN m_siswa b ON a.id_siswa = b.id WHERE a.id_kelas = '".$pc_bawa[1]."' AND a.ta = '".$this->d['tahun']."'")->result_array();

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

        $kolom_akhir_kd = ($kolom-1);
        $kolom_uts = $kolom;
        $kolom_uas = ($kolom+1);
        
        $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', 'UTS');
        $kolom++;
        $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', 'UAS');
        $kolom++;


        $objPHPExcel->getActiveSheet()->getColumnDimension($this->kolom_xl[$kolom])->setVisible(FALSE);
        $kolom++;            
        foreach($d_kd as $k) {        
            //$objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', $kolom);
            if (!empty($this->kolom_xl[$kolom])) {
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

                $c_nh = $this->config->item('pnp_h');
                $c_nt = $this->config->item('pnp_t');
                $c_na = $this->config->item('pnp_a');
                $jml_pn = ($c_nh+$c_nt+$c_na);

                $rumus = "=round(((SUM(".$this->kolom_xl[$kolom_awal].$bds.":".$this->kolom_xl[$kolom_akhir_kd].$bds.")/".$jml_kd."*".($c_nh/$jml_pn).")+(".$this->kolom_xl[$kolom_uts].$bds."*".($c_nt/$jml_pn).")+(".$this->kolom_xl[$kolom_uas].$bds."*".($c_na/$jml_pn).")),0)";
                //echo $rumus."<br>";

                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $rumus);
                $klm++;

                $bds++;
                $no++;
                
            }
            $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[($klm-1)].'7', 'NP Akhir');
        }

        //exit;

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

        $nama_file = "NP_".str_replace(" ","",$detil_guru['nmkelas'])."_".str_replace(" ", "_", $detil_guru['nmmapel']).'_'.str_replace(" ", "_", $detil_guru['nmguru']).'.xlsx';
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$nama_file.'"');
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
        die();
    }

    public function upload($bawa) {
        $pc_bawa = explode("-", $bawa);
        
        $detil_guru = $this->db->query("SELECT 
                                         a.id, a.id_guru, b.nama nmmapel, c.nama nmkelas, d.nama nmguru
                                         FROM t_guru_mapel a
                                         INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                         INNER JOIN m_kelas c ON a.id_kelas = c.id
                                         INNER JOIN m_guru d ON a.id_guru = d.id 
                                         WHERE b.id = ".$pc_bawa[0]." AND c.id = ".$pc_bawa[1]."
                                         AND a.tasm = '".$this->d['tasm']."'")->row_array();
        

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

        $q_siswa_kelas = $this->db->query("SELECT a.id_siswa FROM t_kelas_siswa a WHERE a.id_kelas = '".$id_kelas."' AND a.ta = '".$this->d['tahun']."'");
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
                    $id_mapel_kd = !empty($pc_hide[1]) ? $pc_hide[1] : 0;
                    
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

            //j($arr_perdata);
            //exit;
            
            //echo $strq;
            //exit;

            $this->db->query($strq);

            //echo $strq;
            //exit;

            @unlink('./upload/temp/'.$tmp);
            
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
        
        $id_guru_mapel = $this->session->userdata('id_guru_mapel');

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
            //echo $this->db->last_query();
            //exit;
            
            $ambil_nilai = $this->db->query("SELECT 
                        t_nilai.id_siswa ids, m_siswa.nama, IFNULL(t_nilai.nilai,0) nilai
                        FROM t_nilai 
                        INNER JOIN t_guru_mapel ON t_nilai.id_guru_mapel = t_guru_mapel.id
                        INNER JOIN m_siswa ON t_nilai.id_siswa = m_siswa.id
                        WHERE t_guru_mapel.id = '$id_guru_mapel' 
                        AND t_nilai.id_mapel_kd = '$id_kd' 
                        AND t_nilai.jenis = '$jenis' 
                        AND t_nilai.tasm = '".$this->d['tasm']."'")->result_array();

            //echo $this->db->last_query();
            //exit;
        } else {
            /*
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
            */
            $ambil_nilai = $this->db->query("SELECT 
						t_nilai.id_siswa ids, m_siswa.nama, IFNULL(t_nilai.nilai,0) nilai
						FROM t_nilai 
						INNER JOIN t_guru_mapel ON t_nilai.id_guru_mapel = t_guru_mapel.id
						INNER JOIN m_siswa ON t_nilai.id_siswa = m_siswa.id
						WHERE t_guru_mapel.id = '$id_guru_mapel' AND t_nilai.jenis = '$jenis' AND 
						t_nilai.tasm = '".$this->d['tasm']."'")->result_array();
            //j($ambil_nilai);
            //exit;
            //echo $this->db->last_query();
            //exit;
        }
        
        //echo $this->db->last_query();
        //exit;
        
        if (empty($ambil_nilai)) {
            $list_data = $this->db->query("SELECT 
                                        b.id ids, b.nama, 0 nilai
                                        FROM t_kelas_siswa a 
                                        INNER JOIN m_siswa b ON a.id_siswa = b.id
                                        WHERE a.id_kelas = $kelas 
                                        AND a.ta = '".$this->d['tahun']."'
                                        ORDER BY b.nama ASC")->result_array();
            $d['sik_endi'] = "belum ada";
        } else {
            $list_data = $ambil_nilai;
            $d['sik_endi'] = "sudah ada";
        }

        //echo $this->db->last_query();

        $d['id_guru_mapel'] = $id_guru_mapel;
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
                                    
        //echo $this->db->last_query();
        //exit;
        $this->d['id_guru_mapel'] = $id;

        $this->session->set_userdata("id_guru_mapel", $id);


    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

    public function list_kd($id) {
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

        j($this->d['list_kd']);
        exit;
    }
}
