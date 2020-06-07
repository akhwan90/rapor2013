<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class N_catatan extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("guru");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['url'] = "n_catatan";
    }

    public function cetak($bawa) {
        $this->d['data_nilai'] = $this->db->query("SELECT
                                                    c.nama,a.s, a.i, a.a
                                                    FROM t_nilai_absensi a
                                                    LEFT JOIN t_kelas_siswa b ON a.id_siswa = b.id_siswa
                                                    LEFT JOIN m_siswa c ON b.id_siswa = c.id
                                                    WHERE b.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' AND a.tasm = '".$this->d['c']['ta_tasm']."'")->result_array();

        $this->load->view('cetak', $this->d);
    }


    public function simpan() {
        $p = $this->input->post();
        
        $mode_form = $p['mode_form'];

        $edit = 0;
        $tambah = 0;
        for ($i = 1; $i < $p['jumlah']; $i++) {
            $tasm = $this->d['c']['ta_tasm'];
            $id_siswa = $p['id_siswa_'.$i];
            $naik = !empty($p['naik_'.$i]) ? $p['naik_'.$i] : NULL;
            $catatan = $p['catatan_'.$i] == "" ? "-" : $p['catatan_'.$i];

            $this->db->where('ta', $tasm);
            $this->db->where('id_siswa', $id_siswa);
            $cek = $this->db->get('t_naikkelas')->num_rows();

            if ($cek > 0) {
                $this->db->where('ta', $tasm);
                $this->db->where('id_siswa', $id_siswa);
                $this->db->update('t_naikkelas', [
                    'naik'=>$naik,
                    'catatan_wali'=>$catatan,
                ]);
                $edit++;
            } else {
                $this->db->where('ta', $tasm);
                $this->db->where('id_siswa', $id_siswa);
                $this->db->insert('t_naikkelas', [
                    'ta'=>$tasm,
                    'id_siswa'=>$id_siswa,
                    'naik'=>$naik,
                    'catatan_wali'=>$catatan,
                ]);
                $tambah++;
            }
            
        }

        $d['status'] = "ok";
        $d['data'] = "Data berhasil disimpan. Tambah: ".$tambah.", Edit: ".$edit;

        j($d);
    }

    public function index() {
        $this->d['p_naik'] = array("Y"=>"Ya","N"=>"Tidak");

        $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.*, b.nama, a.naik, a.catatan_wali
                                                    FROM t_naikkelas a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    INNER JOIN t_kelas_siswa c ON CONCAT(c.ta,c.id_kelas,c.id_siswa) = CONCAT('".$this->d['c']['ta_tahun']."','".$this->d['s']['walikelas']['id_walikelas']."',b.id)
                                                    WHERE c.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' AND a.ta = '".$this->d['c']['ta_tasm']."'")->result_array();

        $this->d['mode_form'] = "edit";

        if (empty($this->d['siswa_kelas'])) {
            $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.id_siswa, b.nama, '' naik, '' catatan_wali
                                                    FROM t_kelas_siswa a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' AND a.ta = '".$this->d['c']['ta_tahun']."'")->result_array();
            $this->d['mode_form'] = "add";
            
            
        }
        
        // echo $this->db->last_query();
        // exit;
        
    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

}