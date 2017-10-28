<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class N_ekstra extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['walikelas'] = $this->session->userdata($this->sespre.'walikelas');
        $this->d['url'] = "n_ekstra";

        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['ta'] = '2016';

        $wali = $this->session->userdata($this->sespre."walikelas");

        $this->d['id_kelas'] = $wali['id_walikelas'];
        $this->d['nama_kelas'] = $wali['nama_walikelas'];
    }

    
    public function ambil_siswa($id_ekstra) {

        $list_data = array();

        $ambil_nilai = $this->db->query("SELECT 
                                        b.id ids, b.nama nmsiswa, c.nilai, c.desk
                                        FROM t_kelas_siswa a 
                                        LEFT JOIN m_siswa b ON a.id_siswa = b.id
                                        LEFT JOIN t_nilai_ekstra c ON a.id_siswa = c.id_siswa
                                        WHERE a.id_kelas = '".$this->d['walikelas']['id_walikelas']."' AND c.id_ekstra = '$id_ekstra' AND c.tasm = '".$this->d['tasm']."'")->result_array();
        

        if (empty($ambil_nilai)) {
            $list_data = $this->db->query("SELECT 
                                        b.id ids, b.nama nmsiswa, '' nilai, '' desk
                                        FROM t_kelas_siswa a 
                                        LEFT JOIN m_siswa b ON a.id_siswa = b.id
                                        WHERE a.id_kelas = '".$this->d['walikelas']['id_walikelas']."' AND a.ta = '".$this->d['ta']."'")->result_array();
        } else {
            $list_data = $ambil_nilai;
        }

        //echo $this->db->last_query();

        $d['status'] = "ok";
        $d['data'] = $list_data;

        j($d);
    }

    public function simpan_nilai() {
        $p = $this->input->post();


        $jumlah_sudah = 0;

        $i = 0;
        foreach ($p['nilai'] as $s) {
            
            $cek = $this->db->query("SELECT id FROM t_nilai_ekstra WHERE tasm = '".$this->d['tasm']."' AND id_ekstra = '".$p['id_ekstra']."' AND id_siswa = '".$p['id_siswa'][$i]."'")->num_rows();

            //echo $this->db->last_query();
            //exit;

            if ($cek > 0) {
                $jumlah_sudah ++;
                $this->db->query("UPDATE t_nilai_ekstra SET nilai = '$s', desk = '".$p['nilai_d'][$i]."' WHERE tasm = '".$this->d['tasm']."' AND id_ekstra = '".$p['id_ekstra']."' AND id_siswa = '".$p['id_siswa'][$i]."'");
            } else {
                $this->db->query("INSERT INTO t_nilai_ekstra (tasm, id_ekstra, id_siswa, nilai, desk) VALUES ('".$this->d['tasm']."', '".$p['id_ekstra']."', '".$p['id_siswa'][$i]."', '".$s."', '".$p['nilai_d'][$i]."')");
            }

            $i++;
        }
        
        $d['status'] = "ok";
        $d['data'] = "Data berhasil disimpan";

        j($d);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM t_guru_mapel WHERE id = '$id'");

        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }

    public function index() {
        /*
        $this->d['detil_mp'] = $this->db->query("SELECT 
                                        a.*, b.nama nmmapel, c.nama nmkelas, c.tingkat tingkat
                                        FROM t_guru_mapel a
                                        INNER JOIN m_mapel b ON a.id_mapel = b.id 
                                        INNER JOIN m_kelas c ON a.id_kelas = c.id 
                                        WHERE a.id  = '$id'")->row_array();
        */

        $this->d['list_kd'] = $this->db->query("SELECT * FROM m_ekstra")->result_array();

    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

}