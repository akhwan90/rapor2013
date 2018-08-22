<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class N_prestasi extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['url'] = "n_prestasi";

        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['ta'] = substr($this->d['tasm'], 0, 4);

        $wali = $this->session->userdata($this->sespre."walikelas");

        $this->d['id_kelas'] = $wali['id_walikelas'];
        $this->d['nama_kelas'] = $wali['nama_walikelas'];
        $this->d['nama_kelas'] = $wali['nama_walikelas'];
    }

    public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT 
                                        a.id, c.nama nmsiswa, a.jenis, a.keterangan
                                        FROM t_prestasi a 
                                        LEFT JOIN t_kelas_siswa b ON a.id_siswa = b.id_siswa
                                        LEFT JOIN m_siswa c ON a.id_siswa = c.id
                                        WHERE (b.id_kelas = '".$this->d['id_kelas']."' 
                                        AND a.ta = '".$this->d['tasm']."') 
                                        AND (c.nama LIKE '%".$search['value']."%' 
                                        OR a.jenis LIKE '%".$search['value']."%' 
                                        OR a.keterangan LIKE '%".$search['value']."%' 
                                        )")->num_rows();
    
        $q_datanya = $this->db->query("SELECT 
                                        a.id, c.nama nmsiswa, a.jenis, a.keterangan
                                        FROM t_prestasi a 
                                        LEFT JOIN t_kelas_siswa b ON a.id_siswa = b.id_siswa
                                        LEFT JOIN m_siswa c ON a.id_siswa = c.id
                                        WHERE b.id_kelas = '".$this->d['id_kelas']."' 
                                        AND a.ta = '".$this->d['tasm']."'
                                        AND (c.nama LIKE '%".$search['value']."%' 
                                        OR a.jenis LIKE '%".$search['value']."%' 
                                        OR a.keterangan LIKE '%".$search['value']."%' 
                                        )
                                        LIMIT ".$start.", ".$length."")->result_array();
        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[0] = $no++;
            $data_ok[1] = $d['nmsiswa'];
            $data_ok[2] = $d['jenis'];
            $data_ok[3] = $d['keterangan'];

            $data_ok[4] = '<a href="#" class="btn btn-xs btn-danger" onclick="return hapus('.$d['id'].');"><i class="fa fa-remove"></i> Hapus</a> ';

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


    public function simpan() {
        $p = $this->input->post();
        
        $p_data = array(
            "id_siswa"=>$p['id_siswa'],  
            "jenis"=>$p['jenis'],  
            "keterangan"=>$p['keterangan'],  
            "ta"=>$this->d['tasm'],  
        );
        
        $this->db->insert("t_prestasi", $p_data);

        $d['status'] = "ok";
        $d['data'] = "Data berhasil disimpan..";

        j($d);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM t_prestasi WHERE id = '$id'");
        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus..";

        j($d);
    }

    public function index() {
        $this->d['siswa_kelas'] = $this->db->query("SELECT 
                                                    a.id_siswa, b.nama
                                                    FROM t_kelas_siswa a
                                                    INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                    WHERE a.id_kelas = '".$this->d['id_kelas']."' AND a.ta = '".$this->d['ta']."'")->result_array();
    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

}