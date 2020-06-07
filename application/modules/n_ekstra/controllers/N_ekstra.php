<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class N_ekstra extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("guru");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['url'] = "n_ekstra";
    }
    
    public function ambil_siswa($id_ekstra) {
        $list_data = array();

        $strq_sudah_ada_data = "SELECT 
                    a.id_siswa ids, c.nama nmsiswa, b.nilai, b.desk
                    FROM t_kelas_siswa a
                    LEFT JOIN 
                        (SELECT * FROM t_nilai_ekstra WHERE tasm = '".$this->d['c']['ta_tasm']."' 
                        AND id_ekstra = ".$id_ekstra.") b 
                        ON a.id_siswa = b.id_siswa
                    INNER JOIN m_siswa c ON a.id_siswa = c.id
                    WHERE a.id_kelas = ".$this->d['s']['walikelas']['id_walikelas']." 
                    AND a.ta = '".$this->d['c']['ta_tahun']."'";

        $ambil_nilai = $this->db->query($strq_sudah_ada_data)->result_array();
        
        if (empty($ambil_nilai)) {
            $list_data = $this->db->query($strq_belum_ada_data)->result_array();
            $d['ambil_mana'] = "data nilai kosong";
            //$d['last_queri'] = $this->db->last_query();
        } else {
            $list_data = $ambil_nilai;
            $d['ambil_mana'] = "data nilai real";
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

        // j($p);
        // exit;

        $tambah = 0;
        $edit = 0;
        foreach ($p['nilai'] as $s) {
            if ($s != "Tidak Ikut") {
                $this->db->where('tasm', $this->d['c']['ta_tasm']);
                $this->db->where('id_ekstra', $p['id_ekstra']);
                $this->db->where('id_siswa', $p['id_siswa'][$i]);
                $cek = $this->db->get('t_nilai_ekstra')->num_rows();


                if ($cek > 0) {
                    $this->db->where('tasm', $this->d['c']['ta_tasm']);
                    $this->db->where('id_ekstra', $p['id_ekstra']);
                    $this->db->where('id_siswa', $p['id_siswa'][$i]);
                    $this->db->update('t_nilai_ekstra', [
                        'nilai'=>$s,
                        'desk'=>$p['nilai_d'][$i]
                    ]);
                    $edit++;
                } else {
                    $this->db->insert('t_nilai_ekstra', [
                        'tasm'=>$this->d['c']['ta_tasm'],
                        'id_ekstra'=>$p['id_ekstra'],
                        'id_siswa'=>$p['id_siswa'][$i],
                        'nilai'=>$s,
                        'desk'=>$p['nilai_d'][$i]
                    ]);
                    $tambah++;
                }
            }
            $i++;
        }
        
        $d['status'] = "ok";
        $d['data'] = "Data berhasil disimpan. Tambah: ".$tambah.", Edit: ".$edit;
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