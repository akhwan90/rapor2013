<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {
    function __construct() {
        parent::__construct();

        // get_sekolah
        $this->db->where('id', 1);
        $get_sekolah= $this->db->get('m_sekolah')->row_array();
        
        // get tahun aktif
        $this->db->where('aktif', 'Y');
        $get_tahun_aktif = $this->db->get('tahun')->row_array();

        $this->d['c']['sekolah_nama'] = $get_sekolah['nama_sekolah']; 
        $this->d['c']['sekolah_alamat'] = $get_sekolah['alamat'].", ".$get_sekolah['desa']; 
        $this->d['c']['sekolah_sebutan_kepala'] = $get_sekolah['sebutan_kepala']; 
        $this->d['c']['sekolah_kota'] = $get_sekolah['kec']; 
        $this->d['c']['detil_sekolah'] = $get_sekolah;
        
        $this->d['c']['ta_tahun'] = substr($get_tahun_aktif['tahun'], 0, 4); 
        $this->d['c']['ta_semester'] = substr($get_tahun_aktif['tahun'], 4, 1); 
        $this->d['c']['ta_tasm'] = $get_tahun_aktif['tahun'];
        $this->d['c']['ta_kepsek_nama'] = $get_tahun_aktif['nama_kepsek'];
        $this->d['c']['ta_kepsek_nip'] = $get_tahun_aktif['nip_kepsek'];
        $this->d['c']['ta_tgl_raport'] = $get_tahun_aktif['tgl_raport'];
        $this->d['c']['ta_tgl_raport_k3'] = $get_tahun_aktif['tgl_raport_kelas3'];

        $this->d['s'] = $this->session->userdata();

    }

}