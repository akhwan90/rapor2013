<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class N_keterampilan extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("guru");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['url'] = "n_keterampilan";
        $this->d['idnya'] = "setmapel";
        $this->d['nama_form'] = "f_setmapel";

        $this->kolom_xl = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    }
    public function index($id) {

        $this->session->set_userdata("id_guru_mapel", $id);
        
        $this->d['detil_mp'] = $this->db->query("SELECT 
                                        a.*, b.nama nmmapel, c.nama nmkelas, c.tingkat tingkat
                                        FROM t_guru_mapel a
                                        INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                        INNER JOIN m_kelas c ON a.id_kelas = c.id 
                                        WHERE a.id  = '$id'")->row_array();
        $this->d['list_kd'] = $this->db->query("SELECT * FROM t_mapel_kd 
                                    WHERE id_mapel = '".$this->d['detil_mp']['id_mapel']."'
                                    AND tingkat = '".$this->d['detil_mp']['tingkat']."'
                                    AND semester = '".$this->d['c']['ta_semester']."'
                                    AND jenis = 'K'")->result_array();
        $this->d['id_guru_mapel'] = $id;
        $this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }



    public function cek() {
        $this->load->model('n_keterampilan_model', 'nkm');

        j($this->nkm->gen_nilai(4));
        exit;
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

        $this->load->model('n_keterampilan_model', 'nkm');
        $get_nilai = $this->nkm->gen_nilai($id_guru_mapel);
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
                    $nilai_uh = empty($dn[$id_kd]) ? '' : $dn[$id_kd];
                    $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $nilai_uh);
                    $klm++;
                }

                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $id_siswa);
                $klm++;
                
                foreach ($get_nilai['data_kd'] as $k => $v) {
                    $id_siswa = $id_siswa;
                    $id_kd = $k;
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, "h-".$id_kd);                        
                    $klm++;
                }

                $c_nh = $this->config->item('pnp_h');
                $jml_pn = ($c_nh);

                $rumus = "=round(((SUM(".$this->kolom_xl[$kolom_awal].$bds.":".$this->kolom_xl[$kolom_akhir_kd].$bds.")/".$jml_kd."*".($c_nh/$jml_pn).")),0)";
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

                $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, $id_siswa);
                $klm++;
                
                foreach ($get_nilai['data_kd'] as $id_kd => $dt_kd) {                    
                    $objPHPExcel->getActiveSheet()->setCellValue($this->kolom_xl[$klm].$bds, "h-".$id_kd);
                    $klm++;
                }

                $c_nh = $this->config->item('pnp_h');
                $jml_pn = ($c_nh);

                $rumus = "=round(((SUM(".$this->kolom_xl[$kolom_awal].$bds.":".$this->kolom_xl[$kolom_akhir_kd].$bds.")/".$jml_kd."*".($c_nh/$jml_pn).")),0)";

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

        $nama_file = "NK_".str_replace(" ","",$get_nilai['meta']['nmkelas'])."_".str_replace(" ", "_", $get_nilai['meta']['nmmapel']).'_'.str_replace(" ", "_", $get_nilai['meta']['nmguru']).'.xlsx';
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

            $idx_kolom_id_siswa = $idx_kolom_mulai + $j_list_kd;
            $idx_kolom_hide = $idx_kolom_mulai + $j_list_kd + 1;

            $jumlah = 0;
            $tambah = 0;
            $edit = 0;

            for ($b = $idx_baris_mulai; $b < $idx_baris_selesai; $b++) {
                $id_siswa = $_sheet->getCell($this->kolom_xl[$idx_kolom_id_siswa].$b)->getCalculatedValue();

                for ($k = $idx_kolom_hide; $k < ($idx_kolom_hide + $j_list_kd); $k++) {
                    // kolom nilai yg asli
                    $kolom_nilai = ($k - ($j_list_kd + 1));
                    $nilai = $_sheet->getCell($this->kolom_xl[$kolom_nilai].$b)->getCalculatedValue();
                    // kolom properties nilai yg di hiden
                    $hide = $_sheet->getCell($this->kolom_xl[$k].$b)->getCalculatedValue();
                    
                    $pc_hide = explode("-", $hide);
                    $id_mapel_kd = !empty($pc_hide[1]) ? $pc_hide[1] : 0;

                    $this->db->where('tasm', $tasm);
                    $this->db->where('id_guru_mapel', $id_guru_mapel);
                    $this->db->where('id_mapel_kd', $id_mapel_kd);
                    $this->db->where('id_siswa', $id_siswa);
                    $this->db->select('id');
                    $cek_sudah_ada = $this->db->get('t_nilai_ket')->num_rows();

                    if ($cek_sudah_ada > 0) {
                        $edit++;
                        $this->db->where('tasm', $tasm);
                        $this->db->where('id_guru_mapel', $id_guru_mapel);
                        $this->db->where('id_mapel_kd', $id_mapel_kd);
                        $this->db->where('id_siswa', $id_siswa);
                        $this->db->update('t_nilai_ket', ['nilai'=>$nilai]);
                    } else {
                        $tambah++;
                        $this->db->insert('t_nilai_ket', [
                                "tasm"=>$tasm, 
                                "id_guru_mapel"=>$id_guru_mapel, 
                                "id_mapel_kd"=>$id_mapel_kd, 
                                "id_siswa"=>$id_siswa, 
                                "nilai"=>$nilai
                            ]
                        );
                    }
                } 
            }


            $this->session->set_flashdata('k', '<div class="alert alert-success">Nilai berhasil diupload. Edit: '.$edit.', Insert: '.$tambah.'</div>');
            redirect('n_keterampilan/index/'.$id_guru_mapel);

        } 
    }

    public function cetak($bawa) {
        $this->load->model('n_keterampilan_model', 'nkm');
        $get_nilai = $this->nkm->gen_nilai($bawa);
        $jml_kd = count($get_nilai['data_kd']);

        $html = '<p align="left"><b>REKAP NILAI KETERAMPILAN</b>
                <br>
                Mata Pelajaran : '.$get_nilai['meta']['nmmapel'].', Kelas : '.$get_nilai['meta']['nmkelas'].', Guru : '.$get_nilai['meta']['nmguru'].'. Tahun Pelajaran '.$get_nilai['meta']['tasm'].'<hr style="border: solid 1px #000; margin-top: -10px"></p>';

        $html .= '<table class="table"><tr><td rowspan="2">Nama</td><td colspan="'.$jml_kd.'">NH</td><td rowspan="2">Rata-rata NH / Nilai Akhir</td></tr><tr>';
        foreach ($get_nilai['data_kd'] as $k) {
            $html .= '<td>KD '.$k['nama'].'</td>';
        }
        $html .= '</tr>';

        foreach ($get_nilai['data_siswa'] as $id_siswa => $s) {
            $html .= '<tr><td>'.$s['nama'].'</td>';
            $jml_nilai_kd = 0;
            foreach ($get_nilai['data_kd'] as $k) {
                $id_mapel_kd = $k['id'];
                $nilai_kd = !empty($get_nilai['data_np'][$id_siswa][$id_mapel_kd]) ? number_format($get_nilai['data_np'][$id_siswa][$id_mapel_kd]) : 0;
                $jml_nilai_kd += $nilai_kd;

                $html .= '<td>'.$nilai_kd.'</td>';
            }
            if ($jml_kd > 0) {
                $rata_rata_nilai_kd = number_format($jml_nilai_kd / $jml_kd);
            } else {
                $rata_rata_nilai_kd = 0;
            }
            $html .= '<td>'.$rata_rata_nilai_kd.'</td></tr>';

        }

        $html .= '</table>';


        $this->d['html'] = $html;
        $this->load->view('cetak', $this->d);
    }

    public function ambil_siswa($kelas) {
        $id_kd = $this->uri->segment(4);
        $list_data = array();
        $ambil_nilai = $this->db->query("SELECT
                                        b.id ids, 
                                        b.nama nama, 
                                        IFNULL(a.nilai,0) nilai
                                        FROM m_siswa b 
                                        INNER JOIN t_nilai_ket a ON a.id_siswa = b.id
                                        INNER JOIN t_guru_mapel c ON a.id_guru_mapel = c.id
                                        INNER JOIN t_mapel_kd d ON a.id_mapel_kd = d.id 
                                        INNER JOIN m_guru e ON c.id_guru = e.id 
                                        INNER JOIN m_mapel f ON c.id_mapel = f.id
                                        INNER JOIN t_kelas_siswa g ON a.id_siswa = g.id_siswa
                                        INNER JOIN m_kelas h ON g.id_kelas = h.id
                                        WHERE h.id = $kelas AND a.id_mapel_kd = $id_kd 
                                        AND a.tasm = '".$this->d['c']['ta_tasm']."'
                                        ORDER BY b.nama
                                        ")->result_array();

        if (empty($ambil_nilai)) {
            $list_data = $this->db->query("SELECT 
                                        b.id ids, b.nama, 0 nilai
                                        FROM t_kelas_siswa a 
                                        INNER JOIN m_siswa b ON a.id_siswa = b.id
                                        WHERE a.id_kelas = $kelas
                                        AND a.ta = '".$this->d['c']['ta_tahun']."'
                                        ORDER BY b.nama")->result_array();
        } else {
            $list_data = $ambil_nilai;
        }
        
        // echo $this->db->last_query();
        // exit;
        
        $d['status'] = "ok";
        $d['data'] = $list_data;
        j($d);
    }
    public function simpan_nilai() {
        $p = $this->input->post();
        $tambah = 0;
        $edit = 0;
        $i = 0;
        foreach ($p['nilai'] as $s) {
            $cek = $this->db->query("SELECT id FROM t_nilai_ket WHERE tasm = '".$this->d['c']['ta_tasm']."' AND id_guru_mapel = '".$p['id_guru_mapel']."' AND id_mapel_kd = '".$p['id_mapel_kd']."' AND id_siswa = '".$p['id_siswa'][$i]."'")->num_rows();

            if ($cek > 0) {
                $edit++;
                $this->db->query("UPDATE t_nilai_ket SET nilai = '$s' WHERE tasm = '".$this->d['c']['ta_tasm']."' AND id_guru_mapel = '".$p['id_guru_mapel']."' AND id_mapel_kd = '".$p['id_mapel_kd']."' AND id_siswa = '".$p['id_siswa'][$i]."'");
            } else {
                $tambah++;
                $this->db->query("INSERT INTO t_nilai_ket (tasm,id_guru_mapel, id_mapel_kd, id_siswa, nilai) VALUES ('".$this->d['c']['ta_tasm']."', '".$p['id_guru_mapel']."', '".$p['id_mapel_kd']."', '".$p['id_siswa'][$i]."', '".$s."')");
            }
            $i++;
        }
        
        $d['status'] = "ok";
        $d['data'] = $i." Data berhasil disimpan. Tambah: ".$tambah.", Edit: ".$edit;
        j($d);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM t_guru_mapel WHERE id = '$id'");
        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }
}
