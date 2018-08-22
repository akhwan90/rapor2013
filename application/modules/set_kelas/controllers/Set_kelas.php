<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Set_kelas extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['url'] = "set_kelas";
        $this->d['idnya'] = "setkelas";
        $this->d['nama_form'] = "f_setkelas";

        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['ta'] = substr($this->d['tasm'], 0, 4);

        //echo $this->d['ta'];
        //exit;
    }

    public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT 
                                        a.id, b.nama nmkelas, c.nama nmsiswa
                                        FROM t_kelas_siswa a
                                        INNER JOIN m_kelas b ON a.id_kelas = b.id
                                        INNER JOIN m_siswa c ON a.id_siswa = c.id
                                        ORDER BY nmkelas ASC, nmsiswa ASC")->num_rows();
    
        $q_datanya = $this->db->query("SELECT 
                                    a.id, b.nama nmkelas, c.nama nmsiswa
                                    FROM t_kelas_siswa a
                                    INNER JOIN m_kelas b ON a.id_kelas = b.id
                                    INNER JOIN m_siswa c ON a.id_siswa = c.id
                                    WHERE c.nama LIKE '%".$search['value']."%' 
                                    ORDER BY nmkelas ASC, nmsiswa ASC LIMIT ".$start.", ".$length."")->result_array();
        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[0] = $no++;
            $data_ok[1] = $d['nmkelas'];
            $data_ok[2] = $d['nmsiswa'];

            $data_ok[3] = '<a href="#" onclick="return hapus(\''.$d['id'].'\');" class="btn btn-xs btn-danger"><i class="fa fa-remove"></i> Hapus</a> ';

            $data[] = $data_ok;
        }

        $json_data = array(
                    "draw" => $draw,
                    "iTotalRecords" => $d_total_row,
                    "iTotalDisplayRecords" => $d_total_row,
                    "data" => $data
                );
        j($json_data);
        exit;
    }

    public function edit($id) {
        $q = $this->db->query("SELECT id, nama FROM m_siswa a 
                                WHERE YEAR(a.diterima_tgl) > 2015 AND stat_data = 'A' AND a.id NOT IN 
                                (SELECT id_siswa FROM t_kelas_siswa WHERE ta = ".$this->d['ta'].") 
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
            $teks_val[] = "('".$p['kelas']."', '".$s."', '".date('Y')."')";
        }

        $query = "INSERT IGNORE INTO t_kelas_siswa (id_kelas, id_siswa, ta) VALUES ".implode(", ", $teks_val).";";
        
        $this->db->query($query);
        redirect($this->d['url']);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM t_kelas_siswa WHERE id = '$id'");
        $this->db->query("DELETE FROM t_nilai WHERE id_siswa = '$id' AND tasm = '".$this->d['tasm']."'");
        $this->db->query("DELETE FROM t_nilai_absensi WHERE id_siswa = '$id' AND tasm = '".$this->d['tasm']."'");
        $this->db->query("DELETE FROM t_nilai_ekstra WHERE id_siswa = '$id' AND tasm = '".$this->d['tasm']."'");
        $this->db->query("DELETE FROM t_nilai_ket WHERE id_siswa = '$id' AND tasm = '".$this->d['tasm']."'");
        $this->db->query("DELETE FROM t_nilai_sikap_so WHERE id_siswa = '$id' AND tasm = '".$this->d['tasm']."'");
        $this->db->query("DELETE FROM t_nilai_sikap_sp WHERE id_siswa = '$id' AND tasm = '".$this->d['tasm']."'");

        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
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
                                                        a.id, a.id_kelas, b.nama nmsiswa
                                                        FROM t_kelas_siswa a
                                                        INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                        WHERE a.id_kelas = '".$v['id']."' 
                                                        AND a.ta = '".$this->d['ta']."'
                                                        ORDER BY b.nis ASC, b.nama ASC")->result_array();

                if (!empty($q_siswa_per_kelas)) {
                    $no = 1;
                    foreach ($q_siswa_per_kelas as $k) {
                        $tampil .= '<tr><td>'.$no++.'</td><td>'.$k['nmsiswa'].'</td><td class="ctr"><a href="#" onclick="return hapus('.$k['id'].');" class="btn btn-danger btn-xs"><i class="fa fa-remove"></i></a></td></tr>';
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