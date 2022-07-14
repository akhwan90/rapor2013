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

        $config1['upload_path']          = './upload/logo';
        $config1['allowed_types']        = 'gif|jpg|png';
        $config1['max_size']             = 512;
        $config1['encrypt_name']         = true;

        $this->load->library('upload');
        $this->upload->initialize($config1);

        $pdata = [
            'nama_sekolah'=>$p['nama_sekolah'],
            'alamat'=>$p['alamat'],
            'desa'=>$p['desa'],
            'kec'=>$p['kec'],
            'kab'=>$p['kab'],
            'prov'=>$p['prov'],
            'telp'=>$p['telp'],
            'email'=>$p['email'],
            'web'=>$p['web'],
            'kodepos'=>$p['kodepos'],
            'sebutan_kepala'=>$p['sebutan_kepala'],
            'nss'=>$p['nss'],
            'npsn'=>$p['npsn'],
            'kop_1'=>$p['kop_1'],
            'kop_2'=>$p['kop_2'],
        ];

        if ($this->upload->do_upload('logo')) {
            // get sebelum 
            $this->db->where('id', 1);
            $this->db->select('logo');
            $get_gambar = $this->db->get('m_sekolah')->row_array();

            @unlink('./upload/logo/'.$get_gambar['logo']);

            $pdata['logo'] = $this->upload->data('file_name');
        }

        $this->db->where('id', 1);
        $this->db->update('m_sekolah', $pdata);

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