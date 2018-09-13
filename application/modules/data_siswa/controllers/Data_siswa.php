<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_siswa extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['url'] = "data_siswa";
        $this->d['idnya'] = "datasiswa";
        $this->d['nama_form'] = "f_datasiswa";
    }

    public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT id FROM m_siswa WHERE nama LIKE '%".$search['value']."%' AND stat_data = 'A'")->num_rows();
    
        $q_datanya = $this->db->query("SELECT a.*,
                                        (SELECT COUNT(id) FROM m_admin WHERE level = 'siswa' AND konid = a.id) AS jml_aktif
                                        FROM m_siswa a
                                        WHERE a.nama LIKE '%".$search['value']."%' AND stat_data = 'A' 
                                        ORDER BY a.nis ASC 
                                        LIMIT ".$start.", ".$length."")->result_array();
        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[0] = $no++;
            $data_ok[1] = $d['nis'];
            $data_ok[2] = strtoupper($d['nama']);

            $link_aktif_user = $d['jml_aktif'] > 0 ? '' : '<a href="#" onclick="return aktifkan(\''.$d['id'].'\');" class="btn btn-xs btn-info"><i class="fa fa-user"></i> Aktifkan User</a>';

            $data_ok[3] = '<a href="'.base_url().$this->d['url'].'/edit/'.$d['id'].'" class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</a>
                '.$link_aktif_user.'
                <a href="#" onclick="return hapus(\''.$d['id'].'\');" class="btn btn-xs btn-danger"><i class="fa fa-remove"></i> Hapus</a> ';

            $data[] = $data_ok;
        }

        $json_data = array(
                    "draw" => $draw,
                    "iTotalRecords" => $d_total_row,
                    "iTotalDisplayRecords" => $d_total_row,
                    "data" => $data
                );
        j($json_data);
        exit;
    }

    public function edit($id) {
        $q = $this->db->query("SELECT *, 'edit' AS mode FROM m_siswa WHERE id = '$id'")->row_array();
        $this->d['p_jk']  = array(""=>"JK","L"=>"Laki-laki","P"=>"Perempuan");
        $this->d['p_agama']  = array(""=>"Agama","Islam"=>"Islam","Katolik"=>"Katolik","Kristen"=>"Kristen","Hindu"=>"Hindu","Budha"=>"Budha","Konghucu"=>"Konghucu");
        $this->d['p_status_anak']  = array(""=>"Status Anak","AK"=>"Anak Kandung","Anak Tiri"=>"Anak Tiri");
        $this->d['p_diterima_kelas']  = array(""=>"Diterima Kelas","VII"=>"VII","VIII"=>"VIII", "IX"=>"IX");
        
        if (empty($q)) {
            $this->d['data']['id'] = "";
            $this->d['data']['mode'] = "add";
            $this->d['data']['nis'] = "";
            $this->d['data']['nisn'] = "";
            $this->d['data']['nama'] = "";
            $this->d['data']['jk'] = "";
            $this->d['data']['tmp_lahir'] = "";
            $this->d['data']['tgl_lahir'] = "";
            $this->d['data']['agama'] = "";
            $this->d['data']['status'] = "";
            $this->d['data']['anakke'] = "";
            $this->d['data']['alamat'] = "";
            $this->d['data']['notelp'] = "";
            $this->d['data']['sek_asal'] = "";
            $this->d['data']['sek_asal_alamat'] = "";
            $this->d['data']['diterima_kelas'] = "";
            $this->d['data']['diterima_tgl'] = "";
            $this->d['data']['diterima_smt'] = "";
            $this->d['data']['ijazah_no'] = "";
            $this->d['data']['ijazah_thn'] = "";
            $this->d['data']['skhun_no'] = "";
            $this->d['data']['skhun_thn'] = "";
            $this->d['data']['ortu_ayah'] = "";
            $this->d['data']['ortu_ibu'] = "";
            $this->d['data']['ortu_alamat'] = "";
            $this->d['data']['ortu_notelp'] = "";
            $this->d['data']['ortu_ayah_pkj'] = "";
            $this->d['data']['ortu_ibu_pkj'] = "";
            $this->d['data']['wali'] = "";
            $this->d['data']['wali_alamat'] = "";
            $this->d['data']['notelp_rumah'] = "";
            $this->d['data']['wali_pkj'] = "";
            $this->d['data']['stat_data'] = "";
            $this->d['data']['foto'] = "";
        } else {
            $this->d['data'] = $q;
        }


        $this->d['p'] = "form";
        $this->load->view("template_utama", $this->d);
    }

    public function simpan() {
        $p = $this->input->post();

        $data['nis'] = $p['nis'];
        $data['nisn'] = $p['nisn'];
        $data['nama'] = addslashes($p['nama']);
        $data['jk'] = $p['jk'];
        $data['tmp_lahir'] = $p['tmp_lahir'];
        $data['tgl_lahir'] = $p['tgl_lahir'];
        $data['agama'] = $p['agama'];
        $data['status'] = $p['status'];
        $data['anakke'] = $p['anakke'];
        $data['alamat'] = $p['alamat'];
        $data['notelp'] = $p['notelp'];
        $data['sek_asal'] = $p['sek_asal'];
        $data['sek_asal_alamat'] = $p['sek_asal_alamat'];
        $data['diterima_kelas'] = $p['diterima_kelas'];
        $data['diterima_tgl'] = $p['diterima_tgl'];
        $data['diterima_smt'] = $p['diterima_kelas'];
        $data['ijazah_no'] = $p['ijazah_no'];
        $data['ijazah_thn'] = $p['ijazah_thn'];
        $data['skhun_no'] = $p['skhun_no'];
        $data['skhun_no'] = $p['skhun_no'];
        $data['skhun_thn'] = $p['skhun_thn'];
        $data['ortu_ayah'] = $p['ortu_ayah'];
        $data['ortu_ibu'] = $p['ortu_ibu'];
        $data['ortu_alamat'] = $p['ortu_alamat'];
        $data['ortu_notelp'] = $p['ortu_notelp'];
        $data['ortu_ayah_pkj'] = $p['ortu_ayah_pkj'];
        $data['ortu_ibu_pkj'] = $p['ortu_ibu_pkj'];
        $data['wali'] = $p['wali'];
        $data['wali_alamat'] = $p['wali_alamat'];
        $data['notelp_rumah'] = $p['notelp_rumah'];
        $data['wali_pkj'] = $p['wali_pkj'];

        //upload config 
        $config['upload_path']      = './upload/foto_siswa';
        $config['allowed_types']    = 'jpg';
        $config['max_size']         = '2000';
        $config['max_width']        = '1000';
        $config['max_height']       = '1000';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('userfile')) {
            $this->session->set_flashdata('ue', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>');
        } else {
            $ud = $this->upload->data();
            $data['foto'] = $ud['file_name'];
        }


        if ($p['_mode'] == "add") {
            $this->db->insert('m_siswa', $data);
        } else if ($p['_mode'] == "edit") {
            $this->db->where('id', $p['_id']);
            $this->db->update('m_siswa', $data);
        } else {
            echo "kesalahan sistem";
            exit;
        }

        redirect($this->d['url']);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM m_siswa WHERE id = '$id'");
        redirect($this->d['url']);
    }


    public function aktifkan($id) {

        $detil_data = $this->db->query("SELECT nis, nama FROM m_siswa WHERE id = '".$id."'")->row_array();

        if (empty($detil_data)) {
            $d['status'] = "gagal";
            $d['data'] = "Terjadi kesalahan sistem..";
        } else {
            $username = $detil_data['nis'];
            $password = sha1(sha1($username));

            $this->db->query("INSERT INTO m_admin (username,password,level,konid,aktif) VALUES ('".$username."', '".$password."', 'siswa', '$id', 'Y')");

            $d['status'] = "ok";
            $d['data'] = "Username : ".$username." berhasil diaktifkan..! Password default : ".$username."";
        }
        
        j($d);
    }

    public function upload() {
        $this->d['p'] = "form_import";
        $this->load->view("template_utama", $this->d);
    }

    public function import_siswa() {
        $target_file = './upload/temp/';
        move_uploaded_file($_FILES["import_excel"]["tmp_name"], $target_file."import_siswa.xls");

        $file   = explode('.',$_FILES['import_excel']['name']);
        $length = count($file);

        if($file[$length -1] == 'xlsx' || $file[$length -1] == 'xls') {
            //jagain barangkali uploadnya selain file excel
            $tmp    = './upload/temp/import_siswa.xls';
            //Baca dari tmp folder jadi file ga perlu jadi sampah di server :-p
            
            $this->load->library('excel');//Load library excelnya
            $read   = PHPExcel_IOFactory::createReaderForFile($tmp);
            $read->setReadDataOnly(true);
            $excel  = $read->load($tmp);

            //echo $tmp;
    
            $_sheet = $excel->setActiveSheetIndexByName('data_siswa');            


            $data = array();

            $no = 1;
            for ($b = 2; $b < 500; $b++) {
                $nis = $_sheet->getCell('A'.$b)->getCalculatedValue();
                $nisn = $_sheet->getCell('B'.$b)->getCalculatedValue();
                $nama = addslashes($_sheet->getCell('C'.$b)->getCalculatedValue());
                $jk = $_sheet->getCell('D'.$b)->getCalculatedValue();
                $tmp_lahir = $_sheet->getCell('E'.$b)->getCalculatedValue();
                $tgl_lahir = $_sheet->getCell('F'.$b)->getCalculatedValue();
                $agama = $_sheet->getCell('G'.$b)->getCalculatedValue();
                $status = $_sheet->getCell('H'.$b)->getCalculatedValue();
                $anakke = $_sheet->getCell('I'.$b)->getCalculatedValue();
                $alamat = $_sheet->getCell('J'.$b)->getCalculatedValue();
                $notelp = $_sheet->getCell('K'.$b)->getCalculatedValue();
                $sek_asal = $_sheet->getCell('L'.$b)->getCalculatedValue();
                $sek_asal_alamat = $_sheet->getCell('M'.$b)->getCalculatedValue();
                $diterima_kelas = $_sheet->getCell('N'.$b)->getCalculatedValue();
                $diterima_tgl = $_sheet->getCell('O'.$b)->getCalculatedValue();
                $diterima_smt = $_sheet->getCell('P'.$b)->getCalculatedValue();
                $ijazah_no = $_sheet->getCell('Q'.$b)->getCalculatedValue();
                $ijazah_thn = $_sheet->getCell('R'.$b)->getCalculatedValue();
                $skhun_no = $_sheet->getCell('S'.$b)->getCalculatedValue();
                $skhun_thn = $_sheet->getCell('T'.$b)->getCalculatedValue();
                $ortu_ayah = $_sheet->getCell('U'.$b)->getCalculatedValue();
                $ortu_ibu = $_sheet->getCell('V'.$b)->getCalculatedValue();
                $ortu_alamat = $_sheet->getCell('W'.$b)->getCalculatedValue();
                $ortu_notelp = $_sheet->getCell('X'.$b)->getCalculatedValue();
                $ortu_ayah_pkj = $_sheet->getCell('Y'.$b)->getCalculatedValue();
                $ortu_ibu_pkj = $_sheet->getCell('Z'.$b)->getCalculatedValue();
                $wali = $_sheet->getCell('AA'.$b)->getCalculatedValue();
                $wali_alamat = $_sheet->getCell('AB'.$b)->getCalculatedValue();
                $notelp_rumah = $_sheet->getCell('AC'.$b)->getCalculatedValue();
                $wali_pkj = $_sheet->getCell('AD'.$b)->getCalculatedValue();

                if ($nis != "" || $nisn != "" || $nama != "") {
                    $data[] = "('$nis', '$nisn', '$nama', '$jk', '$tmp_lahir', '$tgl_lahir', '$agama', '$status', '$anakke', '$alamat', '$notelp', '$sek_asal', '$sek_asal_alamat', '$diterima_kelas', '$diterima_tgl', '$diterima_smt', '$ijazah_no', '$ijazah_thn', '$skhun_no', '$skhun_thn', '$ortu_ayah', '$ortu_ibu', '$ortu_alamat', '$ortu_notelp', '$ortu_ayah_pkj', '$ortu_ibu_pkj', '$wali', '$wali_alamat', '$notelp_rumah', '$wali_pkj')";
                    $no++;
                }
            }

            //exit;

            $strq = "INSERT INTO m_siswa (nis, nisn, nama, jk, tmp_lahir, tgl_lahir, agama, status, anakke, alamat, notelp, sek_asal, sek_asal_alamat, diterima_kelas, diterima_tgl, diterima_smt, ijazah_no, ijazah_thn, skhun_no, skhun_thn, ortu_ayah, ortu_ibu, ortu_alamat, ortu_notelp, ortu_ayah_pkj, ortu_ibu_pkj, wali, wali_alamat, notelp_rumah, wali_pkj) VALUES ";

            $strq .= implode(",", $data).";";
            
            $this->db->query($strq);

            @unlink('./upload/temp/import_siswa.xls');
            
            $this->session->set_flashdata('k', '<div class="alert alert-success">'.($no-1).' siswa berhasil diupload</div>');
            redirect('data_siswa/upload');

        } else {
            exit('Buka File Excel...');//pesan error tipe file tidak tepat
        }
        redirect('m_siswa/index/');
    }

    public function index() {
        $this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

}
