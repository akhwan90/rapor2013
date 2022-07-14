<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class Data_guru extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("admin");
        cek_hak_akses($this->d['s']['level'], $akses);


        $this->d['url'] = "data_guru";
        $this->d['idnya'] = "dataguru";
        $this->d['nama_form'] = "f_dataguru";
    
    }

    public function datatable() {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');

        $d_total_row = $this->db->query("SELECT id FROM m_guru")->num_rows();
    
        $qdata = $this->db->query("SELECT 
                                    a.*,
                                    (SELECT COUNT(id) FROM m_admin WHERE level = 'guru' AND konid = a.id) AS jml_aktif,
                                    b.username
                                    FROM m_guru a
                                    LEFT JOIN m_admin b ON CONCAT('guru',a.id) = CONCAT(b.level,b.konid) 
                                    WHERE a.nama LIKE '%".$search['value']."%' ORDER BY a.nama ASC LIMIT ".$start.", ".$length."");
        $q_datanya = $qdata->result_array();
        $j_datanya = $qdata->num_rows();

        $data = array();
        $no = ($start+1);

        foreach ($q_datanya as $d) {
            $data_ok = array();
            $data_ok[0] = $no++;
            $data_ok[1] = $d['nip'];
            $data_ok[2] = $d['jml_aktif'] > 0 ? $d['nama']." / <b><i>".$d['username']."</i></b> / <b><i>guruhebat</i></b>" : $d['nama'];
            $data_ok[3] = $d['jml_aktif'] > 0 ? '<span class="label label-success">Aktif</span>' : '<span class="label label-warning">Belum Aktif</span>';

            $link_aktif_user = $d['jml_aktif'] > 0 ? '<a href="#" onclick="return nonaktifkan(\''.$d['id'].'\');" class="btn btn-xs btn-warning"><i class="fa fa-user"></i> NonAktifkan User</a>' : '<a href="#" onclick="return aktifkan(\''.$d['id'].'\');" class="btn btn-xs btn-info"><i class="fa fa-user"></i> Aktifkan User</a>';

            $data_ok[4] = '<a href="#" onclick="return edit(\''.$d['id'].'\');" class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</a> 
                <a href="#" onclick="return hapus(\''.$d['id'].'\');" class="btn btn-xs btn-danger"><i class="fa fa-remove"></i> Hapus</a> '.$link_aktif_user;

            $data[] = $data_ok;
        }

        $json_data = array(
            "draw" => $draw,
            "iTotalRecords" => $j_datanya,
            "iTotalDisplayRecords" => $d_total_row,
            "data" => $data
        );
        
        j($json_data);
        exit;
    }

    public function edit($id) {
        $q = $this->db->query("SELECT *, 'edit' AS mode FROM m_guru WHERE id = '$id'")->row_array();

        $d = array();
        $d['status'] = "ok";
        if (empty($q)) {
            $d['data']['id'] = "";
            $d['data']['mode'] = "add";
            $d['data']['nama'] = "";
            $d['data']['nip'] = "";
        } else {
            $d['data'] = $q;
        }

        j($d);
    }

    public function simpan() {
        $p = $this->input->post();

        $d['status'] = "";
        $d['data'] = "";

        if ($p['_mode'] == "add") {

            // cek nip 
            $this->db->where('nip', $p['nip']);
            $this->db->select('id');
            $get_nip = $this->db->get('m_guru')->num_rows();

            if ($get_nip < 1) {
                $this->db->insert("m_guru", [
                    'nama'=>$p['nama'],
                    'nip'=>$p['nip'],
                    'is_bk'=>$p['isbk'],
                ]);

                $d['status'] = "ok";
                $d['data'] = "Data berhasil disimpan";
            } else {
                $d['status'] = "gagal";
                $d['data'] = "NIP sudah dipakai";
            }
        } else if ($p['_mode'] == "edit") {
            $this->db->where('id', $p['id']);
            $this->db->update('m_guru', [
                'nama'=>$p['nama'],
                'is_bk'=>$p['isbk'],
            ]);

            $d['status'] = "ok";
            $d['data'] = "Data berhasil disimpan";
        } else {
            $d['status'] = "gagal";
            $d['data'] = "Kesalahan sistem";
        }

        j($d);
    }

    public function hapus($id) {
        $this->db->query("DELETE FROM m_guru WHERE id = '$id'");

        $d['status'] = "ok";
        $d['data'] = "Data berhasil dihapus";
        
        j($d);
    }

    public function aktifkan($id) {

        $this->db->where('id', $id);
        $this->db->select('nip');
        $detil_data = $this->db->get("m_guru")->row_array();

        if (empty($detil_data)) {
            $d['status'] = "gagal";
            $d['data'] = "Terjadi kesalahan sistem..";
        } else {
            $username = $detil_data['nip'];
            $password = password_hash('guruhebat', PASSWORD_DEFAULT);


            $this->db->where('username', $username);
            $cek_username = $this->db->get("m_admin");

            $jml_username = $cek_username->num_rows();
            $jika_sudah_ada = $jml_username > 0 ? $username."_".($jml_username++) : $username;
            $username_fix = $jika_sudah_ada;

            $this->db->insert('m_admin', [
                'username'=>$username_fix,
                'password'=>$password,
                'level'=>'guru',
                'konid'=>$id,
                'aktif'=>'Y'
            ]);

            $d['status'] = "ok";
            $d['data'] = "Username : ".$username_fix." berhasil diaktifkan..! Password default <b>guruhebat</b>";
        }
        
        j($d);
    }

    public function nonaktifkan($id) {

        $detil_data = $this->db->query("SELECT nama FROM m_guru WHERE id = '".$id."'")->row_array();

        if (empty($detil_data)) {
            $d['status'] = "gagal";
            $d['data'] = "Terjadi kesalahan sistem..";
        } else {
            $username = strtolower(str_replace(array(".",","," "), array("","",""), $detil_data['nama']));
            $password = sha1(sha1('guru123'));

            $this->db->query("DELETE FROM m_admin WHERE level = 'guru' AND konid = '$id'");

            $d['status'] = "ok";
            $d['data'] = "User dinonaktifkan..";
        }
        
        j($d);
    }

    public function index() {
    	$this->d['p'] = "list";
        $this->d['p_isbk'] = array("2"=>"Bukan","1"=>"Ya");
        $this->load->view("template_utama", $this->d);
    }

}