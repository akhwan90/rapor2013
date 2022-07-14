<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class Login extends Master {
	function __construct() {
        parent::__construct();

        // $this->sespre = $this->config->item('session_name_prefix');
        // $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        
        $this->d['nama_form'] = "f_login";
        
        // $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        // $this->d['tasm'] = $get_tasm['tahun'];
        // $this->d['ta'] = substr($this->d['tasm'], 0, 4);
    }
    public function index() {
    	$this->d['p'] = "login";
        $this->load->view("template_utama", $this->d);
    }

    /*public function update_password($username) {
        $this->db->query("ALTER TABLE `m_admin` CHANGE `password` `password` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");

        $this->db->where('username', $username);
        $this->db->update('m_admin', ['password'=>password_hash($username, PASSWORD_DEFAULT)]);
        
        echo "Update password ".$username." sukses";
    }*/

    public function update_db()
    {
        // update password
        $this->db->query('ALTER TABLE `m_admin` CHANGE `password` `password` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;');
        $this->db->update('m_admin', [
            'password' => password_hash('rahasia123!', PASSWORD_DEFAULT)
        ]);
        
        // tambah field kkm
        $this->db->query('ALTER TABLE `t_guru_mapel` ADD `kkm` INT(3) NOT NULL AFTER `id_mapel`;');
        
        // tabel m_sekolah
        $this->db->query('CREATE TABLE `m_sekolah`( `id` int(1) NOT NULL, `nama_sekolah` varchar(100) NOT NULL, `alamat` varchar(50) DEFAULT NULL, `desa` varchar(50) DEFAULT NULL, `kec` varchar(50) DEFAULT NULL, `kab` varchar(50) DEFAULT NULL, `prov` varchar(100) DEFAULT NULL, `sebutan_kepala` varchar(50) DEFAULT NULL, `logo` varchar(128) DEFAULT NULL, `telp` varchar(50) DEFAULT NULL, `email` varchar(100) DEFAULT NULL, `web` varchar(100) DEFAULT NULL, `kodepos` varchar(10) DEFAULT NULL, `nss` varchar(20) DEFAULT NULL, `npsn` varchar(20) DEFAULT NULL, `kop_1` varchar(100) DEFAULT NULL, `kop_2` varchar(100) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
        $this->db->query('ALTER TABLE `m_sekolah` ADD PRIMARY KEY(`id`);');
        $this->db->query('ALTER TABLE `m_sekolah` MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;');

        // insert tabel m_sekolah
        $this->db->query("INSERT INTO `m_sekolah`(`id`, `nama_sekolah`, `alamat`, `desa`, `kec`, `kab`, `prov`, `sebutan_kepala`, `logo`, `telp`, `email`, `web`, `kodepos`, `nss`, `npsn`, `kop_1`, `kop_2`) VALUES (1, 'SMP 1 Percobaan', 'Jl. Pahlawan No 1', 'Wates', 'Wates', 'Kulon Progo', 'DI Yogyakarta', 'Kepala Sekolah', NULL, '08123', 'sekolah@gmail.com', '-', '-', '-', '-', '-', '-'); ");

        echo "OK";
    }

    public function do_login() {
    	$p = $this->input->post();
    	$u = $this->security->xss_clean($p['username']);
        $p = $this->security->xss_clean($p['password']);

		
        $this->db->where('username', $u);
        $q_cek = $this->db->get('m_admin');
        $j_cek = $q_cek->num_rows();
        $d_cek = $q_cek->row_array();

        if ($j_cek != 1) {
            $d['status'] = "gagal";
            $d['data'] = "Terjadi kesalahan...!";
        } else {
            $cek_password = (password_verify($p, $d_cek['password']));

            if (!($cek_password)) {
                $d['status'] = "gagal";
                $d['data'] = "Terjadi kesalahan...!";
            } else {
                $level = $d_cek['level'];
                $data_login = [];

                if ($level == "guru") {
                    $id_guru = $d_cek['konid'];

                    $this->db->where('a.id_guru', $id_guru);
                    $this->db->where('a.tasm', $this->d['c']['ta_tasm']);
                    $this->db->select('a.id_kelas, b.nama nmkelas');
                    $this->db->join('m_kelas b', 'a.id_kelas = b.id');
                    $cek_is_wali_kelas = $this->db->get('t_walikelas a')->row_array();

                    $this->db->select('nama, nip, jk');
                    $this->db->where('id', $d_cek['konid']);
                    $detil_nama = $this->db->get('m_guru')->row_array();

                    if (!empty($cek_is_wali_kelas)) {
                        $data_login = array(
                            'id' => $d_cek['id'],
                            'user' => $d_cek['username'],
                            'level' => $d_cek['level'],
                            'valid' => true,
                            'konid' => $d_cek['konid'],
                            'nama' => $detil_nama['nama'],
                            'jk' => $detil_nama['jk'],
                            'nip' => $detil_nama['nip'],
                            'walikelas' => array(
                                "is_wali"=>true, 
                                "id_walikelas"=>$cek_is_wali_kelas['id_kelas'],
                                "nama_walikelas"=>$cek_is_wali_kelas['nmkelas']
                            )
                        );  
                    } else {
                        $data_login = array(
                            'id' => $d_cek['id'],
                            'user' => $d_cek['username'],
                            'level' => $d_cek['level'],
                            'valid' => true,
                            'konid' => $d_cek['konid'],
                            'nama' => $detil_nama['nama'],
                            'jk' => $detil_nama['jk'],
                            'nip' => $detil_nama['nip'],
                            'walikelas' => array(
                                "is_wali"=>false, 
                                "id_walikelas"=>"",
                                "nama_walikelas"=>""
                            )
                        );  
                    }
                } else if ($level == "siswa") {
                    $this->db->where('id', $d_cek['konid']);
                    $this->db->select('nama, nis, nisn, jk');
                    $detil_nama = $this->db->get('m_siswa')->row_array();

                    $data_login = array(
                        'id' => $d_cek['id'],
                        'user' => $d_cek['username'],
                        'level' => $d_cek['level'],
                        'valid' => true,
                        'konid' => $d_cek['konid'],
                        'nama' => $detil_nama['nama'],
                        'jk' => $detil_nama['jk'],
                        'nip' => $detil_nama['nis'],
                        'walikelas' => array(
                            "is_wali"=>false, 
                            "id_walikelas"=>"",
                            "nama_walikelas"=>""
                        )
                    );  
                } else if ($level == "admin") {
                    $data_login = array(
                        'id' => $d_cek['id'],
                        'user' => $d_cek['username'],
                        'level' => $d_cek['level'],
                        'valid' => true,
                        'konid' => $d_cek['konid'],
                        'nama'    => "Administrator",
                        'nip'     => "-",
                        'walikelas' => array("is_wali"=>false, "id_walikelas"=>"","nama_walikelas"=>"")
                    );  
                }

                $this->session->set_userdata($data_login);

                $d['status'] = "ok";
                $d['data'] = "Login berhasil";
            }
        }

        j($d);
        exit;
    }

    public function logout() {
		$data = array(
            'id' 		=> "",
            'user' 	=> "",
            'level' 	=> "",
            'valid' 	=> false,
            'konid'   => "",
            'nama'    => "",
            'nip'     => "",
            'walikelas' 	=> null,
        );
        
        $this->session->set_userdata($data);
		redirect('home');
    }
}