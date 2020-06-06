<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class N_keterampilan_model extends CI_Model {

    public function gen_nilai($bawa) {
        $strq_detail_guru = "select 
                a.tasm, b.nama nmguru, c.nama nmkelas, d.nama nmmapel,
                a.id idgurumapel, b.id idguru, c.id idkelas, d.id idmapel, c.tingkat
                from t_guru_mapel a
                inner join m_guru b on a.id_guru = b.id
                inner join m_kelas c on a.id_kelas = c.id
                inner join m_mapel d on a.id_mapel = d.id
                where a.id = '".$bawa."'";
        $detil_guru = $this->db->query($strq_detail_guru)->row_array();

        $ret['meta'] = $detil_guru;


        $ta = substr($detil_guru['tasm'],0,4);

        $strq_np = "select 
                a.id_siswa, a.id_mapel_kd, a.nilai
                from t_nilai_ket a 
                where a.id_guru_mapel = '".$bawa."'
                group by a.id_siswa, a.id_mapel_kd";

        
        $strq_kd = "SELECT 
                    a.id, a.no_kd
                    FROM t_mapel_kd a 
                    WHERE a.id_mapel = ".$detil_guru['idmapel']."
                    AND a.tingkat = ".$detil_guru['tingkat']." 
                    AND a.semester = '".$this->d['c']['ta_semester']."'
                    AND a.jenis = 'K'";


        $strq_siswa = "select
                b.id_siswa, c.nama
                from t_guru_mapel a 
                inner join t_kelas_siswa b on a.id_kelas = b.id_kelas
                inner join m_siswa c on b.id_siswa = c.id
                where a.id = '".$bawa."' and b.ta = '".$ta."'";

        $queri_np = $this->db->query($strq_np)->result_array();
        $queri_kd = $this->db->query($strq_kd)->result_array();
        $jml_kd = $this->db->query($strq_kd)->num_rows();
        $queri_siswa = $this->db->query($strq_siswa)->result_array();

        $data_siswa = array();
        if (!empty($queri_siswa)) {
            foreach ($queri_siswa as $qs) {
                $idx = $qs['id_siswa'];
                $data_siswa[$idx] = ['id'=>$idx, 'nama'=>$qs['nama']];
            }
        }

        $data_kd = array();
        if (!empty($queri_kd)) {
            foreach ($queri_kd as $qk) {
                $idx = $qk['id'];
                $data_kd[$idx] = ['id'=>$idx, 'nama'=>$qk['no_kd']];
            }
        }


        $data_np = array();
        foreach ($queri_np as $a) {
            $idx1 = $a['id_siswa'];
            $idx3 = $a['id_mapel_kd'];
            $data_np[$idx1][$idx3] = floatval($a['nilai']);
        }
        $ret['data_np'] = $data_np;
        $ret['data_kd'] = $data_kd;
        $ret['data_siswa'] = $data_siswa;

        return $ret;
    }

}