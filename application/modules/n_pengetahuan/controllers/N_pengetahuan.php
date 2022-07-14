<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class N_pengetahuan extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("guru");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['url'] = "n_pengetahuan";
        $this->d['idnya'] = "setmapel";
        $this->d['nama_form'] = "f_setmapel";

        $this->kolom_xl = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $this->d['id_guru_mapel'] = 0;

    }

    public function cek() {
        $this->load->model('n_pengetahuan_model', 'npm');

        j($this->npm->gen_nilai(4));
        exit;
    }
    public function cetak($bawa) {
        $this->load->model('n_pengetahuan_model', 'npm');
        $get_nilai = $this->npm->gen_nilai($bawa);
        $jml_kd = count($get_nilai['data_kd']);

        $html = '<p align="left"><b>REKAP NILAI PENGETAHUAN</b>
                <br>
                Mata Pelajaran : '.$get_nilai['meta']['nmmapel'].', Kelas : '.$get_nilai['meta']['nmkelas'].', Guru : '.$get_nilai['meta']['nmguru'].'. Tahun Pelajaran '.$get_nilai['meta']['tasm'].'<hr style="border: solid 1px #000; margin-top: -10px"></p>';

        $html .= '<table class="table"><tr><td rowspan="2">Nama</td><td colspan="'.$jml_kd.'">NH</td><td rowspan="2">Rata-rata NH</td><td rowspan="2">UTS</td><td rowspan="2">UAS</td><td rowspan="2">Nilai Akhir</td></tr><tr>';
        foreach ($get_nilai['data_kd'] as $k) {
            $html .= '<td>KD '.$k['nama'].'</td>';
        }
        $html .= '</tr>';

        foreach ($get_nilai['data_siswa'] as $id_siswa => $s) {
            $idxs = $id_siswa;
            $html .= '<tr><td>'.$s['nama'].'</td>';
            $jml_nilai_kd = 0;
            foreach ($get_nilai['data_kd'] as $k) {
                $idxk = $k['id'];
                $nilai_kd = !empty($get_nilai['data_np'][$idxs]['h'][$idxk]) ? number_format($get_nilai['data_np'][$idxs]['h'][$idxk]) : 0;
                $jml_nilai_kd += $nilai_kd;

                $html .= '<td>'.$nilai_kd.'</td>';
            }
            if ($jml_kd > 0) {
                $rata_rata_nilai_kd = number_format($jml_nilai_kd / $jml_kd);
            } else {
                $rata_rata_nilai_kd = 0;
            }
            $nilai_uts = !empty($get_nilai['data_np'][$idxs]['t']) ? number_format($get_nilai['data_np'][$idxs]['t']) : 0;
            $nilai_uas = !empty($get_nilai['data_np'][$idxs]['a']) ? number_format($get_nilai['data_np'][$idxs]['a']) : 0;
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


        $this->d['html'] = $html;
        $this->load->view('cetak', $this->d);
    }

    public function export($id_guru_mapel) {
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

        $this->load->model('n_pengetahuan_model', 'npm');
        $get_nilai = $this->npm->gen_nilai($id_guru_mapel);
        $jml_kd = count($get_nilai['data_kd']);

        $id_mapel = $get_nilai['meta']['idmapel'];
        $nm_mapel = $get_nilai['meta']['nmmapel'];
        $id_guru = $get_nilai['meta']['idguru'];
        $nm_guru = $get_nilai['meta']['nmguru'];
        $id_kelas = $get_nilai['meta']['idkelas'];
        $nm_kelas = $get_nilai['meta']['nmkelas'];

        /// mulai
        $objPHPExcel->getActiveSheet()->setCellValue('A4', 'ID Mapel');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', $id_mapel);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', ": ".$nm_mapel);
        $objPHPExcel->getActiveSheet()->setCellValue('A5', 'ID Guru');
        $objPHPExcel->getActiveSheet()->setCellValue('B5', $id_guru);
        $objPHPExcel->getActiveSheet()->setCellValue('C5', ": ".$nm_guru);
        $objPHPExcel->getActiveSheet()->setCellValue('A6', 'ID Kelas');
        $objPHPExcel->getActiveSheet()->setCellValue('B6', $id_kelas);
        $objPHPExcel->getActiveSheet()->setCellValue('C6', ": ".$nm_kelas);

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
        foreach($get_nilai['data_kd'] as $k) {
            $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', "KD ".$k['nama']);
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
        foreach($get_nilai['data_kd'] as $k) {        
            //$objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', $kolom);
            if (!empty($this->kolom_xl[$kolom])) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($this->kolom_xl[$kolom])->setVisible(FALSE);
                $kolom++;
            }
        }
        
        //bds = baris mulai data nilai
        $bds = 8;

        if (!empty($get_nilai['data_np'])) {
            /* JIKA NILAI TIDAK KOSONG */
            $no = 1;
            foreach ($get_nilai['data_np'] as $id_siswa => $dn) {
                $nama = empty($get_nilai['data_siswa'][$id_siswa]['nama']) ? "" : $get_nilai['data_siswa'][$id_siswa]['nama'];
                
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$bds, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$bds, $nama);
                
                $klm = $kolom_awal;

                foreach($get_nilai['data_kd'] as $id_kd => $d_kd) {
                    $nilai_uh = empty($dn['h'][$id_kd]) ? '' : $dn['h'][$id_kd];
                    $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', $nilai_uh);
                    $klm++;
                }

                $n_uts  = empty($dn["t"]) ? '' : number_format($dn["t"]);
                $n_uas  = empty($dn["a"]) ? '' : number_format($dn["a"]);
                
                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $n_uts);
                $klm++;
                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $n_uas);
                $klm++;
                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $id_siswa);
                $klm++;
                
                foreach ($get_nilai['data_kd'] as $k => $v) {
                    $id_siswa = $id_siswa;
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
        } else {
            /* JIKA NILAI MASIH KOSONG */

            $no = 1;
            foreach ($get_nilai['data_siswa'] as $id_siswa => $dt_siswa) {              
                $nama = $dt_siswa['nama'];
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$bds, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$bds, $nama);
                
                $klm = $kolom_awal;

                foreach($get_nilai['data_kd'] as $id_kd => $d_kd) {
                    $nilai_uh = '';
                    $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$kolom].'7', $nilai_uh);
                    $klm++;
                }

                $n_uts  = '';
                $n_uas  = '';
                
                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $n_uts);
                $klm++;
                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $n_uas);
                $klm++;
                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $id_siswa);
                $klm++;
                
                foreach ($get_nilai['data_kd'] as $id_kd => $dt_kd) {                    
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

        $nama_file = "NP_".str_replace(" ","",$get_nilai['meta']['nmkelas'])."_".str_replace(" ", "_", $get_nilai['meta']['nmmapel']).'_'.str_replace(" ", "_", $get_nilai['meta']['nmguru']).'.xlsx';
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
        $this->db->where('id', $bawa);
        $get_guru_mapel = $this->db->get('t_guru_mapel')->row_array();
        
        $detil_guru = $this->db->query("SELECT 
                                         a.id, a.id_guru, b.nama nmmapel, c.nama nmkelas, d.nama nmguru
                                         FROM t_guru_mapel a
                                         INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                         INNER JOIN m_kelas c ON a.id_kelas = c.id
                                         INNER JOIN m_guru d ON a.id_guru = d.id 
                                         WHERE b.id = ".$get_guru_mapel['id_mapel']." AND c.id = ".$get_guru_mapel['id_kelas']."
                                         AND a.tasm = '".$this->d['c']['ta_tasm']."'")->row_array();
        

        $this->d['bawa'] = $detil_guru;
        $this->d['id_kelas'] = $get_guru_mapel['id_kelas'];
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
                                    WHERE id_mapel = '".$detil_mp['id_mapel']."'
                                    AND tingkat = '".$detil_mp['tingkat']."'
                                    AND jenis = 'P'
                                    AND semester = '".$this->d['c']['ta_semester']."'");
        $d_list_kd = $list_kd->result_array();
        $j_list_kd = $list_kd->num_rows();

        $q_siswa_kelas = $this->db->query("SELECT a.id_siswa FROM t_kelas_siswa a WHERE a.id_kelas = '".$id_kelas."' AND a.ta = '".$this->d['c']['ta_tahun']."'");
        $d_list_siswa = $q_siswa_kelas->result_array();        
        $j_list_siswa = $q_siswa_kelas->num_rows();        

        
        $config['upload_path']          = './upload/temp/';
        $config['allowed_types']        = 'xls|xlsx';
        $config['max_size']             = 1024;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('import_excel')) {
            echo json_encode($this->upload->display_errors());
        } else {
            $upload_data = $this->upload->data();
            $tmp = './upload/temp/'.$upload_data['file_name'];
            
            $this->load->library('excel');//Load library excelnya
            $read   = PHPExcel_IOFactory::createReaderForFile($tmp);
            $read->setReadDataOnly(true);
            $excel  = $read->load($tmp);

            $_sheet = $excel->setActiveSheetIndexByName('Worksheet'); 

            $x_id_mapel = $_sheet->getCell('B4')->getCalculatedValue();
            $x_id_guru = $_sheet->getCell('B5')->getCalculatedValue();
            $x_id_kelas = $_sheet->getCell('B6')->getCalculatedValue();

            //echo $x_id_mapel."/".$detil_mp['id_mapel']."-".$x_id_guru."/".$detil_mp['id_guru']."-".$x_id_kelas."/".$detil_mp['id_kelas'];

            if ($x_id_mapel != $detil_mp['id_mapel'] || $x_id_guru != $detil_mp['id_guru'] || $x_id_kelas != $detil_mp['id_kelas']) {
                echo "File Excel SALAH";
                exit;
            }

            $data = array();

            //var tetap
            $tasm = $this->d['c']['ta_tasm'];
            $id_guru_mapel = $id_guru_mapel;
            $id_mapel = $detil_mp['id_mapel'];


            $idx_kolom_mulai = 2;
            // $idx_kolom_selesai = ($idx_kolom_mulai + ((2*$j_list_kd) + 2)) - 1;
            $idx_baris_mulai = 8;
            $idx_baris_selesai = $idx_baris_mulai + $j_list_siswa;


            $idx_kolom_uts = $idx_kolom_mulai + $j_list_kd;
            $idx_kolom_uas = $idx_kolom_mulai + $j_list_kd + 1;
            $idx_kolom_id_siswa = $idx_kolom_mulai + $j_list_kd + 1 + 1;
            $idx_kolom_hide = $idx_kolom_mulai + $j_list_kd + 1 + 1 + 1;


            for ($b = $idx_baris_mulai; $b < $idx_baris_selesai; $b++) {
                $id_siswa = $_sheet->getCell($this->kolom_xl[$idx_kolom_id_siswa].$b)->getCalculatedValue();

                $xy_nilai_uts = $_sheet->getCell($this->kolom_xl[($idx_kolom_uts)].$b)->getCalculatedValue();
                $xy_nilai_uas = $_sheet->getCell($this->kolom_xl[($idx_kolom_uas)].$b)->getCalculatedValue();
                
                $data[] = array("tasm"=>$tasm, "jenis"=>"t", "id_guru_mapel"=>$id_guru_mapel, "id_mapel_kd"=>$id_mapel, "id_siswa"=>$id_siswa, "nilai"=>$xy_nilai_uts);
                $data[] = array("tasm"=>$tasm, "jenis"=>"a", "id_guru_mapel"=>$id_guru_mapel, "id_mapel_kd"=>$id_mapel, "id_siswa"=>$id_siswa, "nilai"=>$xy_nilai_uas);
                

                for ($k = $idx_kolom_hide; $k < ($idx_kolom_hide + $j_list_kd); $k++) {
                    // kolom nilai yg asli
                    $kolom_nilai = ($k - ($j_list_kd + 3));
                    $nilai = $_sheet->getCell($this->kolom_xl[$kolom_nilai].$b)->getCalculatedValue();
                    // kolom properties nilai yg di hiden
                    $hide = $_sheet->getCell($this->kolom_xl[$k].$b)->getCalculatedValue();
                    
                    $pc_hide = explode("-", $hide);
                    $id_mapel_kd = !empty($pc_hide[1]) ? $pc_hide[1] : 0;
                    
                    $data[] = array("tasm"=>$tasm, "jenis"=>"h", "id_guru_mapel"=>$id_guru_mapel, "id_mapel_kd"=>$id_mapel_kd, "id_siswa"=>$id_siswa, "nilai"=>$nilai);
                } 
            }


            $strq = "REPLACE INTO t_nilai (tasm, jenis, id_guru_mapel, id_mapel_kd, id_siswa, nilai) VALUES ";
            $arr_perdata = array();
            foreach ($data as $d) {
                $arr_perdata[] = "('".$d['tasm']."', '".$d['jenis']."', '".$d['id_guru_mapel']."', '".$d['id_mapel_kd']."', '".$d['id_siswa']."', '".$d['nilai']."')";
            }
            $strq .= implode(",", $arr_perdata).";";


            $this->db->query($strq);

            $this->session->set_flashdata('k', '<div class="alert alert-success">Nilai berhasil diupload..</div>');
            redirect('n_pengetahuan/index/'.$id_guru_mapel);

        } 
    }

    public function ambil_siswa($kelas, $id_kd, $jenis) {
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
                                        AND a.tasm = '".$this->d['c']['ta_tasm']."'
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
                        AND t_nilai.tasm = '".$this->d['c']['ta_tasm']."'")->result_array();

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
                                        AND a.tasm = '".$this->d['c']['ta_tasm']."'
                                        ORDER BY b.nama ASC")->result_array();
            */
            $ambil_nilai = $this->db->query("SELECT 
						t_nilai.id_siswa ids, m_siswa.nama, IFNULL(t_nilai.nilai,0) nilai
						FROM t_nilai 
						INNER JOIN t_guru_mapel ON t_nilai.id_guru_mapel = t_guru_mapel.id
						INNER JOIN m_siswa ON t_nilai.id_siswa = m_siswa.id
						WHERE t_guru_mapel.id = '$id_guru_mapel' AND t_nilai.jenis = '$jenis' AND 
						t_nilai.tasm = '".$this->d['c']['ta_tasm']."'")->result_array();
            //j($ambil_nilai);
            //exit;
            //echo $this->db->last_query();
            //exit;
        }
        
        //echo $this->db->last_query();
        //exit;
        
        if (empty($ambil_nilai)) {
            $this->db->where('a.id_kelas', $kelas);
            $this->db->where('a.ta', $this->d['c']['ta_tahun']);
            $this->db->order_by('b.nama', 'asc');
            $this->db->join('m_siswa b', 'a.id_siswa = b.id');
            $this->db->select('b.id ids, b.nama, 0 nilai');
            $list_data = $this->db->get('t_kelas_siswa a')->result_array();
        } else {
            $list_data = $ambil_nilai;
        }


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
        $tambah = 0;
        $edit = 0;
        foreach ($p['nilai'] as $s) {
            $this->db->where('tasm', $this->d['c']['ta_tasm']);
            $this->db->where('jenis', $p['jenis']);
            $this->db->where('id_guru_mapel', $p['id_guru_mapel']);
            $this->db->where('id_mapel_kd', $p['id_mapel_kd']);
            $this->db->where('id_siswa', $p['id_siswa'][$i]);
            $this->db->select('id');
            $cek = $this->db->get('t_nilai')->num_rows();

            if ($cek > 0) {
                $jumlah_sudah ++;
                $edit++;

                $this->db->where('tasm', $this->d['c']['ta_tasm']);
                $this->db->where('jenis', $p['jenis']);
                $this->db->where('id_guru_mapel', $p['id_guru_mapel']);
                $this->db->where('id_mapel_kd', $p['id_mapel_kd']);
                $this->db->where('id_siswa', $p['id_siswa'][$i]);
                $this->db->update('t_nilai', ['nilai'=>$s]);
            } else {
                $tambah++;
                $pdata = [
                    'tasm'=>$this->d['c']['ta_tasm'],
                    'jenis'=>$p['jenis'],
                    'id_guru_mapel'=>$p['id_guru_mapel'],
                    'id_mapel_kd'=>$p['id_mapel_kd'],
                    'id_siswa'=>$p['id_siswa'][$i],
                    'nilai'=>$s,
                ];
                $this->db->insert('t_nilai', $pdata);
            }
            $i++;
        }
        
        $d['status'] = "ok";
        $d['data'] = $i." Data berhasil disimpan: ".$tambah." tambah, ".$edit." edit";
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
                                    WHERE 
                                    id_mapel = '".$this->d['detil_mp']['id_mapel']."'
                                    AND tingkat = '".$this->d['detil_mp']['tingkat']."'
                                    AND semester = '".$this->d['c']['ta_semester']."'
                                    AND jenis = 'P'")->result_array();
                                    
        //echo $this->db->last_query();
        //exit;
        $this->d['id_guru_mapel'] = $id;

        $this->session->set_userdata("id_guru_mapel", $id);


    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

    public function list_kd($id) {
        $this->db->where('a.id', $id);
        $this->db->join('m_mapel b', 'a.id_mapel = b.id');
        $this->db->join('m_kelas c', 'a.id_kelas = c.id');
        $this->db->select('a.*, b.nama nmmapel, c.nama nmkelas, c.tingkat tingkat');
        $this->d['detil_mp'] = $this->db->get('t_guru_mapel a')->row_array();

        $this->db->where('id_mapel', $this->d['detil_mp']['id_mapel']);
        $this->db->where('tingkat', $this->d['detil_mp']['tingkat']);
        $this->db->where('semester', $this->d['c']['ta_semester']);
        $this->db->where('jenis', 'P');
        $this->d['list_kd'] = $this->db->get('t_mapel_kd')->result_array();

        j($this->d['list_kd']);
        exit;
    }
}
