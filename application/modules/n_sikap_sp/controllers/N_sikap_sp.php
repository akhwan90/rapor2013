<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class N_sikap_sp extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("guru");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['url'] = "n_sikap_sp";
        $this->d['idnya'] = "setmapel";
        $this->d['nama_form'] = "f_nilai_ssp";

        //ambil id di tabel t_walikelas (HANYA id_nya saja)
        $wali_kelas = $this->session->userdata('walikelas');
    	$this->d['id_kelas'] = $wali_kelas['id_walikelas'];
    }
    public function cetak() {
        //echo $this->d['id_kelas'];
        $this->d['detil_wali_kelas'] = $this->db->query("SELECT 
                                                    b.nama nmguru, d.nama nmkelas
                                                    FROM t_walikelas a
                                                    INNER JOIN m_guru b ON a.id_guru = b.id
                                                    INNER JOIN m_kelas d ON a.id_kelas = d.id
                                                    WHERE a.id_kelas = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['c']['ta_tasm']."'")->row_array();

        
        $this->d['data_nilai'] = $this->db->query("SELECT 
                                                    a.*, b.nama
                                                    FROM t_nilai_sikap_sp a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    INNER JOIN t_walikelas c ON a.id_guru_mapel = c.id
                                                    WHERE a.id_guru_mapel = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['c']['ta_tasm']."'")->result_array();
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
        $id_guru = $this->d['s']['konid'];
        $id_guru_mapel = $this->d['s']['walikelas']['id_walikelas'];
        $is_wali = 'Y';

        $tambah = 0;
        $edit = 0;

        for ($i = 1; $i < $p['jumlah']; $i++) {
            $selalu = $p['ssp1_'.$i]."-".$p['ssp2_'.$i];
            $meningkat = $p['ssp3_'.$i];
            $id_siswa = $p['id_siswa_'.$i];

            $this->db->where('tasm', $this->d['c']['ta_tasm']);
            $this->db->where('id_guru_mapel', $id_guru_mapel);
            $this->db->where('id_siswa', $id_siswa);
            $cek = $this->db->get('t_nilai_sikap_sp')->num_rows();

            if ($cek > 0) {
                $this->db->where('tasm', $this->d['c']['ta_tasm']);
                $this->db->where('id_guru_mapel', $id_guru_mapel);
                $this->db->where('id_siswa', $id_siswa);
                $this->db->update('t_nilai_sikap_sp', [
                    'selalu'=>$selalu,
                    'mulai_meningkat'=>$meningkat
                ]);
                $edit++;
            } else {
                $this->db->insert('t_nilai_sikap_sp', [
                    'tasm'=>$this->d['c']['ta_tasm'],
                    'id_guru_mapel'=>$id_guru_mapel,
                    'id_siswa'=>$id_siswa,
                    'is_wali'=>'Y',
                    'selalu'=>$selalu,
                    'mulai_meningkat'=>$meningkat,
                ]);
                $tambah++;
            }
        }

        $d['status'] = "ok";
        $d['data'] = "Data berhasil disimpan. Tambah: ".$tambah.", Edit: ".$edit;

        j($d);
    }
    public function index() {
        //$detil_guru_mapel = $this->db->query("SELECT * FROM t_walikelas WHERE id_guru = '".$this->d['s']['konid']."' AND tasm = '".$this->d['c']['ta_tasm']."'")->row_array();
        $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.*, b.nama
                                                    FROM t_nilai_sikap_sp a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_guru_mapel = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['c']['ta_tasm']."'")->result_array();
        //echo $this->db->last_query();
        //exit;
        //echo var_dump($this->d['siswa_kelas']);
        $this->d['mode_form'] = "edit";
        if (empty($this->d['siswa_kelas'])) {
            $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.id_siswa, b.nama, '-' selalu, '' mulai_meningkat
                                                    FROM t_kelas_siswa a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_kelas = '".$this->d['id_kelas']."' AND a.ta = '".$this->d['c']['ta_tahun']."'")->result_array();
            $this->d['mode_form'] = "add";
        }
        $this->d['list_kd'] = $this->db->query("SELECT id, nama_kd FROM t_mapel_kd 
                                    WHERE jenis = 'Ssp'")->result_array();
    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }
}