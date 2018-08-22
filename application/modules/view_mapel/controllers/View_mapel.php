<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class View_mapel extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');
        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['url'] = "view_mapel";
        $this->d['idnya'] = "viewmapel";
        $this->d['nama_form'] = "f_view_mapel";
        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        cek_aktif();
    }
    public function index() {
    	$this->d['list_mapelkelas'] = $this->db->query("SELECT 
                                                a.id, b.kd_singkat nmmapel, a.id_mapel, a.id_kelas, c.nama nmkelas, b.is_sikap
                                                FROM t_guru_mapel a
                                                INNER JOIN m_mapel b ON a.id_mapel = b.id
                                                INNER JOIN m_kelas c ON a.id_kelas = c.id 
                                                WHERE a.id_guru = '".$this->d['admkonid']."'
                                                AND a.tasm = '".$this->d['tasm']."'") ->result_array();
        $this->d['p'] = "v_view_mapel";
        $this->load->view("template_utama", $this->d);
    }

    
    public function cetak_absensi($id) {
        
        $detil_mp = $this->db->query("select 
                a.tasm, a.id_kelas, a.tasm, b.nama nmmapel, c.nama nmguru, c.nip, d.nama kelas
                from t_guru_mapel a 
                inner join m_mapel b on a.id_mapel = b.id
                inner join m_guru c on a.id_guru = c.id
                inner join m_kelas d on a.id_kelas = d.id
                where a.id = '".$id."'")->row_array();

        $strq = "select 
                c.nama, c.jk
                from t_kelas_siswa a 
                inner join m_siswa c on a.id_siswa = c.id
                where a.id_kelas = '".$detil_mp['id_kelas']."' 
                AND a.ta = LEFT('".$detil_mp['tasm']."',4)
                ORDER BY c.nama ASC";

        $data = $this->db->query($strq)->result_array();

        $ta = substr($detil_mp['tasm'], 0, 4) ."/". (substr($detil_mp['tasm'], 0, 4) + 1);
        $sm = substr($detil_mp['tasm'], 4, 1);

        $html = '<center>DAFTAR HADIR / PRESENSI SISWA</center><br>
        <table class="tablef">
            <tr>
                <td width="15%">Satuan Kerja</td>
                <td width="2%">:</td>
                <td width="28%">MTs N 5 Kulon Progo</td>
                <td width="10%"></td>
                <td width="15%">Kelas/Semester</td>
                <td width="2%">:</td>
                <td width="28%">'.$detil_mp['kelas'].'/'.($sm).'</td>
            </tr>
            <tr><td>Tahun Pelajaran</td><td>:</td><td>'.$ta.'</td><td></td><td>Mata Pelajaran</td><td>:</td><td>'.$detil_mp['nmmapel'].'</td></tr>
        </table>';

        $html .= '
            <table class="table">
                <tr>
                    <td rowspan="2" width="2%" class="ctr">No</td>
                    <td rowspan="2" width="22%" class="ctr">Nama</td>
                    <td rowspan="2" width="4%" class="ctr">JK</td>
                    <td colspan="25" class="ctr">Tgl</td>
                    <td colspan="3" class="ctr">Absensi</td>
                    <td rowspan="2" width="2%" class="ctr">Ket</td>
                </tr>
                <tr style="height: 60px">';
        for ($i = 1; $i <= 25; $i++) {
            $html .= '<td width="2.5%"></td>';
        }

        $html .= '<td width="2.5%" class="ctr">S</td><td width="2.5%" class="ctr">I</td><td width="2.5%" class="ctr">A</td></tr>';
        if (!empty($data)) {
            $no = 1;
            foreach ($data as $d) {
                $html .= '<tr><td class="ctr">'.($no++).'</td><td>'.$d['nama'].'</td><td class="ctr">'.$d['jk'].'</td>';
                for ($i = 1; $i <= 25; $i++) {
                    $html .= '<td></td>';
                }
                $html .= '<td></td><td></td><td></td><td></td></tr>';
            }
        }

        $html .= '<table class="tablef" style="margin-top: 10px">
            <tr>
            <td width="15%"></td>
            <td width="25%">
            Mengetahui,<br>
            Kepala Madrasah<br><br><br><br>
            Drs. Sukarlan<br>
            NIP. 19650422 200012 1 001
            </td>
            <td width="25%"></td>
            <td width="25%">
            Guru Mata Pelajaran,<br>
            <br><br><br><br>
            '.$detil_mp['nmguru'].'<br>
            NIP. '.$detil_mp['nip'].'
            </td>
            </tr>
            </table>';



        $this->d['html'] = $html;
        $this->load->view('cetak_absensi', $this->d);
    }
}