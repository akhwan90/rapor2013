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
        $this->d['ta'] = substr($this->d['tasm'], 0, 4);
        $this->d['sm'] = substr($this->d['tasm'], 4, 1);
        $this->d['wk'] = $this->session->userdata('app_rapot_walikelas');
        $wali = $this->session->userdata($this->sespre."walikelas");
        $this->d['id_kelas'] = $wali['id_walikelas'];
        $this->d['nama_kelas'] = $wali['nama_walikelas'];

        $this->d['dw'] = $this->db->query("select 
                b.nama nmkelas, c.nama nmguru
                from t_walikelas a 
                inner join m_kelas b on a.id_kelas = b.id
                inner join m_guru c on a.id_guru = c.id
                where left(a.tasm,4) = '".$this->d['ta']."' and a.id_kelas = '".$this->d['id_kelas']."'")->row_array();
        
    }
    public function index() {
        $this->d['p'] = "landing";
        $this->load->view("template_utama", $this->d);
    }
    public function cetak() {
        $s_siswa = "select 
            a.id_siswa, b.nama
            from t_kelas_siswa a 
            inner join m_siswa b on a.id_siswa = b.id
            where a.id_kelas = '".$this->d['id_kelas']."' and a.ta = '".$this->d['ta']."'";

        $s_mapel = "select a.id, a.kd_singkat from m_mapel a order by a.id asc";

        $strq_np = "select 
                c.id_mapel, a.id_siswa, a.jenis, avg(a.nilai) nilai
                from t_nilai a 
                inner join t_kelas_siswa b on a.id_siswa = b.id_siswa
                inner join t_guru_mapel c on a.id_guru_mapel = c.id
                where b.id_kelas = '".$this->d['id_kelas']."' 
                and a.tasm = '".$this->d['tasm']."'
                group by c.id_mapel, a.id_siswa, a.jenis";
        $strq_nk = "select 
                 c.id_mapel, a.id_siswa, avg(a.nilai) nilai
                 from t_nilai_ket a 
                 inner join t_kelas_siswa b on a.id_siswa = b.id_siswa
                 inner join t_guru_mapel c on a.id_guru_mapel = c.id
                 where b.id_kelas = '".$this->d['id_kelas']."' 
                 and a.tasm = '".$this->d['tasm']."'
                 group by c.id_mapel, a.id_siswa";

        $queri_np = $this->db->query($strq_np)->result_array();
        $queri_nk = $this->db->query($strq_nk)->result_array();
        $queri_siswa = $this->db->query($s_siswa)->result_array();
        $queri_mapel = $this->db->query($s_mapel)->result_array();

        $data_np = array();
        foreach ($queri_np as $a) {
            $idx1 = $a['id_mapel'];
            $idx2 = $a['id_siswa'];
            $idx3 = $a['jenis'];
            $data_np[$idx1][$idx2][$idx3] = $a['nilai'];
        }

        $data_nk = array();
        foreach ($queri_nk as $a) {
            $idx1 = $a['id_mapel'];
            $idx2 = $a['id_siswa'];
            $data_nk[$idx1][$idx2] = $a['nilai'];
        }

        $html = '<p align="left"><b>LEGER NILAI PENGETAHUAN & KETERAMPILAN</b>
                <br>
                Kelas : '.$this->d['dw']['nmkelas'].', Nama Wali : '.$this->d['dw']['nmguru'].', Tahun Pelajaran '.$this->d['tasm'].'<hr style="border: solid 1px #000; margin-top: -10px"></p>';

        $html .= '<table class="table"><tr><td rowspan="2">Nama</td>';
        foreach ($queri_mapel as $m) {
            $html .= '<td colspan="2">'.$m['kd_singkat'].'</td>';
        }
        $html .= '<td rowspan="2">Jumlah</td>';
        $html .= '<td rowspan="2">Ranking</td></tr>';
        foreach ($queri_mapel as $m) {
            $html .= '<td>P</td><td>K</td>';
        }
        //$html .= '<td>P</td><td>K</td></tr>';

        foreach ($queri_siswa as $s) {
            $html .= '<tr><td>'.$s['nama'].'</td>';
            $jml_np = 0;
            $jml_nk = 0;
            foreach ($queri_mapel as $m) {
                // Nilai Pengetahuan
                $idx1 = $m['id'];
                $idx2 = $s['id_siswa'];
                $np_h = !empty($data_np[$idx1][$idx2]['h']) ? $data_np[$idx1][$idx2]['h'] : 0;
                $np_t = !empty($data_np[$idx1][$idx2]['t']) ? $data_np[$idx1][$idx2]['t'] : 0;
                $np_a = !empty($data_np[$idx1][$idx2]['a']) ? $data_np[$idx1][$idx2]['a'] : 0;
                $p_h = $this->config->item('pnp_h');
                $p_t = $this->config->item('pnp_t');
                $p_a = $this->config->item('pnp_a');
                $jml = $p_h+$p_t+$p_a;

                $p_h = ($p_h / $jml) * 100; 
                $p_t = ($p_t / $jml) * 100; 
                $p_a = ($p_a / $jml) * 100; 
                
                $np = number_format((($np_h * $p_h) + ($np_t * $p_t) + ($np_a * $p_a)) / 100);
                $jml_np = $jml_np + $np;

                // Nilai Ketrampilan
                $nk = !empty($data_nk[$idx1][$idx2]) ? number_format($data_nk[$idx1][$idx2]) : 0;
                $jml_nk = $jml_nk + $nk;

                $html .= '<td class="ctr">'.($np).'</td><td class="ctr">'.($nk).'</td>';
            }
            $html .= '<td class="ctr">'.($jml_np+$jml_nk).'</td><td></td></tr>';
        }

        $html .= '</table>';

        /*
        echo $html;
        exit;


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
        */

        $d['html'] = $html;
        $d['teks_tasm'] = "Tahun Ajaran ".$this->d['ta']."/".($this->d['ta']+1).", Semester ".$this->d['sm'];
        //j($d);
        //$this->load->view('cetak', $d);

        $mauke = $this->uri->segment(3);

        if (empty($mauke)) {
            $this->load->view('cetak', $d);
        } else if ($mauke == "print") {
            $this->load->view('cetak', $d);
        } else if ($mauke == "excel") {
            $this->load->view('cetak', $d);
            $filename = "leger_" . date('YmdHis') . ".xls";
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: application/vnd.ms-excel");
        }
    }
    public function cetak_ekstra() {
        $s_siswa = "select 
            a.id_siswa, b.nama
            from t_kelas_siswa a 
            inner join m_siswa b on a.id_siswa = b.id
            where a.id_kelas = '".$this->d['id_kelas']."' and a.ta = '".$this->d['ta']."'";

        $s_mapel = "select a.id, a.nama from m_ekstra a order by a.id asc";

        $strq_n_ekstra = "select 
                a.id_siswa ids, a.id_ekstra, a.nilai, a.desk
                from t_nilai_ekstra a 
                inner join t_kelas_siswa b on a.id_siswa = b.id_siswa
                where 
                b.id_kelas = '".$this->d['id_kelas']."' 
                and a.tasm = '".$this->d['tasm']."' 
                and b.ta = '".$this->d['ta']."'";
        $strq_n_absen = "select 
                a.id_siswa, a.s, a.i, a.a
                from t_nilai_absensi a 
                inner join t_kelas_siswa b on a.id_siswa = b.id_siswa
                where 
                b.id_kelas = '".$this->d['id_kelas']."' 
                and a.tasm = '".$this->d['tasm']."' 
                and b.ta = '".$this->d['ta']."'";

        $queri_ne = $this->db->query($strq_n_ekstra)->result_array();
        $queri_na = $this->db->query($strq_n_absen)->result_array();
        $queri_siswa = $this->db->query($s_siswa)->result_array();
        $queri_mapel = $this->db->query($s_mapel)->result_array();

        $data_ne = array();
        foreach ($queri_ne as $a) {
            $idx1 = $a['id_ekstra'];
            $idx2 = $a['ids'];
            $data_ne[$idx1][$idx2]['nilai'] = $a['nilai'];
            $data_ne[$idx1][$idx2]['desk'] = $a['desk'];
        } 

        $data_na = array();
        foreach ($queri_na as $a) {
            $idx1 = $a['id_siswa'];
            $data_na[$idx1]['s'] = $a['s'];
            $data_na[$idx1]['i'] = $a['i'];
            $data_na[$idx1]['a'] = $a['a'];
        }

        $html = '<p align="left"><b>LEGER NILAI EKSTRAKURIKULER & ABSENSI</b>
                <br>
                Kelas : '.$this->d['dw']['nmkelas'].', Nama Wali : '.$this->d['dw']['nmguru'].', Tahun Pelajaran '.$this->d['tasm'].'<hr style="border: solid 1px #000; margin-top: -10px"></p>';
        $html .= '<table class="table"><tr><td rowspan="2">Nama</td>';
        foreach ($queri_mapel as $m) {
            $html .= '<td colspan="2">'.$m['nama'].'</td>';
        }
        $html .= '<td>S</td><td>I</td><td>A</td><tr>';

        foreach ($queri_siswa as $s) {
            $html .= '<tr><td>'.$s['nama'].'</td>';
            $id_siswa = $s['id_siswa'];
            foreach ($queri_mapel as $m) {
                $id_ekstra = $m['id'];
                $nilai = !empty($data_ne[$id_ekstra][$id_siswa]['nilai']) ? $data_ne[$id_ekstra][$id_siswa]['nilai'] : '-';
                $desk = !empty($data_ne[$id_ekstra][$id_siswa]['desk']) ? $data_ne[$id_ekstra][$id_siswa]['desk'] : '-';

                $html .= '
                <td class="ctr">'.$nilai.'</td>
                <td class="ctr">'.$desk.'</td>';
            }

            $s = !empty($data_na[$id_siswa]['s']) ? $data_na[$id_siswa]['s'] : '-';
            $i = !empty($data_na[$id_siswa]['i']) ? $data_na[$id_siswa]['i'] : '-';
            $a = !empty($data_na[$id_siswa]['a']) ? $data_na[$id_siswa]['a'] : '-';

            $html .= '
            <td class="ctr">'.$s.'</td>
            <td class="ctr">'.$i.'</td>
            <td class="ctr">'.$a.'</td>
            </tr>';
        }

        $html .= '</table>';


        /*

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
        */
        $this->d['html'] = $html;
        $this->load->view('cetak_ekstra', $this->d);
    }
}