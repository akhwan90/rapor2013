<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cetak_kartu extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->sespre = $this->config->item('session_name_prefix');
        $this->d['admlevel'] = $this->session->userdata($this->sespre.'level');
        $this->d['admkonid'] = $this->session->userdata($this->sespre.'konid');
        $this->d['url'] = "cetak_leger";
        $get_tasm = $this->db->query("SELECT tahun FROM tahun WHERE aktif = 'Y'")->row_array();
        $this->d['tasm'] = $get_tasm['tahun'];
        $this->d['ta'] = '2016';
        $this->d['wk'] = $this->session->userdata('app_rapot_walikelas');
        $wali = $this->session->userdata($this->sespre."walikelas");
        $this->d['id_kelas'] = $wali['id_walikelas'];
        $this->d['nama_kelas'] = $wali['nama_walikelas'];
        
    }
    public function index() {
        $queri = $this->db->query("SELECT 
                                    b.nis, b.nisn, b.nama, b.jk, c.nama nmkelas, c.tingkat
                                    FROM t_kelas_siswa a 
                                    LEFT JOIN m_siswa b ON a.id_siswa = b.id
                                    LEFT JOIN m_kelas c ON a.id_kelas = c.id
                                    WHERE a.ta = '2017'
                                    ORDER BY c.tingkat ASC, c.nama ASC, b.nis ASC, b.nama ASC")->result_array();

        $html = '<html>
                <head>
                    <style type="text/css">
                        .layout {border: none; width: 21cm}
                        .kartu {
                            border: solid 1px #000; 
                            width: 96%; 
                            margin-bottom: 5px 
                        }
                        .kartu tr td {
                            font-size: 12px; 
                            vertical-align: top; 
                            padding: 0px
                        }
                        .ctr {text-align: center}
                        .b_bawah {border-bottom: solid 1px #000}
                        .ttd {
                            margin-left: 60%; 
                            margin-top: -15px;
                            margin-bottom: 15px;
                            background: url(http://mtsn-sidoharjo.sch.id/ttd.png);
                            background-size: 62px 49px;
                            background-repeat: no-repeat;
                            background-position: 10px 5px;
                        }
                        .page {
                            position: relative;
                            width: 21cm;
                            min-height: 29.7cm;
                            page-break-after: always;
                            margin: 0cm auto;
                            margin-bottom: 0.1cm;
                            background: #FFF;
                            /*box-shadow: 0 2px 10px rgba(0,0,0,0.3);*/
                            -webkit-box-sizing: none;
                            -moz-box-sizing: none;
                            box-sizing: none;
                            page-break-after: always;
                        }
                        hr {border: solid 1px #000}
                    </style>
                    <title>Cetak Kartu</title>
                </head>
                <body>';
        $no = 1;

        $html .= '<div class="page"><center>1<br><table class="layout"><tbody><tr>';

        //$a_kelas = array();

        $no2 = 1;
        $kelas = "";
        $tahun = 2018;

        $header1 = empty($_GET['h1']) ? "KARTU PESERTA" : $_GET['h1'];  
        $header2 = empty($_GET['h2']) ? "TAHUN AJARAN" : $_GET['h2']; 
        $header3 = empty($_GET['h3']) ? "MTs N 5 KULON PROGO" : $_GET['h3'];

        if (!empty($queri)) {
            foreach ($queri as $q) {
                $idx = $q['tingkat'];

                if ($kelas == $idx) {
                    $no2++;
                } else {
                    $no2 = 1;
                    $kelas = $idx;
                    $tahun--;
                }

                //echo $kelas."<br>";

                $html .= '
                <td width="50%">
                    <table class="kartu">
                        <tr><td class="ctr"><img src="https://minhsukorejo.files.wordpress.com/2012/03/logo_kemenag_mi-nurul-huda-sukorejo.jpg" style="width: 50px;"></td><td colspan="2" class="ctr" style="vertical-align: middle">
                            <b>'.$header1.'<br>
                            '.$header2.'<br>
                            '.$header3.'</b></td></tr>
                        <tr><td colspan="3"><hr></td></tr>
                        <tr><td width="25%" style="padding-left: 30px">Nama</td><td width="2%">:</td><td width="73%">'.potong(strtoupper($q['nama'])).'</td></tr>
                        <tr><td style="padding-left: 30px">NIS / NISN</td><td>:</td><td>'.$q['nis'].' / '.$q['nisn'].'</td></tr>
                        <tr><td style="padding-left: 30px">No Peserta</td><td>:</td><td>'.$tahun.'-04-03-062-'.str_pad($no2, 3, '0', STR_PAD_LEFT).'</td></tr>
                        <tr><td style="padding-left: 30px">Kelas</td><td>:</td><td>'.$q['nmkelas'].'</td></tr>
                        <tr>
                        <td colspan="3">
                            <div class="ttd">
                            Kepala Madrasah<br><br><br>
                            <b>Drs. SUKARLAN<br>
                            NIP. 19650422 200012 1 001</b>
                            </div>
                        </td>
                        </tr>
                    </table>
                </td>';

                $html .= $no % 2 == 0 ? '</tr><tr>' : '';
                $html .= $no % 10 == 0 ? '</tbody></table></center></div><div class="page"><center>'.(($no/10)+1).'<br><table class="layout"><tbody><tr>' : '';
                $no++;
            }
        } 

        $html .= '</tbody></table></center></div>
                </body></html>';

        //echo $kelas;

        echo $html;
    }
}