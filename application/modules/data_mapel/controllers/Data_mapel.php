<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_mapel extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['url'] = "data_mapel";
        $this->d['idnya'] = "datamapel";
        $this->d['nama_form'] = "f_datamapel";
    }

    public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT id FROM m_mapel")->num_rows();
    
        $q_datanya = $this->db->query("SELECT * FROM m_mapel WHERE nama LIKE '%".$search['value']."%' ORDER BY id DESC LIMIT ".$start.", ".$length."")->result_array();
        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[] = $no++;
            $data_ok[] = $d['kelompok'];
            $data_ok[] = $d['kd_singkat'];
            $data_ok[] = $d['nama'];
            //$data_ok[3] = ($d['is_sikap'] == "0") ? '<i class="fa fa-minus-circle"></i>' : '<i class="fa fa-check-circle"></i>';

            $data_ok[] = '<a href="#" onclick="return edit(\''.$d['id'].'\');" class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</a> 
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
        $q = $this->db->query("SELECT *, 'edit' AS mode FROM m_mapel WHERE id = '$id'")->row_array();

        $d = array();
        $d['status'] = "ok";
        if (empty($q)) {
            $d['data']['id'] = "";
            $d['data']['mode'] = "add";
            //$d['data']['is_sikap'] = "";
            $d['data']['nama'] = "";
        } else {
            $d['data'] = $q;
        }

        j($d);
    }

    public function simpan() {
        $p = $this->input->post();

        $d['status'] = "";
        $d['data'] = "";

        if ($p['_mode'] == "add") {
            $this->db->query("INSERT INTO m_mapel (kelompok, nama, kd_singkat) VALUES ('".$p['nama']."', '".$p['kode_singkat']."')");

            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan";
        } else if ($p['_mode'] == "edit") {
            $this->db->query("UPDATE m_mapel SET kelompok = '".$p['kelompok']."', kd_singkat = '".$p['kd_singkat']."', nama = '".$p['nama']."' WHERE id = '".$p['_id']."'");

            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan";
        } else {
            $d['status'] = "gagal";
            $d['data'] = "Kesalahan sistem";
        }

        j($d);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM m_mapel WHERE id = '$id'");

        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }

    public function index() {
    	$this->d['p'] = "list";
        $this->d['p_kelompok'] = array("A"=>"Kelompok A","B"=>"Kelompok B");
        $this->d['p_nilaisikap'] = array("1"=>"Ada Nilai Sikap","0"=>"Tidak ada nilai sikap");

        $this->load->view("template_utama", $this->d);
    }

}