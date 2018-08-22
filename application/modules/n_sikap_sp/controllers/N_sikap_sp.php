<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class N_sikap_sp extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');
        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['url'] = "n_sikap_sp";
        $this->d['idnya'] = "setmapel";
        $this->d['nama_form'] = "f_nilai_ssp";
        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['ta'] = substr($this->d['tasm'], 0, 4);
        //ambil id di tabel t_walikelas (HANYA id_nya saja)
        $wali_kelas = $this->session->userdata('app_rapot_walikelas');
    	$this->d['id_kelas'] = $wali_kelas['id_walikelas'];
    }
    public function cetak() {
        //echo $this->d['id_kelas'];
        $this->d['detil_data'] = $this->db->query("SELECT 
                                                    b.nama nmguru, d.nama nmkelas
                                                    FROM t_walikelas a
                                                    INNER JOIN m_guru b ON a.id_guru = b.id
                                                    INNER JOIN m_kelas d ON a.id_kelas = d.id
                                                    WHERE a.id_kelas = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['ta']."'")->row_array();
        
        $this->d['data_nilai'] = $this->db->query("SELECT 
                                                    a.*, b.nama
                                                    FROM t_nilai_sikap_sp a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    INNER JOIN t_walikelas c ON a.id_guru_mapel = c.id
                                                    WHERE a.id_guru_mapel = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['tasm']."'")->result_array();
        $q_list_kd = $this->db->query("SELECT id, nama_kd FROM t_mapel_kd WHERE jenis = 'SSp'")->result_array();
        $array_kd = array();
        foreach ($q_list_kd as $v) {
            $idx = $v['id'];
            $val = $v['nama_kd'];
            $array_kd[$idx] = $val;
        }
        $this->d['list_kd'] = $array_kd;
        $this->load->view('cetak', $this->d);
    }
    public function simpan_nilai() {
        $p = $this->input->post();
        $id_guru = $this->d['admkonid'];
        $id_guru_mapel = $this->d['id_kelas'];
        $is_wali = 'Y';
        if ($p['mode_form'] == "add") {
            $sql = array();
            for ($i = 1; $i < $p['jumlah']; $i++) {
                $selalu = $p['ssp1_'.$i]."-".$p['ssp2_'.$i];
                $meningkat = $p['ssp3_'.$i];
                $sql[] = "('".$this->d['tasm']."','".$id_guru_mapel."','".$p['id_siswa_'.$i]."','".$is_wali."', '".$selalu."','".$meningkat."')";
            }
            $this->db->query("INSERT INTO t_nilai_sikap_sp (tasm,id_guru_mapel,id_siswa,is_wali,selalu,mulai_meningkat) VALUES ".implode(",", $sql).";");
        } else if ($p['mode_form'] == "edit") {
            for ($i = 1; $i < $p['jumlah']; $i++) {
                $selalu = $p['ssp1_'.$i]."-".$p['ssp2_'.$i];
                $meningkat = $p['ssp3_'.$i];
                $this->db->query("UPDATE t_nilai_sikap_sp SET id_guru_mapel = '".$id_guru_mapel."', selalu = '".$selalu."', mulai_meningkat = '".$meningkat."' WHERE id_guru_mapel = '".$this->d['admkonid']."' AND tasm = '".$this->d['tasm']."' AND id_siswa = '".$p['id_siswa_'.$i]."'");
            }
        }
        $d['status'] = "ok";
        $d['data'] = "Data berhasil disimpan";
        j($d);
    }
    public function index() {
        //$detil_guru_mapel = $this->db->query("SELECT * FROM t_walikelas WHERE id_guru = '".$this->d['admkonid']."' AND tasm = '".$this->d['tasm']."'")->row_array();
        $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.*, b.nama
                                                    FROM t_nilai_sikap_sp a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_guru_mapel = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['tasm']."'")->result_array();
        //echo $this->db->last_query();
        //exit;
        //echo var_dump($this->d['siswa_kelas']);
        $this->d['mode_form'] = "edit";
        if (empty($this->d['siswa_kelas'])) {
            $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.id_siswa, b.nama, '-' selalu, '' mulai_meningkat
                                                    FROM t_kelas_siswa a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_kelas = '".$this->d['id_kelas']."' AND a.ta = '".$this->d['ta']."'")->result_array();
            $this->d['mode_form'] = "add";
        }
        $this->d['list_kd'] = $this->db->query("SELECT id, nama_kd FROM t_mapel_kd 
                                    WHERE jenis = 'Ssp'")->result_array();
    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }
}