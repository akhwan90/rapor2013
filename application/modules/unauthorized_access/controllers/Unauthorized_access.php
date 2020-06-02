<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class Unauthorized_access extends Master {
	function __construct() {
        parent::__construct();
        cek_aktif();
    }

    public function index() {
    	$this->d['p'] = "no_akses";
        $this->load->view("template_utama", $this->d);
    }

}