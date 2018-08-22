<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class N_sikap_so extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');
        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['url'] = "n_sikap_so";
        $this->d['idnya'] = "setmapel";
        $this->d['nama_form'] = "f_nilai_sso";
        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['ta'] = substr($this->d['tasm'], 0, 4);
        
        $wali_kelas = $this->session->userdata('app_rapot_walikelas');
        $this->d['id_kelas'] = $wali_kelas['id_walikelas'];
    }
    public function cetak() {
        $this->d['detil_data'] = $this->db->query("SELECT 
                                                    b.nama nmguru, d.nama nmkelas
                                                    FROM t_walikelas a
                                                    INNER JOIN m_guru b ON a.id_guru = b.id
                                                    INNER JOIN m_kelas d ON a.id_kelas = d.id
                                                    WHERE a.id_kelas = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['ta']."'")->row_array();
        $this->d['data_nilai'] = $this->db->query("SELECT 
                                                    a.*, b.nama
                                                    FROM t_nilai_sikap_so a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    INNER JOIN t_guru_mapel c ON a.id_guru_mapel = c.id
                                                    WHERE a.id_guru_mapel = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['tasm']."'")->result_array();
        $q_list_kd = $this->db->query("SELECT id, nama_kd FROM t_mapel_kd WHERE jenis = 'SSo'")->result_array();
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
        $mode_form = $p['mode_form'];
        $error = array();
        for ($i = 1; $i < $p['jumlah']; $i++) {
            $selalu = empty($p['selalu_'.$i]) ? "" : $p['selalu_'.$i];
            $meningkat = empty($p['meningkat_'.$i]) ? "" : $p['meningkat_'.$i];
            //echo var_dump($selalu);
            //echo var_dump($meningkat);
            if (!empty($selalu)) {
                if (in_array($meningkat, $selalu)) {
                    $error[] = "Error baris ".$i." : Isian \"meningkat\" sudah dipakai di isian \"Selalu\"";
                }
            } else {
                $error[] = "Error baris ".$i." : masih kosong";
            }
        }
        if (empty($error)) {
            $strsql = "";
            
            for ($i = 1; $i < $p['jumlah']; $i++) {
                $tasm = $this->d['tasm'];
                $id_guru_mapel = $this->d['id_kelas'];
                $id_siswa = $p['id_siswa_'.$i];
                $selalu = implode(",",$p['selalu_'.$i]);
                $meningkat = $p['meningkat_'.$i];
                $is_wali = 'Y';
                if ($mode_form == "add") {
                    $this->db->query("INSERT INTO t_nilai_sikap_so (tasm,id_guru_mapel,id_siswa,is_wali,selalu,mulai_meningkat) VALUES ('$tasm','$id_guru_mapel','$id_siswa','$is_wali','$selalu','$meningkat')");
                } else {
                    $this->db->query("UPDATE t_nilai_sikap_so SET is_wali = '".$is_wali."', selalu = '".$selalu."', mulai_meningkat = '".$meningkat."' WHERE id_guru_mapel = '".$id_guru_mapel."' AND tasm = '".$tasm."' AND id_siswa = '".$id_siswa."'");
                }
                
            }
            
            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan..";
        } else {
            $d['status'] = "gagal";
            $d['data'] = implode("<br>", $error);
        }
        j($d);
    }
    public function index() {
        $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.*, b.nama
                                                    FROM t_nilai_sikap_so a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_guru_mapel = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['tasm']."'")->result_array();
        $this->d['mode_form'] = "edit";
        if (empty($this->d['siswa_kelas'])) {
            $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.id_siswa, b.nama, '-' selalu, '' mulai_meningkat
                                                    FROM t_kelas_siswa a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_kelas = '".$this->d['id_kelas']."' AND a.ta = '".$this->d['ta']."'")->result_array();
            $this->d['mode_form'] = "add";
        }
        $q_list_kd = $this->db->query("SELECT id, nama_kd FROM t_mapel_kd 
                                    WHERE jenis = 'Sso'");
        
        $this->d['list_kd'] = $q_list_kd->result_array();
        $this->d['jmlh_kd'] = $q_list_kd->num_rows();
        $this->d['dropdown_kd'] = array();
        if (!empty($this->d['list_kd'])) {
            foreach ($this->d['list_kd'] as $v) {
                $this->d['dropdown_kd'][$v['id']] = $v['nama_kd'];
            }
        }
        
    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }
}