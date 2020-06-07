<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "controllers/Master.php");

class Cetak_leger extends Master {
    function __construct() {
        parent::__construct();
        cek_aktif();

        $akses = array("guru");
        cek_hak_akses($this->d['s']['level'], $akses);
        
        $this->d['url'] = "cetak_leger";        
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
            where a.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' and a.ta = '".$this->d['c']['ta_tahun']."'";

        $s_mapel = "select a.id, a.kd_singkat from m_mapel a order by a.id asc";

        $strq_np = "select 
                c.id_mapel, a.id_siswa, a.jenis, avg(a.nilai) nilai
                from t_nilai a 
                inner join t_kelas_siswa b on a.id_siswa = b.id_siswa
                inner join t_guru_mapel c on a.id_guru_mapel = c.id
                where b.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' 
                and a.tasm = '".$this->d['c']['ta_tasm']."'
                group by c.id_mapel, a.id_siswa, a.jenis";
        $strq_nk = "select 
                 c.id_mapel, a.id_siswa, avg(a.nilai) nilai
                 from t_nilai_ket a 
                 inner join t_kelas_siswa b on a.id_siswa = b.id_siswa
                 inner join t_guru_mapel c on a.id_guru_mapel = c.id
                 where b.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' 
                 and a.tasm = '".$this->d['c']['ta_tasm']."'
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
                Kelas : '.$this->d['s']['walikelas']['nama_walikelas'].', Nama Wali : '.$this->d['s']['nama'].', Tahun Pelajaran '.$this->d['c']['ta_tasm'].'<hr style="border: solid 1px #000; margin-top: -10px"></p>';

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

        $d['html'] = $html;
        $d['teks_tasm'] = "Tahun Ajaran ".$this->d['c']['ta_tahun']."/".($this->d['c']['ta_tahun']+1).", Semester ".$this->d['c']['ta_semester'];

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
            where a.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' and a.ta = '".$this->d['c']['ta_tahun']."'";

        $s_mapel = "select a.id, a.nama from m_ekstra a order by a.id asc";

        $strq_n_ekstra = "select 
                a.id_siswa ids, a.id_ekstra, a.nilai, a.desk
                from t_nilai_ekstra a 
                inner join t_kelas_siswa b on a.id_siswa = b.id_siswa
                where 
                b.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' 
                and a.tasm = '".$this->d['c']['ta_tasm']."' 
                and b.ta = '".$this->d['c']['ta_tahun']."'";
        $strq_n_absen = "select 
                a.id_siswa, a.s, a.i, a.a
                from t_nilai_absensi a 
                inner join t_kelas_siswa b on a.id_siswa = b.id_siswa
                where 
                b.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' 
                and a.tasm = '".$this->d['c']['ta_tasm']."' 
                and b.ta = '".$this->d['c']['ta_tahun']."'";

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
                Kelas : '.$this->d['s']['walikelas']['nama_walikelas'].', Nama Wali : '.$this->d['s']['nama'].', Tahun Pelajaran '.$this->d['c']['ta_tasm'].'<hr style="border: solid 1px #000; margin-top: -10px"></p>';
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
                                        WHERE b.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' AND a.tasm = '".$this->d['c']['ta_tasm']."'")->result_array();
        $q_ekstra = $this->db->query("SELECT 
                                        a.*
                                        FROM t_nilai_ekstra a 
                                        INNER JOIN t_kelas_siswa b ON a.id_siswa = b.id_siswa
                                        WHERE b.id_kelas = '".$this->d['s']['walikelas']['id_walikelas']."' AND a.tasm = '".$this->d['c']['ta_tasm']."'")->result_array();
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