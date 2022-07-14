<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class Home extends Master {
	public function __construct() {
        parent::__construct();
        cek_aktif();


        $akses = array("admin", "siswa", "guru");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['nama_form'] = "f_login";
    }

    public function index() {
    	/*
        $q_jml_siswa = $this->db->query("SELECT 
                                        SUM(IF(a.jk='L',1,0)) jml_l,
                                        SUM(IF(a.jk='P',1,0)) jml_p
                                        FROM m_siswa a
                                        WHERE a.stat_data = 'A'")->row_array();
        $q_jml_guru = $this->db->query("SELECT COUNT(id) jml
                                        FROM m_guru a
                                        WHERE a.stat_data = 'A'")->row_array();
        $this->d['jml_siswa'] = $q_jml_siswa;
        $this->d['jml_guru'] = $q_jml_guru;
        $q_jml_kelas = $this->db->query("SELECT 
                                        SUM(IF(b.jk='L',1,0)) jmlk_l,
                                        SUM(IF(b.jk='P',1,0)) jmlk_p
                                        FROM t_kelas_siswa a
                                        INNER JOIN m_siswa b ON a.id_siswa = b.id
                                        WHERE a.ta = '".$this->d['ta']."' AND a.id_kelas = '".$this->d['id_kelas']."'")->row_array();
        //echo $this->db->last_query();
        $this->d['stat_kelas'] = $q_jml_kelas;
        */

        $this->d['guru_input_nk'] = $this->db->query("select 
                t_guru_mapel.id_guru, m_guru.nama nmguru, m_mapel.kd_singkat nmmapel, m_kelas.nama nmkelas
                from t_nilai_ket
                inner join t_guru_mapel on t_nilai_ket.id_guru_mapel = t_guru_mapel.id
                inner join m_guru on t_guru_mapel.id_guru = m_guru.id
                inner join m_mapel on t_guru_mapel.id_mapel = m_mapel.id
                inner join m_kelas on t_guru_mapel.id_kelas = m_kelas.id
                where t_guru_mapel.tasm = '".$this->d['c']['ta_tasm']."' 
                group by t_guru_mapel.id_guru, t_guru_mapel.id_mapel, t_guru_mapel.id_kelas, t_nilai_ket.id
                order by t_nilai_ket.id")->result_array();
        $this->d['guru_input_np'] = $this->db->query("select 
                t_guru_mapel.id_guru, m_guru.nama nmguru, m_mapel.kd_singkat nmmapel, m_kelas.nama nmkelas
                from t_nilai
                inner join t_guru_mapel on t_nilai.id_guru_mapel = t_guru_mapel.id
                inner join m_guru on t_guru_mapel.id_guru = m_guru.id
                inner join m_mapel on t_guru_mapel.id_mapel = m_mapel.id
                inner join m_kelas on t_guru_mapel.id_kelas = m_kelas.id
                where t_guru_mapel.tasm = '".$this->d['c']['ta_tasm']."' 
                group by t_guru_mapel.id_guru, t_guru_mapel.id_mapel, t_guru_mapel.id_kelas, t_nilai.id
                order by t_nilai.id")->result_array();

        if ($this->d['s']['level'] != "siswa") {
            $this->d['p'] = "v_home";
        } else {
            $this->d['p'] = "v_home_siswa";
        }
        $this->load->view("template_utama", $this->d);
    }
    public function ubah_password() {
        $this->d['p'] = "v_ubah_password";
        $this->load->view("template_utama", $this->d);
    }
    public function simpan_ubah_password() {
        $id_user = $this->d['s']['id'];
        $this->db->where('id', $id_user);
        $cek_user = $this->db->get("m_admin")->row_array();

        $p = $this->input->post();
        
        $plama = sha1(sha1($p['p1']));
        $d = array();

        if (empty($cek_user)) {
            $d['status'] = "gagal";
            $d['data'] = "User tidak ditemukan";
        } else if ($p['username'] != $cek_user['username'])  {
            $d['status'] = "gagal";
            $d['data'] = "Username tidak ditemukan";
        } else if ($plama != $cek_user['password'])  {
            $d['status'] = "gagal";
            $d['data'] = "Password lama tidak cocok";
        } else if (strlen($p['p2']) < 6) {
            $d['status'] = "gagal";
            $d['data'] = "Password minimal 6 karakter";
        } else if ($p['p2'] != $p['p3']) {
            $d['status'] = "gagal";
            $d['data'] = "Password baru tidak sama";
        } else {
            $this->db->where('id', $id_user);
            $this->db->update('m_admin', [
                'password'=>sha1(sha1($p['p2']))
            ]);

            $d['status'] = "ok";
            $d['data'] = "Password berhasil diubah";
        }
        j($d);
        exit;
    }
    
    public function cetak() {
        $this->d['siswa'] = $this->db->query("SELECT * FROM m_siswa WHERE m_siswa.id IN (SELECT id_siswa FROM t_kelas_siswa)")->result_array();
        $this->d['tahun'] = $this->db->query("SELECT * FROM tahun")->result_array();
        
        $this->d['p'] = "v_cetak";
        $this->load->view("template_utama", $this->d);
    }
    
    public function cetak_rapot_ok() {
        $p = $this->input->post();
        $id_siswa = $p['id_siswa'];
        $tahun = $p['tahun'];
        
        redirect('cetak_raport/cetak/'.$id_siswa.'/'.$tahun);
    }
    
    
}