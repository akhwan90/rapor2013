<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cetak_leger extends CI_Controller {
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
        $this->d['p'] = "landing";
        $this->load->view("template_utama", $this->d);
    }
    public function cetak() {
        $uri3 = $this->uri->segment(3);
        
        $_tasm = '';
        if (empty($uri3)) {
            $_tasm = $this->d['tasm'];
        } else {
            $_tasm = $uri3;
        }
        
        $_ta = substr($_tasm,0,4);
        $_sm = substr($_tasm,4,1);
        
        
        $d = array();
        $id_kelas = $this->d['wk']['id_walikelas'];
        $siswa = $this->db->query("SELECT a.id_siswa, b.nama FROM t_kelas_siswa a INNER JOIN m_siswa b ON a.id_siswa = b.id WHERE a.id_kelas = $id_kelas AND a.ta = '".$_ta."' ORDER BY b.id ASC")->result_array();
        
        $mapel = $this->db->query("SELECT id, kd_singkat FROM m_mapel ORDER BY id ASC")->result_array();
        //echo $this->db->last_query();
        if (!empty($siswa)) {
            foreach ($siswa as $s) {
                $id_siswa = $s['id_siswa'];
                $n_pengetahuan = $this->db->query("select 
                                                c.id, group_concat(if(a.jenis='h',concat(a.jenis,'-',a.nilai),concat(a.jenis,'-',0))) gabung,
                                                round((((sum(if(a.jenis='h',a.nilai,0))/(count(if(a.jenis='h',1,0))-2)*2) + 
                                                (sum(if(a.jenis='t',a.nilai,0))) + 
                                                (sum(if(a.jenis='a',a.nilai,0)))) / 4),0) na
                                                from t_nilai a
                                                left join t_guru_mapel b on a.id_guru_mapel = b.id
                                                left join m_mapel c on b.id_mapel = c.id
                                                where a.id_siswa = $id_siswa and a.tasm = '".$_tasm."'
                                                group by b.id_mapel
                                                order by c.kd_singkat asc")->result_array();

                //echo $this->db->last_query();
                //exit;

                $n_keterampilan = $this->db->query("select 
                                                c.id,
                                                c.kd_singkat, 
                                                round(avg(a.nilai),0) na
                                                from t_nilai_ket a
                                                left join t_guru_mapel b on a.id_guru_mapel = b.id
                                                left join m_mapel c on b.id_mapel = c.id
                                                where a.id_siswa = $id_siswa and a.tasm = '".$_tasm."'
                                                group by b.id_mapel")->result_array();
                $jml = 0;
                if (!empty($n_pengetahuan)) {
                    foreach ($n_pengetahuan as $np) {
                        $idx = $np['id'];
                        $val = $np['na'];
                        
                        $jml += $val;
                        $d['np'][$id_siswa][$idx] = $val;
                    }
                }
                if (!empty($n_keterampilan)) {
                    foreach ($n_keterampilan as $nk) {
                        $idx = $nk['id'];
                        $val = $nk['na'];
                        
                        $jml += $val;
                        $d['nk'][$id_siswa][$idx] = $val;
                    }
                }
                $d['peringkat'][$id_siswa] = $jml;
                // echo var_dump($d[$id_siswa]);
            }
        } 
        //urutkan jumlah untuk mendapatkan peringkat
        rsort($d['peringkat']);
        //j($d['peringkat']);
        
        ///GENERATE HTML
        $html = '<table class="table"><thead><tr><th rowspan="2">No</th><th rowspan="2">Nama Siswa</th>';
        
        $baris_kedua = '';
        if (!empty($mapel)) {
            foreach ($mapel as $m) {
                $html .= '<th colspan="2">'.$m['kd_singkat'].'</th>';
                $baris_kedua .= '<th>P</th><th>K</th>';
            }
        }
        
        $html .= '<th colspan="3">Jumlah</th><th rowspan="2">Peringkat</th></tr><tr>'.$baris_kedua.'<th>P</th><th>K</th><th>Jml</th></tr></thead><tbody>';
        if (!empty($siswa)) {
            $no = 1;
            foreach ($siswa as $s) {
                $html .= '<tr><td class="ctr">'.$no++.'</td><td>'.$s['nama'].'</td>';
                if (!empty($mapel)) {
                    $jml = 0;
                    $jml_p = 0;
                    $jml_k = 0;
                    foreach ($mapel as $m) {
                        $idx_siswa = $s['id_siswa'];
                        $idx_mapel = $m['id'];
                        $isi_p = empty($d['np'][$idx_siswa][$idx_mapel]) ? "-" : $d['np'][$idx_siswa][$idx_mapel];
                        $isi_k = empty($d['nk'][$idx_siswa][$idx_mapel]) ? "-" : $d['nk'][$idx_siswa][$idx_mapel];
                        
                        $jml_p += $isi_p;
                        $jml_k += $isi_k;
                        $jml += ($isi_p+$isi_k);
                        
                        $html .= '<td class="ctr">'.$isi_p.'</td><td class="ctr">'.$isi_k.'</td>';
                    }
                }
                $peringkat = array_search($jml, $d['peringkat']) + 1;
                $html .= '<td class="ctr"><b>'.$jml_p.'</td><td class="ctr"><b>'.$jml_k.'</b></td><td class="ctr"><b>'.$jml.'</b></td><td class="ctr">'.$peringkat.'</td></tr>';
            }
        }
        $html .= '</tbody></table>';
        $d['html'] = $html;
        $d['teks_tasm'] = "Tahun Ajaran ".$_ta."/".($_ta+1).", Semester ".$_sm;
        //j($d);
        $this->load->view('cetak', $d);
    }
    public function cetak_ekstra() {
        $data = array();
        $q_d_ekstra = $this->db->query("SELECT * FROM m_ekstra ORDER BY id ASC");
        $j_d_ekstra = $q_d_ekstra->num_rows();
        $d_d_ekstra = $q_d_ekstra->result_array();
        $q_absensi = $this->db->query("SELECT
                                        a.id, a.id_siswa, c.nama,a.s, a.i, a.a
                                        FROM t_nilai_absensi a
                                        LEFT JOIN t_kelas_siswa b ON a.id_siswa = b.id_siswa
                                        LEFT JOIN m_siswa c ON b.id_siswa = c.id
                                        WHERE b.id_kelas = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['tasm']."'")->result_array();
        $q_ekstra = $this->db->query("SELECT 
                                        a.*
                                        FROM t_nilai_ekstra a 
                                        INNER JOIN t_kelas_siswa b ON a.id_siswa = b.id_siswa
                                        WHERE b.id_kelas = '".$this->d['id_kelas']."' AND a.tasm = '".$this->d['tasm']."'")->result_array();
        if (!empty($q_absensi)) {
            foreach ($q_absensi as $d) {
                $idx = $d['id_siswa'];
                $data[$idx]['nama'] = $d['nama'];
                $data[$idx]['absensi']['s'] = $d['s'];
                $data[$idx]['absensi']['i'] = $d['i'];
                $data[$idx]['absensi']['a'] = $d['a'];
            }
        }
        if (!empty($q_ekstra)) {
            foreach ($q_ekstra as $e) {
                $idx = $e['id_siswa'];
                $idx_id_ekstra = $e['id_ekstra'];
                $data[$idx]['ekstra'][$idx_id_ekstra]['nilai'] = $e['nilai'];
                $data[$idx]['ekstra'][$idx_id_ekstra]['desk'] = $e['desk'];
            }
        }
        

        $html = '<table class="table"><thead><tr><th rowspan="2">No</th><th rowspan="2">Nama</th><th colspan="4">Absensi</th><th colspan="'.$j_d_ekstra.'">Ekstrakurikuler</th></tr><tr><th>S</th><th>I</th><th>A</th><th>Jml</th>';
        $arr_id_ekstra = array();
        if (!empty($d_d_ekstra)) {
            foreach ($d_d_ekstra as $k) {
                $html .= '<th>'.$k['nama'].'</th>';
                $arr_id_ekstra[] = $k['id'];
            }
        }
        $no = 1;
        foreach ($data as $k => $v) {
            $html .= '<tr><td class="ct"r>'.$no++.'</td><td>'.$v['nama'].'</td>';
            $jml_absen = $data[$k]['absensi']['s']+$data[$k]['absensi']['i']+$data[$k]['absensi']['a'];
            $html .= '<td class="ctr">'.$data[$k]['absensi']['s'].'</td><td class="ctr">'.$data[$k]['absensi']['i'].'</td><td class="ctr">'.$data[$k]['absensi']['a'].'</td><td class="ctr">'.$jml_absen.'</td>'; 
            if (!empty($arr_id_ekstra)) {
                foreach ($arr_id_ekstra as $e) {
                    $nekstra = !empty($data[$k]['ekstra'][$e]['nilai']) ? $data[$k]['ekstra'][$e]['nilai'].' ('.$data[$k]['ekstra'][$e]['desk'].')' : "-";

                    $html .= '<td>'.$nekstra.'</td>';
                }
            }
        }
        $html .= '</table>';
        $this->d['html'] = $html;
        $this->load->view('cetak_ekstra', $this->d);
    }
}