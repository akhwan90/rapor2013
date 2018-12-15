<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tahun extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['url'] = "tahun";
        $this->d['idnya'] = "dataguru";
        $this->d['nama_form'] = "f_dataguru";

        $akses = array("admin");

        if (!cek_hak_akses($this->d['admlevel'], $akses)) {
            redirect('unauthorized_access');
        }
        
    }

    public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT id FROM tahun")->num_rows();
    
        $qdata = $this->db->query("SELECT * FROM tahun WHERE tahun LIKE '%".$search['value']."%' ORDER BY tahun ASC LIMIT ".$start.", ".$length."");
        $q_datanya = $qdata->result_array();
        $j_datanya = $qdata->num_rows();

        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[] = $no++;
            $data_ok[] = $d['tahun'];
            $data_ok[] = $d['nama_kepsek']."<br>".$d['nip_kepsek'];
            $data_ok[] = $d['tgl_raport'];
            $data_ok[] = $d['tgl_raport_kelas3'];
            $data_ok[] = ($d['aktif'] == "Y") ? '<div class="label label-success">Aktif</div>' : '<div class="label label-danger">Tidak Aktif</div>';

            $data_ok[] = '<a href="#" onclick="return edit(\''.$d['id'].'\');" class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</a> 
                <a href="#" onclick="return aktifkan(\''.$d['id'].'\');" class="btn btn-xs btn-info"><i class="fa fa-star"></i> Aktifkan</a>';

            $data[] = $data_ok;
        }

        $json_data = array(
                    "draw" => $draw,
                    "iTotalRecords" => $j_datanya,
                    "iTotalDisplayRecords" => $d_total_row,
                    "data" => $data
                );
        j($json_data);
        exit;
    }

    public function edit($id) {
        $q = $this->db->query("SELECT *, 'edit' AS mode FROM tahun WHERE id = '$id'")->row_array();

        $d = array();
        $d['status'] = "ok";
        if (empty($q)) {
            $d['data']['id'] = "";
            $d['data']['mode'] = "add";
            $d['data']['tahun'] = "";
            $d['data']['semester'] = "";
            $d['data']['aktif'] = "";
            $d['data']['nama_kepsek'] = "";
            $d['data']['nip_kepsek'] = "";
            $d['data']['tgl_raport'] = "";
            $d['data']['tgl_raport_kelas3'] = "";
        } else {
            $tahun = substr($q['tahun'], 0, 4);
            $semester = substr($q['tahun'], 4,1);

            $d['data'] = $q;
            $d['data']['tahun'] = $tahun;
            $d['data']['semester'] = $semester;
        }

        j($d);
    }

    public function simpan() {
        $p = $this->input->post();

        $d['status'] = "";
        $d['data'] = "";

        if ($p['_mode'] == "add") {
            $cek_sudah_ada = $this->db->query("SELECT tahun FROM tahun WHERE tahun = '".$p['tahun'].$p['semester']."'")->num_rows();

            if ($cek_sudah_ada > 0) {
                $d['status'] = "gagal";
                $d['data'] = "Data '".$p['tahun'].$p['semester']."' ini sudah ada"; 
            } else {
                $this->db->query("INSERT INTO tahun (tahun, aktif, nama_kepsek, nip_kepsek, tgl_raport, tgl_raport_kelas3) VALUES ('".$p['tahun'].$p['semester']."','N','".$p['nama_kepsek']."', '".$p['nip_kepsek']."', '".$p['tgl_raport']."', '".$p['tgl_raport_kelas3']."')");            
                $d['status'] = "ok";
                $d['data'] = "Data berhasil disimpan";
            }
        } else if ($p['_mode'] == "edit") {
            $cek_sudah_ada = $this->db->query("SELECT tahun FROM tahun WHERE tahun = '".$p['tahun'].$p['semester']."'");
            $j_sdh_ada = $cek_sudah_ada->num_rows();
            $d_sdh_ada = $cek_sudah_ada->row_array();
            $cek_yg_ini = $this->db->query("SELECT tahun FROM tahun WHERE id = '".$p['_id']."'")->row_array();

            if ($j_sdh_ada > 0) {

                if ($cek_yg_ini['tahun'] == $p['tahun'].$p['semester']) {
                    $this->db->query("UPDATE tahun SET tahun = '".$p['tahun'].$p['semester']."', nama_kepsek = '".$p['nama_kepsek']."', nip_kepsek = '".$p['nip_kepsek']."', tgl_raport = '".$p['tgl_raport']."', tgl_raport_kelas3 = '".$p['tgl_raport_kelas3']."'  WHERE id = '".$p['_id']."'");

                    $d['status'] = "ok";
                    $d['data'] = "Data berhasil disimpan";
                } else {
                    $d['status'] = "gagal";
                    $d['data'] = "Data '".$p['tahun'].$p['semester']."' ini sudah ada"; 
                }

            } else {
                $this->db->query("UPDATE tahun SET tahun = '".$p['tahun'].$p['semester']."', nama_kepsek = '".$p['nama_kepsek']."', nip_kepsek = '".$p['nip_kepsek']."', tgl_raport = '".$p['tgl_raport']."', tgl_raport_kelas3 = '".$p['tgl_raport_kelas3']."'  WHERE id = '".$p['_id']."'");

                $d['status'] = "ok";
                $d['data'] = "Data berhasil disimpan";
            }
        } else {
            $d['status'] = "gagal";
            $d['data'] = "Kesalahan sistem";
        }

        j($d);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM tahun WHERE id = '$id'");

        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }

    public function aktifkan($id) {
        $get_tahun = $this->db->query("SELECT tahun FROM tahun WHERE id = '$id'")->row_array();

        $this->db->query("UPDATE tahun SET aktif = 'N'");
        $this->db->query("UPDATE tahun SET aktif = 'Y' WHERE id = '$id'");
        
        $d['status'] = "ok";
        $d['data'] = "Semester aktif adalah : ".$get_tahun['tahun'];

        j($d);
    }

    public function index() {
    	$this->d['p'] = "list";
        
        $this->d['p_semester'] = array("1"=>"Satu","2"=>"Dua");
        $this->d['p_tahun'] = array();

        for ($i = (date('Y') - 4); $i <= (date('Y') + 4); $i++) {
            $idx = $i;
            $this->d['p_tahun'][$idx] = $i;
        }

        $this->load->view("template_utama", $this->d);
    }

}
