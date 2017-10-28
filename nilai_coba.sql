DELIMITER $$
--
-- Prosedur
--
CREATE PROCEDURE `n_pengetahuan` (IN `id_mapel` INT, IN `id_kelas` INT)  BEGIN
	SET @sql = NULL;
	
	SELECT
	IFNULL(GROUP_CONCAT(DISTINCT CONCAT('CONCAT(SUM(IF(a.id_mapel_kd = ',a.id,' AND a.id_siswa = b.id,nilai,0)),\'-\',\'',a.nama_kd,'\') ',REPLACE(REPLACE(no_kd,'-','_'),' ','_'))),'') INTO @sql
	FROM t_mapel_kd a
	INNER JOIN m_kelas b ON a.tingkat = b.tingkat
	WHERE a.id_mapel = id_mapel AND b.id = id_kelas AND a.jenis = 'P';
	
	IF @sql = '' THEN SET @sql = '';
	ELSEIF @sql != '' THEN SET @sql = CONCAT(', ',@sql);
	END IF;
		
	SET @sql = concat('SELECT b.nama ', @sql, ', 
							SUM(IF(e.id_mapel = ',id_mapel,' AND a.jenis = \'t\' AND a.id_siswa = b.id,nilai,0)) nilai_uts,
							SUM(IF(e.id_mapel = ',id_mapel,' AND a.jenis = \'a\' AND a.id_siswa = b.id,nilai,0)) nilai_uas
							FROM t_nilai a
							INNER JOIN m_siswa b ON a.id_siswa = b.id
							INNER JOIN t_mapel_kd c ON a.id_mapel_kd = c.id
							INNER JOIN t_guru_mapel e ON a.id_guru_mapel = e.id
							WHERE e.id_mapel = ',id_mapel,' AND e.id_kelas = ',id_kelas,'
							GROUP BY a.id_siswa');
	
		
	PREPARE stmt FROM @sql;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END$$

CREATE PROCEDURE `r_nilai_keterampilan` (IN `id_mapel` INT, IN `id_kelas` INT)  BEGIN
	SET @sql = NULL;
	
	SELECT
	IFNULL(GROUP_CONCAT(DISTINCT CONCAT('CONCAT(SUM(IF(a.id_mapel_kd = ',a.id,' AND a.id_siswa = b.id,nilai,0)),\'///\',\'',a.nama_kd,'\') ',REPLACE(REPLACE(no_kd,'-','_'),' ','_'))),'') INTO @sql
	FROM t_mapel_kd a
	INNER JOIN m_kelas b ON a.tingkat = b.tingkat
	WHERE a.id_mapel = id_mapel AND b.id = id_kelas AND a.jenis = 'K';
	
	IF @sql = '' THEN SET @sql = '';
	ELSEIF @sql != '' THEN SET @sql = CONCAT(', ',@sql);
	END IF;
		
	SET @sql = concat('SELECT b.nama ', @sql, '
							FROM t_nilai_ket a
							INNER JOIN m_siswa b ON a.id_siswa = b.id
							INNER JOIN t_mapel_kd c ON a.id_mapel_kd = c.id
							INNER JOIN t_guru_mapel e ON a.id_guru_mapel = e.id
							WHERE e.id_mapel = ',id_mapel,' AND e.id_kelas = ',id_kelas,' AND c.jenis = \'K\'
							GROUP BY a.id_siswa');
	
		
	PREPARE stmt FROM @sql;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_admin`
--

CREATE TABLE `m_admin` (
  `id` int(4) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `level` enum('admin','guru','siswa') NOT NULL,
  `konid` varchar(10) NOT NULL,
  `aktif` enum('Y','N') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `m_admin`
--

INSERT INTO `m_admin` (`id`, `username`, `password`, `level`, `konid`, `aktif`) VALUES
(1, 'admin', 'bd2c38a321c5920f36d7cd9d4e77eeee64c76c6a', 'admin', '0', 'Y'),
(42, 'agussp', 'fefdd621d35d14c299aef2fcae34d3dfe9b2f12b', 'guru', '11', 'Y'),
(43, 'budi', 'fefdd621d35d14c299aef2fcae34d3dfe9b2f12b', 'guru', '17', 'Y'),
(44, 'candra', 'fefdd621d35d14c299aef2fcae34d3dfe9b2f12b', 'guru', '7', 'Y'),
(45, 'dewi', 'fefdd621d35d14c299aef2fcae34d3dfe9b2f12b', 'guru', '10', 'Y'),
(46, 'eni', 'fefdd621d35d14c299aef2fcae34d3dfe9b2f12b', 'guru', '15', 'Y'),
(47, 'fuad', 'fefdd621d35d14c299aef2fcae34d3dfe9b2f12b', 'guru', '4', 'Y'),
(48, 'ghani', 'fefdd621d35d14c299aef2fcae34d3dfe9b2f12b', 'guru', '21', 'Y');

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_ekstra`
--

CREATE TABLE `m_ekstra` (
  `id` int(2) NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `m_ekstra`
--

INSERT INTO `m_ekstra` (`id`, `nama`) VALUES
(1, 'Pramuka'),
(2, 'Baca Tulis Al Quran'),
(3, 'Pertanian'),
(4, 'Qiroah'),
(5, 'Drumband'),
(6, 'Bulu Tangkis'),
(7, 'Tenis Meja'),
(8, 'Karawitan'),
(9, 'Catur');

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_guru`
--

CREATE TABLE `m_guru` (
  `id` int(3) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `jk` enum('L','P') DEFAULT NULL,
  `is_bk` enum('2','1') DEFAULT NULL,
  `stat_data` enum('A','P','M') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `m_guru`
--

INSERT INTO `m_guru` (`id`, `nama`, `nip`, `jk`, `is_bk`, `stat_data`) VALUES
(4, 'Fuad', '1205', 'L', '1', 'A'),
(7, 'Candra', '1202', 'P', '2', 'A'),
(10, 'Dewi', '1204', 'L', '2', 'A'),
(11, 'Agus, S.Pd', '1200', 'L', '2', 'A'),
(15, 'Eni', '1204', 'L', '2', 'A'),
(17, 'Budi', '1201', 'P', '2', 'A'),
(21, 'Ghani', '1206', NULL, '2', 'A');

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_kelas`
--

CREATE TABLE `m_kelas` (
  `id` int(3) NOT NULL,
  `tingkat` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `m_kelas`
--

INSERT INTO `m_kelas` (`id`, `tingkat`, `nama`) VALUES
(1, 7, 'VII a'),
(2, 7, 'VII b'),
(3, 7, 'VII c'),
(4, 8, 'VIII a'),
(6, 8, 'VIII c'),
(7, 9, 'IX a'),
(8, 9, 'IX b'),
(9, 9, 'IX c'),
(10, 8, 'VIII b');

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_mapel`
--

CREATE TABLE `m_mapel` (
  `id` int(3) NOT NULL,
  `kelompok` enum('A','B') NOT NULL,
  `tambahan_sub` enum('NO','PAI','MULOK') NOT NULL,
  `kd_singkat` varchar(5) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `is_sikap` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `m_mapel`
--

INSERT INTO `m_mapel` (`id`, `kelompok`, `tambahan_sub`, `kd_singkat`, `nama`, `is_sikap`) VALUES
(1, 'A', 'PAI', 'QH', 'Al-Quran Hadis', '1'),
(2, 'A', 'PAI', 'AA', 'Akidah Akhlak', '1'),
(3, 'A', 'PAI', 'FQ', 'Fiqih', '1'),
(4, 'A', 'PAI', 'SKI', 'Sejarah Kebudayaan Islam', '1'),
(5, 'A', 'NO', 'PKN', 'Pendidikan Pancasila dan Kewarganegaraan', '1'),
(6, 'A', 'NO', 'B.IND', 'Bahasa Indonesia', '0'),
(7, 'A', 'NO', 'B.ARB', 'Bahasa Arab', '0'),
(8, 'A', 'NO', 'MTK', 'Matematika', '0'),
(9, 'A', 'NO', 'IPA', 'Ilmu Pengetahuan Alam', '0'),
(10, 'A', 'NO', 'IPS', 'Ilmu Pengetahuan Sosial', '0'),
(11, 'A', 'NO', 'B.ING', 'Bahasa Inggris', '0'),
(12, 'B', 'NO', 'SBUD', 'Seni Budaya', '0'),
(13, 'B', 'NO', 'PJKES', 'Pendidikan Jasmani, Olahraga, dan Kesehatan', '0'),
(14, 'B', 'NO', 'PKRY', 'Prakarya', '0'),
(15, 'B', 'MULOK', 'B.JWA', 'Bahasa Jawa', '0'),
(16, 'B', 'MULOK', 'TFZ', 'Tahfidz', '0');

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_siswa`
--

CREATE TABLE `m_siswa` (
  `id` int(6) NOT NULL,
  `nis` varchar(10) NOT NULL DEFAULT '0',
  `nisn` varchar(10) NOT NULL DEFAULT '0',
  `nama` varchar(100) NOT NULL,
  `jk` enum('L','P') NOT NULL,
  `tmp_lahir` varchar(50) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `agama` varchar(10) NOT NULL,
  `status` varchar(2) NOT NULL,
  `anakke` int(2) NOT NULL,
  `alamat` varchar(50) NOT NULL,
  `notelp` varchar(13) NOT NULL,
  `sek_asal` varchar(30) NOT NULL,
  `sek_asal_alamat` varchar(50) NOT NULL,
  `diterima_kelas` varchar(5) NOT NULL,
  `diterima_tgl` date NOT NULL,
  `diterima_smt` varchar(2) NOT NULL,
  `ijazah_no` varchar(50) NOT NULL,
  `ijazah_thn` varchar(4) NOT NULL,
  `skhun_no` varchar(50) NOT NULL,
  `skhun_thn` varchar(4) NOT NULL,
  `ortu_ayah` varchar(50) NOT NULL,
  `ortu_ibu` varchar(50) NOT NULL,
  `ortu_alamat` varchar(50) NOT NULL,
  `ortu_notelp` varchar(13) NOT NULL,
  `ortu_ayah_pkj` varchar(30) NOT NULL,
  `ortu_ibu_pkj` varchar(30) NOT NULL,
  `wali` varchar(20) NOT NULL,
  `wali_alamat` varchar(50) NOT NULL,
  `notelp_rumah` varchar(13) NOT NULL,
  `wali_pkj` varchar(13) NOT NULL,
  `inputID` int(2) NOT NULL,
  `tgl_input` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tgl_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stat_data` enum('A','K','M','L') NOT NULL,
  `foto` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `m_siswa`
--

INSERT INTO `m_siswa` (`id`, `nis`, `nisn`, `nama`, `jk`, `tmp_lahir`, `tgl_lahir`, `agama`, `status`, `anakke`, `alamat`, `notelp`, `sek_asal`, `sek_asal_alamat`, `diterima_kelas`, `diterima_tgl`, `diterima_smt`, `ijazah_no`, `ijazah_thn`, `skhun_no`, `skhun_thn`, `ortu_ayah`, `ortu_ibu`, `ortu_alamat`, `ortu_notelp`, `ortu_ayah_pkj`, `ortu_ibu_pkj`, `wali`, `wali_alamat`, `notelp_rumah`, `wali_pkj`, `inputID`, `tgl_input`, `tgl_update`, `stat_data`, `foto`) VALUES
(1, '1001', '1001100101', 'Agus', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', ''),
(2, '1002', '1001100202', 'Budi', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', ''),
(3, '1003', '1001100303', 'Candy', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', ''),
(4, '1004', '1001100404', 'Dedi', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', ''),
(5, '1005', '1001100505', 'Enda', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', ''),
(6, '1006', '1001100606', 'Fani', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', ''),
(7, '1007', '1001100707', 'Gandi', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', ''),
(8, '1008', '1001100808', 'Hadi', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', ''),
(9, '1009', '1001100909', 'Ina', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', ''),
(10, '1010', '1001101010', 'Joni', '', '', '0000-00-00', '', '', 0, '', '', '', '', '', '2015-01-01', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '2017-10-28 05:53:36', '0000-00-00 00:00:00', 'A', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tahun`
--

CREATE TABLE `tahun` (
  `id` int(3) NOT NULL,
  `tahun` varchar(5) NOT NULL,
  `aktif` enum('Y','N') NOT NULL,
  `nama_kepsek` varchar(50) NOT NULL,
  `nip_kepsek` varchar(30) NOT NULL,
  `tgl_raport` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tahun`
--

INSERT INTO `tahun` (`id`, `tahun`, `aktif`, `nama_kepsek`, `nip_kepsek`, `tgl_raport`) VALUES
(1, '20161', 'N', 'Drs. Agung Simanjuntak', '1199', '2017-07-17'),
(2, '20162', 'N', '', '', '0000-00-00'),
(3, '20171', 'Y', '', '', '0000-00-00'),
(4, '20172', 'N', '', '', '0000-00-00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_guru_mapel`
--

CREATE TABLE `t_guru_mapel` (
  `id` int(6) NOT NULL,
  `tasm` varchar(5) DEFAULT NULL,
  `id_guru` int(3) NOT NULL,
  `id_kelas` int(3) NOT NULL,
  `id_mapel` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_kelas_siswa`
--

CREATE TABLE `t_kelas_siswa` (
  `id` int(5) NOT NULL,
  `id_kelas` int(5) NOT NULL,
  `id_siswa` int(5) NOT NULL,
  `ta` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `t_kelas_siswa`
--

INSERT INTO `t_kelas_siswa` (`id`, `id_kelas`, `id_siswa`, `ta`) VALUES
(1, 1, 1, 2017),
(2, 1, 2, 2017),
(3, 2, 3, 2017),
(4, 2, 4, 2017),
(5, 2, 5, 2017);

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_mapel_kd`
--

CREATE TABLE `t_mapel_kd` (
  `id` int(6) UNSIGNED NOT NULL,
  `id_guru` int(6) UNSIGNED NOT NULL DEFAULT '0',
  `id_mapel` int(6) NOT NULL,
  `tingkat` int(2) NOT NULL,
  `semester` enum('0','1','2') NOT NULL,
  `no_kd` varchar(5) NOT NULL,
  `jenis` enum('P','K','SSp','SSo') NOT NULL,
  `bobot` int(2) NOT NULL,
  `nama_kd` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_naikkelas`
--

CREATE TABLE `t_naikkelas` (
  `id` int(6) NOT NULL,
  `id_siswa` int(6) NOT NULL,
  `ta` year(4) NOT NULL,
  `naik` enum('Y','N') NOT NULL,
  `catatan_wali` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_nilai`
--

CREATE TABLE `t_nilai` (
  `id` int(6) NOT NULL,
  `tasm` varchar(5) NOT NULL DEFAULT '0',
  `jenis` enum('h','t','a') NOT NULL,
  `id_guru_mapel` int(6) NOT NULL,
  `id_mapel_kd` int(6) NOT NULL,
  `id_siswa` int(6) NOT NULL,
  `nilai` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_nilai_absensi`
--

CREATE TABLE `t_nilai_absensi` (
  `id` int(6) NOT NULL,
  `tasm` varchar(5) NOT NULL,
  `id_siswa` int(6) NOT NULL,
  `s` int(3) NOT NULL,
  `i` int(3) NOT NULL,
  `a` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_nilai_ekstra`
--

CREATE TABLE `t_nilai_ekstra` (
  `id` int(6) NOT NULL,
  `tasm` varchar(5) DEFAULT NULL,
  `id_ekstra` int(3) DEFAULT NULL,
  `id_siswa` int(6) DEFAULT NULL,
  `nilai` char(2) DEFAULT NULL,
  `desk` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_nilai_ket`
--

CREATE TABLE `t_nilai_ket` (
  `id` int(6) NOT NULL,
  `tasm` varchar(5) NOT NULL,
  `id_guru_mapel` int(6) NOT NULL,
  `id_mapel_kd` int(6) NOT NULL,
  `id_siswa` int(6) NOT NULL,
  `nilai` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_nilai_sikap_so`
--

CREATE TABLE `t_nilai_sikap_so` (
  `id` int(6) NOT NULL,
  `tasm` varchar(5) DEFAULT NULL,
  `id_guru_mapel` int(6) DEFAULT NULL,
  `id_siswa` int(6) DEFAULT NULL,
  `is_wali` enum('Y','N') DEFAULT NULL,
  `selalu` varchar(50) DEFAULT NULL,
  `mulai_meningkat` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_nilai_sikap_sp`
--

CREATE TABLE `t_nilai_sikap_sp` (
  `id` int(6) NOT NULL,
  `tasm` varchar(5) DEFAULT NULL,
  `id_guru_mapel` int(6) DEFAULT NULL,
  `id_siswa` int(6) DEFAULT NULL,
  `is_wali` enum('Y','N') DEFAULT NULL,
  `selalu` varchar(50) DEFAULT NULL,
  `mulai_meningkat` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_prestasi`
--

CREATE TABLE `t_prestasi` (
  `id` int(6) NOT NULL,
  `id_siswa` int(6) NOT NULL,
  `ta` year(4) NOT NULL,
  `jenis` varchar(100) NOT NULL,
  `keterangan` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_walikelas`
--

CREATE TABLE `t_walikelas` (
  `id` int(3) NOT NULL,
  `tasm` varchar(5) DEFAULT NULL,
  `id_guru` int(2) DEFAULT NULL,
  `id_kelas` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_admin`
--
ALTER TABLE `m_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_ekstra`
--
ALTER TABLE `m_ekstra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_guru`
--
ALTER TABLE `m_guru`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_kelas`
--
ALTER TABLE `m_kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_mapel`
--
ALTER TABLE `m_mapel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_siswa`
--
ALTER TABLE `m_siswa`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `tahun`
--
ALTER TABLE `tahun`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_guru_mapel`
--
ALTER TABLE `t_guru_mapel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD PRIMARY KEY (`id_kelas`,`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `t_mapel_kd`
--
ALTER TABLE `t_mapel_kd`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_mapel` (`id_mapel`),
  ADD KEY `id_guru` (`id_guru`);

--
-- Indexes for table `t_naikkelas`
--
ALTER TABLE `t_naikkelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_nilai`
--
ALTER TABLE `t_nilai`
  ADD PRIMARY KEY (`tasm`,`jenis`,`id_guru_mapel`,`id_mapel_kd`,`id_siswa`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `t_nilai_absensi`
--
ALTER TABLE `t_nilai_absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `t_nilai_ekstra`
--
ALTER TABLE `t_nilai_ekstra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ekstra` (`id_ekstra`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `t_nilai_ket`
--
ALTER TABLE `t_nilai_ket`
  ADD PRIMARY KEY (`tasm`,`id_guru_mapel`,`id_mapel_kd`,`id_siswa`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `t_nilai_sikap_so`
--
ALTER TABLE `t_nilai_sikap_so`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru_mapel` (`id_guru_mapel`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `t_nilai_sikap_sp`
--
ALTER TABLE `t_nilai_sikap_sp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru_mapel` (`id_guru_mapel`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `t_prestasi`
--
ALTER TABLE `t_prestasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_walikelas`
--
ALTER TABLE `t_walikelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_admin`
--
ALTER TABLE `m_admin`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `m_ekstra`
--
ALTER TABLE `m_ekstra`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `m_guru`
--
ALTER TABLE `m_guru`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `m_kelas`
--
ALTER TABLE `m_kelas`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `m_mapel`
--
ALTER TABLE `m_mapel`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `m_siswa`
--
ALTER TABLE `m_siswa`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `tahun`
--
ALTER TABLE `tahun`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `t_guru_mapel`
--
ALTER TABLE `t_guru_mapel`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `t_mapel_kd`
--
ALTER TABLE `t_mapel_kd`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_naikkelas`
--
ALTER TABLE `t_naikkelas`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_nilai`
--
ALTER TABLE `t_nilai`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_nilai_absensi`
--
ALTER TABLE `t_nilai_absensi`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_nilai_ekstra`
--
ALTER TABLE `t_nilai_ekstra`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_nilai_ket`
--
ALTER TABLE `t_nilai_ket`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_nilai_sikap_so`
--
ALTER TABLE `t_nilai_sikap_so`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_nilai_sikap_sp`
--
ALTER TABLE `t_nilai_sikap_sp`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_prestasi`
--
ALTER TABLE `t_prestasi`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_walikelas`
--
ALTER TABLE `t_walikelas`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;
--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `t_guru_mapel`
--
ALTER TABLE `t_guru_mapel`
  ADD CONSTRAINT `FK_t_guru_mapel_m_guru` FOREIGN KEY (`id_guru`) REFERENCES `m_guru` (`id`),
  ADD CONSTRAINT `FK_t_guru_mapel_m_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `m_kelas` (`id`),
  ADD CONSTRAINT `FK_t_guru_mapel_m_mapel` FOREIGN KEY (`id_mapel`) REFERENCES `m_mapel` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD CONSTRAINT `t_kelas_siswa_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `m_kelas` (`id`),
  ADD CONSTRAINT `t_kelas_siswa_ibfk_2` FOREIGN KEY (`id_siswa`) REFERENCES `m_siswa` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_nilai_ekstra`
--
ALTER TABLE `t_nilai_ekstra`
  ADD CONSTRAINT `FK_t_nilai_ekstra_m_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `m_siswa` (`id`),
  ADD CONSTRAINT `t_nilai_ekstra_ibfk_1` FOREIGN KEY (`id_ekstra`) REFERENCES `m_ekstra` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_nilai_sikap_so`
--
ALTER TABLE `t_nilai_sikap_so`
  ADD CONSTRAINT `FK_t_nilai_sikap_so_m_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `m_siswa` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_nilai_sikap_sp`
--
ALTER TABLE `t_nilai_sikap_sp`
  ADD CONSTRAINT `FK_t_nilai_sikap_sp_m_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `m_siswa` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_walikelas`
--
ALTER TABLE `t_walikelas`
  ADD CONSTRAINT `FK_t_walikelas_m_guru` FOREIGN KEY (`id_guru`) REFERENCES `m_guru` (`id`),
  ADD CONSTRAINT `FK_t_walikelas_m_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `m_kelas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
