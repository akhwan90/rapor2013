<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class Riwayat_mengajar extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("guru");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['url'] = "data_siswa";
        $this->d['idnya'] = "datasiswa";
        $this->d['nama_form'] = "f_datasiswa";
    }
    
    public function index() {
    	$this->d['p'] = "list";
    	$this->d['history_mengajar'] = $this->db->query("SELECT 
                        a.*, b.nama nmmapel, c.nama nmkelas
                        FROM t_guru_mapel a 
                        INNER JOIN m_mapel b ON a.id_mapel = b.id
                        INNER JOIN m_kelas c ON a.id_kelas = c.id
                        WHERE a.id_guru = '".$this->d['s']['konid']."'")->result_array();
        
        // echo $this->db->last_query();
        // exit;
        
        $this->load->view("template_utama", $this->d);
    }
}