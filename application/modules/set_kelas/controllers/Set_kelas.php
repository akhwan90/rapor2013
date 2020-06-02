<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class Set_kelas extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("admin");
        cek_hak_akses($this->d['s']['level'], $akses);

        $this->d['url'] = "set_kelas";
        $this->d['idnya'] = "setkelas";
        $this->d['nama_form'] = "f_setkelas";

    }

    public function edit($id) {
        $q = $this->db->query("SELECT id, nama FROM m_siswa a 
                                WHERE stat_data = 'A' AND a.id NOT IN 
                                (SELECT id_siswa FROM t_kelas_siswa WHERE ta = ".$this->d['c']['ta_tahun'].") 
                                ORDER BY id ASC");
        $r = $this->db->query("SELECT * FROM m_kelas ORDER BY tingkat ASC, nama ASC");

        $this->d['siswa_asal'] = $q->result_array();
        $this->d['kelas'] = $r->result_array();

        $this->d['p'] = "form";
        $this->load->view("template_utama", $this->d);
    }

    public function simpan() {
        $p = $this->input->post();

        $teks_val = array();
        foreach ($p['siswa_pilih'] as $s) {
            $teks_val[] = "('".$p['kelas']."', '".$s."', '".$this->d['c']['ta_tahun']."')";
        }

        $query = "INSERT IGNORE INTO t_kelas_siswa (id_kelas, id_siswa, ta) VALUES ".implode(", ", $teks_val).";";
        
        $this->db->query($query);
        redirect($this->d['url']);
    }

    public function hapus($id) {
        // cek_siswa nilai
        $this->db->where('id_siswa', $id);
        $this->db->where('LEFT(tasm,4)', $this->d['c']['ta_tahun']);
        $get_sdh_ada_nilai_p = $this->db->get('t_nilai')->num_rows();

        $this->db->where('id_siswa', $id);
        $this->db->where('LEFT(tasm,4)', $this->d['c']['ta_tahun']);
        $get_sdh_ada_nilai_ket = $this->db->get('t_nilai_ket')->num_rows();

        $this->db->where('id_siswa', $id);
        $this->db->where('LEFT(tasm,4)', $this->d['c']['ta_tahun']);
        $get_sdh_ada_nilai_sso = $this->db->get('t_nilai_sikap_so')->num_rows();

        $this->db->where('id_siswa', $id);
        $this->db->where('LEFT(tasm,4)', $this->d['c']['ta_tahun']);
        $get_sdh_ada_nilai_ssp = $this->db->get('t_nilai_sikap_sp')->num_rows();

        $this->db->where('id_siswa', $id);
        $this->db->where('LEFT(tasm,4)', $this->d['c']['ta_tahun']);
        $get_sdh_ada_nilai_absen = $this->db->get('t_nilai_absensi')->num_rows();

        $this->db->where('id_siswa', $id);
        $this->db->where('LEFT(tasm,4)', $this->d['c']['ta_tahun']);
        $get_sdh_ada_nilai_ekstra = $this->db->get('t_nilai_ekstra')->num_rows();
        
        $jml_ada = ($get_sdh_ada_nilai_p + $get_sdh_ada_nilai_ket + $get_sdh_ada_nilai_sso + $get_sdh_ada_nilai_ssp + $get_sdh_ada_nilai_absen + $get_sdh_ada_nilai_ekstra);

        if ($jml_ada > 0) {
            $d['status'] = "gagal";
            $d['data'] = "Siswa sudah diinput nilainya";            
        } else {
            $this->db->where('id_siswa', $id);
            $this->db->where('ta', $this->d['c']['ta_tahun']);
            $this->db->delete('t_kelas_siswa');
            
            $d['status'] = "ok";
            $d['data'] = "Data berhasil dihapus";
        }

        
        j($d);
    }

    public function index() {
        $ambil_data_kelas = $this->db->query("SELECT id, nama FROM m_kelas ORDER BY tingkat ASC, nama ASC")->result_array();

        $tampil = "";

        if (!empty($ambil_data_kelas)) {
            foreach ($ambil_data_kelas as $v) {
                $tampil .= '<div class="col-md-4"><div class="panel panel-info">
                                <div class="panel-heading">'.$v['nama'].'</div>
                                <div class="panel-body" style="height: 300px; overflow: auto">
                                <table class="table table-stripped">
                                    <thead>
                                        <tr><th>No</th><th>Nama</th><th>Aksi</th></tr>
                                    </thead>
                                    <tbody>';

                $q_siswa_per_kelas = $this->db->query("SELECT 
                                                        a.id, a.id_siswa, a.id_kelas, b.nama nmsiswa
                                                        FROM t_kelas_siswa a
                                                        INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                        WHERE a.id_kelas = '".$v['id']."' 
                                                        AND a.ta = '".$this->d['c']['ta_tahun']."'
                                                        ORDER BY b.nis ASC, b.nama ASC")->result_array();

                if (!empty($q_siswa_per_kelas)) {
                    $no = 1;
                    foreach ($q_siswa_per_kelas as $k) {
                        $tampil .= '<tr><td>'.$no++.'</td><td>'.$k['nmsiswa'].'</td><td class="ctr"><a href="#" onclick="return hapus('.$k['id_siswa'].');" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i></a></td></tr>';
                    }
                }

                $tampil .= '</tbody></table></div></div></div>';
            }
        }

        $this->d['tampil'] = $tampil;
    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

}