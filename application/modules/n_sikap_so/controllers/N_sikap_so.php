<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class N_sikap_so extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("guru");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['url'] = "n_sikap_so";
        $this->d['idnya'] = "setmapel";
        $this->d['nama_form'] = "f_nilai_sso";
    }

    public function cetak() {
        $this->d['detil_wali_kelas'] = $this->db->query("SELECT 
                                                    b.nama nmguru, d.nama nmkelas
                                                    FROM t_walikelas a
                                                    INNER JOIN m_guru b ON a.id_guru = b.id
                                                    INNER JOIN m_kelas d ON a.id_kelas = d.id
                                                    WHERE a.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' AND a.tasm = '".$this->d['c']['ta_tasm']."'")->row_array();

        $this->d['data_nilai'] = $this->db->query("SELECT 
                                                    a.*, b.nama
                                                    FROM t_nilai_sikap_so a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    INNER JOIN t_walikelas c ON a.id_guru_mapel = c.id
                                                    WHERE a.id_guru_mapel = '".$this->d['s']['walikelas']['id_walikelas']."' AND a.tasm = '".$this->d['c']['ta_tasm']."'")->result_array();
        // j($this->d['data_nilai']);
        // echo $this->db->last_query();
        // exit;

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

        // validasi
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
            $tambah = 0;
            $edit = 0;

            for ($i = 1; $i < $p['jumlah']; $i++) {
                $tasm = $this->d['c']['ta_tasm'];
                $id_guru_mapel = $this->d['s']['walikelas']['id_walikelas'];
                $id_siswa = $p['id_siswa_'.$i];
                $selalu = implode(",",$p['selalu_'.$i]);
                $meningkat = $p['meningkat_'.$i];
                $is_wali = 'Y';

                // cek dulu
                $this->db->where('tasm', $tasm);
                $this->db->where('id_guru_mapel', $id_guru_mapel);
                $this->db->where('id_siswa', $id_siswa);
                $cek = $this->db->get('t_nilai_sikap_so')->num_rows();

                if ($cek > 0) {
                    $this->db->where('tasm', $tasm);
                    $this->db->where('id_guru_mapel', $id_guru_mapel);
                    $this->db->where('id_siswa', $id_siswa);
                    $cek = $this->db->update('t_nilai_sikap_so', [
                            'selalu'=>$selalu,
                            'mulai_meningkat'=>$meningkat
                        ]
                    );
                    $edit++;
                } else {
                    $this->db->insert('t_nilai_sikap_so', [
                        'tasm'=>$tasm,
                        'id_guru_mapel'=>$id_guru_mapel,
                        'id_siswa'=>$id_siswa,
                        'is_wali'=>'Y',
                        'selalu'=>$selalu,
                        'mulai_meningkat'=>$meningkat
                    ]);
                    $tambah++;
                }
            }
            
            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan. Tambah: ".$tambah.", Edit: ".$edit;
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
                                                    WHERE a.id_guru_mapel = '".$this->d['s']['walikelas']['id_walikelas']."' AND a.tasm = '".$this->d['c']['ta_tasm']."'")->result_array();
        $this->d['mode_form'] = "edit";
        if (empty($this->d['siswa_kelas'])) {
            $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.id_siswa, b.nama, '-' selalu, '' mulai_meningkat
                                                    FROM t_kelas_siswa a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' AND a.ta = '".$this->d['c']['ta_tahun']."'")->result_array();
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