<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Set_mapel extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');

        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['url'] = "set_mapel";
        $this->d['idnya'] = "setmapel";
        $this->d['nama_form'] = "f_setmapel";

        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
    }

    public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT
                                        a.id, b.nama nmguru, c.nama nmkelas, d.nama nmmapel
                                        FROM t_guru_mapel a
                                        INNER JOIN m_guru b ON a.id_guru = b.id
                                        INNER JOIN m_kelas c ON a.id_kelas = c.id
                                        INNER JOIN m_mapel d ON a.id_mapel = d.id
                                        WHERE a.tasm = '".$this->d['tasm']."'
                                        ORDER BY nmguru ASC, nmmapel ASC, nmkelas ASC")->num_rows();
    
        $q_datanya = $this->db->query("SELECT
                                    a.id, b.nama nmguru, c.nama nmkelas, d.nama nmmapel
                                    FROM t_guru_mapel a
                                    INNER JOIN m_guru b ON a.id_guru = b.id
                                    INNER JOIN m_kelas c ON a.id_kelas = c.id
                                    INNER JOIN m_mapel d ON a.id_mapel = d.id
                                    WHERE a.tasm = '".$this->d['tasm']."' AND 
                                    (b.nama LIKE '%".$search['value']."%' 
                                    OR c.nama LIKE '%".$search['value']."%'
                                    OR d.nama LIKE '%".$search['value']."%')
                                    ORDER BY nmguru ASC, nmmapel ASC, nmkelas ASC
                                    LIMIT ".$start.", ".$length."")->result_array();
        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[0] = $no++;
            $data_ok[1] = $d['nmguru'];
            $data_ok[2] = $d['nmmapel']." - ".$d['nmkelas'];

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
        $q = $this->db->query("SELECT id, nama FROM m_guru ORDER BY id ASC");
        $r = $this->db->query("SELECT id, nama FROM m_mapel ORDER BY id ASC");
        $s = $this->db->query("SELECT id, nama FROM m_kelas ORDER BY nama ASC");

        $this->d['r_guru'] = $q->result_array();
        $this->d['r_mapel'] = $r->result_array();
        $this->d['r_kelas'] = $s->result_array();

        $this->d['p'] = "form";
        $this->load->view("template_utama", $this->d);
    }

    public function simpan() {
        $p = $this->input->post();

        $jumlah_sudah = 0;

        foreach ($p['data_pilih'] as $s) {
            $cek = $this->db->query("SELECT id FROM t_guru_mapel WHERE tasm = '".$this->d['tasm']."' AND id_mapel = '".$p['mapel']."' AND id_kelas = '$s'")->num_rows();
            
            if ($cek > 0) {
                $jumlah_sudah ++;
            } else {
                $this->db->query("INSERT INTO t_guru_mapel (tasm, id_guru, id_kelas, id_mapel) VALUES ('".$this->d['tasm']."', '".$p['guru']."', '".$s."', '".$p['mapel']."')");
            }
        }

        $this->session->set_flashdata('k', '<div class="alert alert-danger">'.$jumlah_sudah.' mata pelajaran, sudah ada gurunya. Data tidak masuk..</div>');
        
        redirect($this->d['url']);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM t_guru_mapel WHERE id = '$id'");

        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }
    
    public function copy_semester_lalu() {
        $sekarang = $this->d['tasm'];
        $tahun_lalu = intval(substr($sekarang, 0, 4));
        $semester_lalu = intval(substr($sekarang, 4, 1));
        
        if ($semester_lalu == 1) {
            $tahun_lalu = $tahun_lalu - 1;
            $semester_lalu = 2;
        } else {
            $tahun_lalu = $tahun_lalu;
            $semester_lalu = 1;
        }
        
        $semester_yll = $tahun_lalu.$semester_lalu;
        
        $queri = $this->db->query("SELECT * FROM t_guru_mapel WHERE tasm = '".$semester_yll."'")->result_array();
        
        $arre_input = array();
        if (!empty($queri)) {
            foreach ($queri as $d) {
                $teks1 = "('".$sekarang."', '".$d['id_guru']."', '".$d['id_kelas']."', '".$d['id_mapel']."')";
            
                $arre_input[] = $teks1;
            }
        }
        
        $hapus_sekarang = $this->db->query("DELETE FROM t_guru_mapel WHERE tasm = '".$sekarang."'");
        
        $queri_input = "INSERT INTO t_guru_mapel (tasm, id_guru, id_kelas, id_mapel) VALUES ".implode(",", $arre_input).";";
        
        $this->db->query($queri_input);
        redirect('set_mapel');
    }

    public function index() {
    	$this->d['p'] = "list";
        $this->load->view("template_utama", $this->d);
    }

}