<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Riwayat_mengajar extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');
        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
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
                        WHERE a.id_guru = '".$this->d['admkonid']."'")->result_array();
        
        // echo $this->db->last_query();
        // exit;
        
        $this->load->view("template_utama", $this->d);
    }
}