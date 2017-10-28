<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class View_mapel extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');


        $this->d['url'] = "view_mapel";
        $this->d['idnya'] = "viewmapel";
        $this->d['nama_form'] = "f_view_mapel";

        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];

        cek_aktif();
    }

    public function index() {
    	$this->d['list_mapelkelas'] = $this->db->query("SELECT 
                                                a.id, b.kd_singkat nmmapel, a.id_mapel, a.id_kelas, c.nama nmkelas, b.is_sikap
                                                FROM t_guru_mapel a
                                                INNER JOIN m_mapel b ON a.id_mapel = b.id
                                                INNER JOIN m_kelas c ON a.id_kelas = c.id 
                                                WHERE a.id_guru = '".$this->d['admkonid']."'")->result_array();

        $this->d['p'] = "v_view_mapel";
        $this->load->view("template_utama", $this->d);
    }
}