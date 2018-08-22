<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Set_walikelas extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['url'] = "set_walikelas";
        $this->d['idnya'] = "setwalikelas";
        $this->d['nama_form'] = "f_setwalikelas";

        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = substr($get_tasm['tahun'],0,4);
    }

    public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT id FROM t_walikelas a
                                        WHERE a.tasm = '".$this->d['tasm']."'
                                        ORDER BY id ASC")->num_rows();
    
        $q_datanya = $this->db->query("SELECT a.id, b.nama nmguru, c.nama nmkelas
                                    FROM t_walikelas a
                                    INNER JOIN m_guru b ON a.id_guru = b.id
                                    INNER JOIN m_kelas c ON a.id_kelas = c.id
                                    WHERE (a.tasm = '".$this->d['tasm']."') AND (
                                    b.nama LIKE '%".$search['value']."%'
                                    OR c.nama LIKE '%".$search['value']."%')
                                    ORDER BY a.id ASC
                                    LIMIT ".$start.", ".$length."")->result_array();
        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[0] = $no++;
            $data_ok[1] = $d['nmkelas'];
            $data_ok[2] = $d['nmguru'];

            $data_ok[3] = '<a href="#" onclick="return edit(\''.$d['id'].'\');" class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</a> 
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
        $q = $this->db->query("SELECT *, 'edit' AS mode FROM t_walikelas WHERE id = '$id'")->row_array();

        $d = array();
        $d['status'] = "ok";
        if (empty($q)) {
            $d['data']['id'] = "";
            $d['data']['mode'] = "add";
            $d['data']['id_guru'] = "";
            $d['data']['id_kelas'] = "";
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
            $cek = $this->db->query("SELECT id FROM t_walikelas WHERE id_kelas = '".$p['id_kelas']."' AND tasm = '".$this->d['tasm']."'")->num_rows();

            if ($cek > 0) {
                $d['status'] = "gagal";
                $d['data'] = "Kelas tersebut sudah ada walinya..";                
            } else {
                $this->db->query("INSERT INTO t_walikelas (tasm, id_guru, id_kelas) VALUES ('".$this->d['tasm']."', '".$p['id_guru']."', '".$p['id_kelas']."')");

                $d['status'] = "ok";
                $d['data'] = "Data berhasil disimpan";
            }
        } else if ($p['_mode'] == "edit") {
            $this->db->query("UPDATE t_walikelas SET id_kelas = '".$p['id_kelas']."', id_guru = '".$p['id_guru']."' WHERE id = '".$p['_id']."'");

            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan";
        } else {
            $d['status'] = "gagal";
            $d['data'] = "Kesalahan sistem";
        }

        j($d);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM t_walikelas WHERE id = '$id'");

        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }

    public function index() {
    	$this->d['p'] = "list";
        
        $this->d['p_kelas'] = array(""=>"Kelas");

        $q_kelas = $this->db->query("SELECT * FROM m_kelas")->result_array();
        if (!empty($q_kelas)) {
            foreach ($q_kelas as $k) {
                $this->d['p_kelas'][$k['id']] = $k['nama'];
            }
        }

        $this->d['p_guru'] = array(""=>"Guru");

        $q_guru = $this->db->query("SELECT * FROM m_guru")->result_array();
        if (!empty($q_guru)) {
            foreach ($q_guru as $g) {
                $this->d['p_guru'][$g['id']] = $g['nama'];
            }
        }

        $this->load->view("template_utama", $this->d);
    }

}