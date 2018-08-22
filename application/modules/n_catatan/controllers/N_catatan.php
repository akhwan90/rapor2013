<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class N_catatan extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['url'] = "n_catatan";

        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['ta'] = substr($this->d['tasm'], 0, 4);

        $wali = $this->session->userdata($this->sespre."walikelas");

        $this->d['id_kelas'] = $wali['id_walikelas'];
        $this->d['nama_kelas'] = $wali['nama_walikelas'];
    }

    public function cetak($bawa) {
        $this->d['data_nilai'] = $this->db->query("SELECT
                                                    c.nama,a.s, a.i, a.a
                                                    FROM t_nilai_absensi a
                                                    LEFT JOIN t_kelas_siswa b ON a.id_siswa = b.id_siswa
                                                    LEFT JOIN m_siswa c ON b.id_siswa = c.id
                                                    WHERE b.id_kelas = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['tasm']."'")->result_array();

        $this->load->view('cetak', $this->d);
    }


    public function simpan() {
        $p = $this->input->post();
        
        $mode_form = $p['mode_form'];

        for ($i = 1; $i < $p['jumlah']; $i++) {
            $tasm = $this->d['tasm'];
            $id_siswa = $p['id_siswa_'.$i];
            $naik = $p['naik_'.$i];
            $catatan = $p['catatan_'.$i] == "" ? "-" : $p['catatan_'.$i];

            if ($mode_form == "add") {
                $strq = "INSERT INTO t_naikkelas (id_siswa,ta,naik,catatan_wali) VALUES ('$id_siswa','$tasm','$naik','$catatan')";
            } else {
                $strq = "UPDATE t_naikkelas SET naik = '".$naik."', catatan_wali = '".$catatan."' WHERE ta = '".$tasm."' AND id_siswa = '".$id_siswa."'";
            }
            
            
            $this->db->query($strq);
        }

        $d['status'] = "ok";
        $d['data'] = "Data berhasil disimpan..";

        j($d);
    }

    public function index() {
        $this->d['p_naik'] = array("Y"=>"Ya","N"=>"Tidak");

        $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.*, b.nama, a.naik, a.catatan_wali
                                                    FROM t_naikkelas a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    INNER JOIN t_kelas_siswa c ON CONCAT(c.ta,c.id_kelas,c.id_siswa) = CONCAT('".$this->d['ta']."','".$this->d['id_kelas']."',b.id)
                                                    WHERE c.id_kelas = '".$this->d['id_kelas']."' AND a.ta = '".$this->d['tasm']."'")->result_array();

        $this->d['mode_form'] = "edit";
        
        


        if (empty($this->d['siswa_kelas'])) {
            $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.id_siswa, b.nama, '' naik, '' catatan_wali
                                                    FROM t_kelas_siswa a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_kelas = '".$this->d['id_kelas']."' AND a.ta = '".$this->d['ta']."'")->result_array();
            $this->d['mode_form'] = "add";
            
            
        }
        
        // echo $this->db->last_query();
        // exit;
        
    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

}