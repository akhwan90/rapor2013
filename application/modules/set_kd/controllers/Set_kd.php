<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Set_kd extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');


        $this->d['url'] = "set_kd";
        $this->d['idnya'] = "setkd";
        $this->d['nama_form'] = "f_kd";

        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['semester'] = substr($this->d['tasm'], -1, 1);
    }

    public function datatable($id) {

        $pc_id = explode("-", $id);


        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT 
                                        a.id, a.`no_kd`, a.`nama_kd`, a.jenis
                                        FROM t_mapel_kd a
                                        WHERE a.`id_guru` = '".$this->d['admkonid']."' AND a.`id_mapel` = '".$pc_id[0]."' 
                                        AND a.`tingkat` = '".$pc_id[1]."'")->num_rows();
    
        $q_datanya = $this->db->query("SELECT 
                                        a.id, a.`no_kd`, a.`nama_kd`, a.jenis
                                        FROM t_mapel_kd a
                                        WHERE (a.`id_guru` = '".$this->d['admkonid']."' AND a.`id_mapel` = '".$pc_id[0]."' 
                                        AND a.`tingkat` = '".$pc_id[1]."') 
                                        AND a.nama_kd LIKE '%".$search['value']."%' 
                                        ORDER BY a.jenis ASC
                                        LIMIT ".$start.", ".$length."")->result_array();
        

        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[0] = $no++;
            $data_ok[1] = $d['jenis'];
            $data_ok[2] = $d['no_kd'];
            $data_ok[3] = $d['nama_kd'];

            $data_ok[4] = '<a href="#" onclick="return edit(\''.$d['id'].'\');" class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</a> <a href="#" onclick="return hapus(\''.$d['id'].'\');" class="btn btn-xs btn-danger"><i class="fa fa-remove"></i> Hapus</a> ';

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
        $q = $this->db->query("SELECT *, 'edit' AS mode FROM t_mapel_kd WHERE id = '$id'")->row_array();

        $d = array();
        $d['status'] = "ok";

        if (empty($q)) {
            $d['data']['id'] = "";
            $d['data']['mode'] = "add";
            $d['data']['id_guru'] = "";
            $d['data']['id_mapel'] = "";
            $d['data']['tingkat'] = "";
            $d['data']['no_kd'] = "";
            $d['data']['jenis'] = "";
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


        if ($p['_mode'] == "add") {
            $this->db->query("INSERT INTO t_mapel_kd (id_guru, id_mapel, tingkat, no_kd, jenis, nama_kd, semester) VALUES ('".$this->d['admkonid']."', '".$p['id_mapel']."', '".$p['tingkat']."', '".$p['kode']."', '".$p['jenis']."', '".addslashes($p['nama'])."', '".$this->d['semester']."')");

            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan";
        } else if ($p['_mode'] == "edit") {
            $this->db->query("UPDATE t_mapel_kd SET id_mapel = '".$p['id_mapel']."', tingkat = '".$p['tingkat']."', no_kd = '".$p['kode']."', jenis = '".$p['jenis']."', nama_kd = '".addslashes($p['nama'])."' WHERE id = '".$p['_id']."'");

            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan";
        } else {
            $d['status'] = "gagal";
            $d['data'] = "Kesalahan sistem";
        }

        j($d);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM t_mapel_kd WHERE id = '$id'");
        $this->db->query("DELETE FROM t_nilai WHERE id_mapel_kd = '$id'");
        $this->db->query("DELETE FROM t_nilai_ket WHERE id_mapel_kd = '$id'");

        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }

    public function index() {
    	$this->d['mapel_diampu'] = $this->db->query("SELECT 
                                    b.id, b.nama, c.tingkat
                                    FROM t_guru_mapel a
                                    INNER JOIN m_mapel b ON a.`id_mapel` = b.`id`
                                    INNER JOIN m_kelas c ON a.`id_kelas` = c.`id`
                                    WHERE a.`id_guru` = '".$this->d['admkonid']."'
                                    GROUP BY a.`id_mapel`, c.`tingkat`")->result_array();

        $this->d['p_mapel_diampu'] = array();
        if (!empty($this->d['mapel_diampu'])) {
            foreach ($this->d['mapel_diampu'] as $v) {
                $key = $v['id']."-".$v['tingkat'];

                $this->d['p_mapel_diampu'][$key] = $v['nama']." Kelas ".$v['tingkat']; 
            }
        }
        $this->d['p_jenis'] = array(''=>'','P'=>'Pengetahuan','K'=>'Keterampilan');

        $this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

}