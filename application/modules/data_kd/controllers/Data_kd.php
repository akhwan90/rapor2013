<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class Data_kd extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("admin");
        cek_hak_akses($this->d['s']['level'], $akses);


        $this->d['url'] = "data_kd";
        $this->d['idnya'] = "datakd";
        $this->d['nama_form'] = "f_datakd";
        $this->d['title'] = "Data KD";
    
    }

    public function index() {
        $this->d['p'] = "list";
        
        $this->d['p_semester'] = array("1"=>"Semester 1","2"=>"Semester 2");
        $this->d['p_jenis'] = array("P"=>"Pengetahuan","K"=>"Keterampilan","SSp"=>"Sikap Spiritual","SSo"=>"Sikap Sosial");
        $this->d['p_mapel'] = array();

        $get_mapel = $this->db->get('m_mapel')->result_array();
        if (!empty($get_mapel)) {
            foreach ($get_mapel as $m) {
                $idx = $m['id'];
                $this->d['p_mapel'][$idx] = $m['nama'];
            }
        }

        $this->load->view("template_utama", $this->d);
    }

    public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        $jenis = $this->input->post('jenis');
        $mapel = $this->input->post('mapel');
        $tingkat = $this->input->post('tingkat');
        $semester = $this->input->post('semester');

        if ($jenis != "" && $mapel != "" && $tingkat != "" && $semester != "") {

            $this->db->where('id_mapel', $mapel);
            $this->db->where('tingkat', $tingkat);
            $this->db->where('semester', $semester);
            $this->db->where('jenis', $jenis);
            $d_total_row = $this->db->get("t_mapel_kd")->num_rows();


            $this->db->where('id_mapel', $mapel);
            $this->db->where('tingkat', $tingkat);
            $this->db->where('semester', $semester);
            $this->db->where('jenis', $jenis);
            $this->db->limit($length, $start);
            $qdata = $this->db->get("t_mapel_kd");

            $q_datanya = $qdata->result_array();
            $j_datanya = $qdata->num_rows();

            $data = array();
            $no = ($start+1);

            foreach ($q_datanya as $d) {
                $data_ok = array();
                $data_ok[] = $no++;
                $data_ok[] = $d['nama_kd'];

                $link = '<a href="#" onclick="return edit(\''.$d['id'].'\');" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i> Edit</a>';

                $data_ok[] = $link;

                $data[] = $data_ok;
            }

            $json_data = array(
                "draw" => $draw,
                "iTotalRecords" => $j_datanya,
                "iTotalDisplayRecords" => $d_total_row,
                "data" => $data
            );
        } else {
            $json_data = array(
                "draw" => $draw,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "data" => []
            );
        }

        j($json_data);
        exit;
    }

    public function edit($id) {
        $this->db->where('id', $id);
        $q = $this->db->get("t_mapel_kd")->row_array();

        $d = array();
        $d['status'] = "ok";
        if (empty($q)) {
            $d['data']['id'] = "";
            $d['data']['mode'] = "add";
            $d['data']['id_mapel'] = "";
            $d['data']['tingkat'] = "";
            $d['data']['semester'] = "";
            $d['data']['no_kd'] = "";
            $d['data']['jenis'] = "";
            $d['data']['nama_kd'] = "";
        } else {
            $d['data'] = $q;
        }

        j($d);
    }

    public function simpan() {
        $p = $this->input->post();

        $d['status'] = "";
        $d['data'] = "";


        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters("\n", "");

        $this->form_validation->set_rules('f_jenis', 'f_jenis', 'trim|required');
        $this->form_validation->set_rules('f_mapel', 'f_mapel', 'trim|required');
        $this->form_validation->set_rules('f_kelas', 'f_kelas', 'trim|required');
        $this->form_validation->set_rules('f_semester', 'f_semester', 'trim|required');
        $this->form_validation->set_rules('f_nomor_kd', 'f_nomor_kd', 'trim|required');
        $this->form_validation->set_rules('f_nama_kd', 'f_nama_kd', 'trim|required');

        $this->form_validation->set_message('required', 'Silakan input {field} !');

        if ($this->form_validation->run() == FALSE) {
            $d = ['status'=>'gagal', "data"=>validation_errors()];
        } else {
            $p_data = [
                'id_mapel'=>$p['f_mapel'],
                'tingkat'=>$p['f_kelas'],
                'semester'=>$p['f_semester'],
                'no_kd'=>$p['f_nomor_kd'],
                'jenis'=>$p['f_jenis'],
                'nama_kd'=>$p['f_nama_kd'],
            ];

            if ($p['_mode'] == "add") {
                $this->db->insert('t_mapel_kd', $p_data);

                $d['status'] = "ok";
                $d['data'] = "Data berhasil disimpan";
            } else if ($p['_mode'] == "edit") {
                $this->db->where('id', $p['_id']);
                $this->db->update('t_mapel_kd', $p_data);

                $d['status'] = "ok";
                $d['data'] = "Data berhasil disimpan";
            } else {
                $d['status'] = "gagal";
                $d['data'] = "Kesalahan sistem";
            }
        }

        j($d);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM m_guru WHERE id = '$id'");

        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }

    public function aktifkan($id) {

        $detil_data = $this->db->query("SELECT nama FROM m_guru WHERE id = '".$id."'")->row_array();

        if (empty($detil_data)) {
            $d['status'] = "gagal";
            $d['data'] = "Terjadi kesalahan sistem..";
        } else {
            $username = strtolower(str_replace(array(".",","," "), array("","",""), $detil_data['nama']));
            $password = sha1(sha1('guru123'));

            $username = substr($username, 0, 6);

            $cek_username = $this->db->query("SELECT * FROM m_admin WHERE username = '".$username."'");

            $jml_username = $cek_username->num_rows();
            $jika_sudah_ada = $jml_username > 0 ? $username."_".($jml_username++) : $username;
            $username_fix = $jika_sudah_ada;

            $this->db->query("INSERT INTO m_admin (username,password,level,konid,aktif) VALUES ('".$username_fix."', '".$password."', 'guru', '$id', 'Y')");

            $d['status'] = "ok";
            $d['data'] = "Username : ".$username_fix." berhasil diaktifkan..! Password default guru123";
        }
        
        j($d);
    }

    public function nonaktifkan($id) {

        $detil_data = $this->db->query("SELECT nama FROM m_guru WHERE id = '".$id."'")->row_array();

        if (empty($detil_data)) {
            $d['status'] = "gagal";
            $d['data'] = "Terjadi kesalahan sistem..";
        } else {
            $username = strtolower(str_replace(array(".",","," "), array("","",""), $detil_data['nama']));
            $password = sha1(sha1('guru123'));

            $this->db->query("DELETE FROM m_admin WHERE level = 'guru' AND konid = '$id'");

            $d['status'] = "ok";
            $d['data'] = "User dinonaktifkan..";
        }
        
        j($d);
    }

}