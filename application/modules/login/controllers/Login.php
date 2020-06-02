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
    public function do_login() {
    	$p = $this->input->post();
    	$u = $this->security->xss_clean($p['username']);
        $p = $this->security->xss_clean($p['password']);

        $p_enkrip = sha1(sha1($p));
		
        $this->db->where('username', $u);
        $q_cek = $this->db->get('m_admin');
        $j_cek = $q_cek->num_rows();
        $d_cek = $q_cek->row_array();

        if ($j_cek != 1) {
            $d['status'] = "gagal";
            $d['data'] = "Terjadi kesalahan...!";
        } else {
            $cek_password = ($p_enkrip === $d_cek['password']);

            if (!($cek_password)) {
                $d['status'] = "gagal";
                $d['data'] = "Terjadi kesalahan...!";
            } else {
                $level = $d_cek['level'];
                $data_login = [];

                if ($level == "guru") {
                    $id_guru = $d_cek['konid'];

                    $this->db->where('a.id_guru', $id_guru);
                    $this->db->where('a.tasm', $this->d['c']['ta_tahun']);
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

        /*$q_cek	= $this->db->query("SELECT * FROM m_admin WHERE username = '".$u."' AND password = '".$p_enkrip."'");
		$j_cek	= $q_cek->num_rows();
		$d_cek	= $q_cek->row();
		//echo $j_cek;
        if($j_cek == 1) {
            $level = $d_cek->level;
            if ($level == "guru") {
                $cek_is_wali_kelas = $this->db->query("SELECT a.id_kelas, b.nama nmkelas FROM t_walikelas a INNER JOIN m_kelas b ON a.id_kelas = b.id WHERE a.tasm = '".$this->d['ta']."' AND a.id_guru = '".$d_cek->konid."'")->row_array();
                $detil_nama = $this->db->query("SELECT nama, nip, jk FROM m_guru WHERE id = '".$d_cek->konid."'")->row();
                if (!empty($cek_is_wali_kelas)) {
                    $data = array(
                                $this->sespre.'id' => $d_cek->id,
                                $this->sespre.'user' => $d_cek->username,
                                $this->sespre.'level' => $d_cek->level,
                                $this->sespre.'valid' => true,
                                $this->sespre.'konid' => $d_cek->konid,
                                $this->sespre.'nama' => $detil_nama->nama,
                                $this->sespre.'jk' => $detil_nama->jk,
                                $this->sespre.'nip' => $detil_nama->nip,
                                $this->sespre.'walikelas' => array("is_wali"=>true, "id_walikelas"=>$cek_is_wali_kelas['id_kelas'],"nama_walikelas"=>$cek_is_wali_kelas['nmkelas'])
                                );  
                } else {
                    $data = array(
                                $this->sespre.'id' => $d_cek->id,
                                $this->sespre.'user' => $d_cek->username,
                                $this->sespre.'level' => $d_cek->level,
                                $this->sespre.'valid' => true,
                                $this->sespre.'konid' => $d_cek->konid,
                                $this->sespre.'nama' => $detil_nama->nama,
                                $this->sespre.'jk' => $detil_nama->jk,
                                $this->sespre.'nip' => $detil_nama->nip,
                                $this->sespre.'walikelas' => array("is_wali"=>false, "id_walikelas"=>"","nama_walikelas"=>"")
                                );  
                }
            } else if ($level == "siswa") {
                $detil_nama = $this->db->query("SELECT nama, nis, nisn, jk FROM m_siswa WHERE id = '".$d_cek->konid."'")->row();
                $data = array(
                            $this->sespre.'id' => $d_cek->id,
                            $this->sespre.'user' => $d_cek->username,
                            $this->sespre.'level' => $d_cek->level,
                            $this->sespre.'valid' => true,
                            $this->sespre.'konid' => $d_cek->konid,
                            $this->sespre.'nama' => $detil_nama->nama,
                            $this->sespre.'jk' => $detil_nama->jk,
                            $this->sespre.'nip' => $detil_nama->nis,
                            $this->sespre.'walikelas' => array("is_wali"=>false, "id_walikelas"=>"","nama_walikelas"=>"")
                            );  
            } else {
            	$data = array(
                            $this->sespre.'id' => $d_cek->id,
                            $this->sespre.'user' => $d_cek->username,
                            $this->sespre.'level' => $d_cek->level,
                            $this->sespre.'valid' => true,
                            $this->sespre.'konid' => $d_cek->konid,
                            $this->sespre.'nama'    => "Administrator",
                            $this->sespre.'nip'     => "-",
                            $this->sespre.'walikelas' => array("is_wali"=>false, "id_walikelas"=>"","nama_walikelas"=>"")
                            );  
            }
            $this->load->helper('cookie');
            $this->input->cookie("name",true);
            $this->session->set_userdata($data);

            
            $d['status'] = "ok";
	       	$d['data'] = "Login berhasil";
        } else {	
			$d['status'] = "gagal";
            $d['data'] = "Username atau password salah...!";
		}	
		j($d); 	*/
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