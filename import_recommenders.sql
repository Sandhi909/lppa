-- Script Import Data Recommenders (Wali Kelas & Wali Santri)
-- Menghubungkan user_id dari tabel users dengan data recommenders
-- Mapping unit: SD=3, SMP=4, SMA=5
-- Wali Santri: unit_id = NULL

START TRANSACTION;

-- Wali Santri (PSAM) - unit_id = NULL
INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'AAM SYAMSIAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'aam.syamsiah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'ASEP RAHMAN', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'asep.rahman';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'AGUS YUDI ALI FIRDAUS', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'agus.yudi.ali.firdaus';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'ALIFA TURROSYIDAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'alifa.turrosyidah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'AYEP MULYA BARKAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'ayep.mulya.barkah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'DADANG DIMYATI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'dadang.dimyati';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'DINI PURNAMA NURUL AJI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'dini.purnama.nurul.aji';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'ELA N H', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'ela.n.h';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'ELIS SUMYATI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'elis.sumyati';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'F. NIZAR', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'f.nizar';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'FERRY FERDIANSYAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'ferry.ferdiansyah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'FITRIADI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'fitriadi';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'HAMDANI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'hamdani';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'HILMAN', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'hilman';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'HUDA AHMAD SYARIF', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'huda.ahmad.syarif';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'IDA HAMIDAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'ida.hamidah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'IMA SRI YUNIANI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'ima.sri.yuniani';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'ITANG RUSTANDI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'itang.rustandi';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'LUKMANUL HAKIM', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'lukmanul.hakim';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'M. IQBAL SYEKHABUDIN', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'm.iqbal.syekhabudin';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'MIA NUR ASIAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'mia.nur.asiah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'ROSLIANAWATI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'roslianawati';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SITI INDIRA MARYAM', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'siti.indira.maryam';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SRI RODIAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'sri.rodiah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SANTI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'santi';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'RYSNA', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'rysna';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'NUNUNG KARTINI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'nunung.kartini';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'YAYAH HERYATI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif'
FROM users WHERE username = 'yayah.heryati';

-- Wali Kelas - SD (unit_id = 3)
INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'TATAN HERAWATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'tatan.herawati';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'TINTIN AGUSTIANA', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'tintin.agustiana';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SUSAN MARIAM', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'susan.mariam';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'RAISA INDAH FARDINI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'raisa.indah.fardini';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'RYAN FEBRIAN', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'ryan.febrian';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'YULLI MELANIAWATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'yulli.melaniawati';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SUSILOWATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'susilowati';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'HENI HARYANI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'heni.haryani';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'AI MULYANI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'ai.mulyani';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'TENI WARTINI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'teni.wartini';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'RD.ELIH JAMILAH', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'rdelih.jamilah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'ABILLA PRIMA HASTUTI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'abilla.prima.hastuti';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'YATI KURHAYATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'yati.kurhayati';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'AYI KANA', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'ayi.kana';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'LILIS LISNAWATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'lilis.lisnawati';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SRI WULANDARI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'sri.wulandari';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'WIWI WIHARYATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'wiwi.wiharyati';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'INDAYANI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'indayani';

-- Wali Kelas - SMP (unit_id = 4)
INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'RIKI AHMAD ZAELANI, S.Si.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'riki.ahmad.zaelani.ssi';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'VANNY GUSTIKASARI, S.Pd., M.M.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'vanny.gustikasari.spd.mm';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Hj. KUSMAYATI, S.Pd.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'hj.kusmayati.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Dra. IPAH SARIPAH', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'dra.ipah.saripah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'ELIS MASITOH, S.P.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'elis.masitoh.sp';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'WINA NURNINGSARI, M.Pd.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'wina.nurningsari.mpd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Drs. RONY HERMANSYAH', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'drs.rony.hermansyah';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'HANNY HADIYANTI, S.Pd.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'hanny.hadiyanti.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'NELA NAYLA, S. Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'nela.nayla.s.pd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SUCI LESTARI, M. Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'suci.lestari.m.pd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'CHAERUL WILDAN, S.Pd.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'chaerul.wildan.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'KURNIAWATI, S.Ag.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'kurniawati.sag';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'RITA PUSPITASARI, S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'rita.puspitasari.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'TINTIN SURTINI, Dra', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'tintin.surtini.dra';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'EUIS NURYANTI DEWI, S.TP', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'euis.nuryanti.dewi.stp';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'MOMON , S.S', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'momon.ss';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Drs. MUHAMMAD RIDWAN, M.M.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'drs.muhammad.ridwan.mm';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'INDRIANI APSARI, S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'indriani.apsari.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SATIVA , SE', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'sativa.se';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'HASYIDAH BR MANURUNG ,S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'hasyidah.br.manurung.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SUMARTONO, S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'sumartono.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'IIS NURHAYATI,S.Si', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'iis.nurhayatissi';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SOLEH, S.Ag', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'soleh.sag';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'IMAS SUNARTI S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'imas.sunarti.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'SITI SUWARNI, S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'siti.suwarni.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'YUKARSA', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'yukarsa';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'ANI SOPYANI', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'ani.sopyani';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'DADANG SUPRIYANTO', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'dadang.supriyanto';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'AHMAD SODIKIN', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'ahmad.sodikin';

-- Wali Kelas - SMA (unit_id = 5)
INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Lilis Nurlaela Hasanah, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'lilis.nurlaela.hasanah.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Kosim Saputra, Drs.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'kosim.saputra.drs';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'M. Ady Saefurohman S. Y., S.H.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'm.ady.saefurohman.s.y.sh';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Raihani Ariefah, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'raihani.ariefah.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Risni Tristania, S.Si.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'risni.tristania.ssi';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Rina Fauziyyah Hasna, S.S.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'rina.fauziyyah.hasna.ss';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Anggia Perbawa, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'anggia.perbawa.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Hj. Eros Rosmawati, Dra., M.M.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'hj.eros.rosmawati.dra.mm';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Dadang Supriatna, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'dadang.supriatna.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Eva Nur Indah Hayati, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'eva.nur.indah.hayati.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Aida Mariyah, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'aida.mariyah.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Hj. Euis Nuraeni, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'hj.euis.nuraeni.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Didik Permana, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'didik.permana.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Fitria Dwi Farina, M.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'fitria.dwi.farina.mpd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Yunita Puji Astutik, S.Kom.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'yunita.puji.astutik.skom';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Rita Permata, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'rita.permata.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Mochtar Arief Kamis, Drs.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'mochtar.arief.kamis.drs';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Yani Taryani, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'yani.taryani.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Budi Jayadi, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'budi.jayadi.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Tati Rohayati, S.S.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'tati.rohayati.ss';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Hanny Fitriani Yusuf, S.Si.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'hanny.fitriani.yusuf.ssi';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'H. Udin Nashruddin, S.Si., M.M.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'h.udin.nashruddin.ssi.mm';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Abas Basari, S.Si. M.M.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'abas.basari.ssi.mm';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Dadan Rohaendi, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'dadan.rohaendi.spd';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Wiwit Widorini, S.Si', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'wiwit.widorini.ssi';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Endang Jamaludin H., S.Ag.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'endang.jamaludin.h.sag';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Noval Anwari Faiz, S.S.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'noval.anwari.faiz.ss';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Endang Rahmat, Drs.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'endang.rahmat.drs';

INSERT IGNORE INTO recommenders (user_id, nama, nomor_induk, unit_id, jenis, telepon, email, status)
SELECT id, 'Wawan Gunawan, S.Ag.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif'
FROM users WHERE username = 'wawan.gunawan.sag';

COMMIT;