<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class Data_sekolah extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("admin");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['url'] = "data_ekstra";

    }

    public function simpan() {
        
        $p = $this->input->post();

        $this->db->where('id', 1);
        $this->db->update('m_sekolah', [
            'nama_sekolah'=>$p['nama_sekolah'],
            'alamat'=>$p['alamat']
        ]);

        $d['status'] = "sukses";
        $d['data'] = "Data Sekolah Berhasil Diupdate";

        j($d);
    }
    public function index() {
        $this->db->where('id', 1);
        $this->d['sekolah'] = $this->db->get('m_sekolah')->row_array();

    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }
}