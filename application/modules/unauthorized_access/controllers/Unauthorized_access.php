<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unauthorized_access extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');
        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
               
    }

    public function index() {
    	$this->d['p'] = "no_akses";
        $this->load->view("template_utama", $this->d);
    }

}