-- Script Import Data Users (Wali Kelas & Wali Santri)
-- Password: 12345 (hash bcrypt: $2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi)
-- Struktur tabel: nama, username, email, password, role, status
-- Username format: nama.depan.nama.belakang (huruf kecil, spasi diganti titik)
-- Email: username@lppa.local

START TRANSACTION;

-- Wali Santri (PSAM)
INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('AAM SYAMSIAH', 'aam.syamsiah', 'aam.syamsiah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('ASEP RAHMAN', 'asep.rahman', 'asep.rahman@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('AGUS YUDI ALI FIRDAUS', 'agus.yudi.ali.firdaus', 'agus.yudi.ali.firdaus@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('ALIFA TURROSYIDAH', 'alifa.turrosyidah', 'alifa.turrosyidah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('AYEP MULYA BARKAH', 'ayep.mulya.barkah', 'ayep.mulya.barkah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('DADANG DIMYATI', 'dadang.dimyati', 'dadang.dimyati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('DINI PURNAMA NURUL AJI', 'dini.purnama.nurul.aji', 'dini.purnama.nurul.aji@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('ELA N H', 'ela.n.h', 'ela.n.h@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('ELIS SUMYATI', 'elis.sumyati', 'elis.sumyati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('F. NIZAR', 'f.nizar', 'f.nizar@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('FERRY FERDIANSYAH', 'ferry.ferdiansyah', 'ferry.ferdiansyah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('FITRIADI', 'fitriadi', 'fitriadi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('HAMDANI', 'hamdani', 'hamdani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('HILMAN', 'hilman', 'hilman@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('HUDA AHMAD SYARIF', 'huda.ahmad.syarif', 'huda.ahmad.syarif@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('IDA HAMIDAH', 'ida.hamidah', 'ida.hamidah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('IMA SRI YUNIANI', 'ima.sri.yuniani', 'ima.sri.yuniani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('ITANG RUSTANDI', 'itang.rustandi', 'itang.rustandi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('LUKMANUL HAKIM', 'lukmanul.hakim', 'lukmanul.hakim@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('M. IQBAL SYEKHABUDIN', 'm.iqbal.syekhabudin', 'm.iqbal.syekhabudin@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('MIA NUR ASIAH', 'mia.nur.asiah', 'mia.nur.asiah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('ROSLIANAWATI', 'roslianawati', 'roslianawati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SITI INDIRA MARYAM', 'siti.indira.maryam', 'siti.indira.maryam@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SRI RODIAH', 'sri.rodiah', 'sri.rodiah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SANTI', 'santi', 'santi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('RYSNA', 'rysna', 'rysna@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('NUNUNG KARTINI', 'nunung.kartini', 'nunung.kartini@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('YAYAH HERYATI', 'yayah.heryati', 'yayah.heryati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif');

-- Wali Kelas - SD (unit_id = 3)
INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('TATAN HERAWATI', 'tatan.herawati', 'tatan.herawati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('TINTIN AGUSTIANA', 'tintin.agustiana', 'tintin.agustiana@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SUSAN MARIAM', 'susan.mariam', 'susan.mariam@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('RAISA INDAH FARDINI', 'raisa.indah.fardini', 'raisa.indah.fardini@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('RYAN FEBRIAN', 'ryan.febrian', 'ryan.febrian@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('YULLI MELANIAWATI', 'yulli.melaniawati', 'yulli.melaniawati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SUSILOWATI', 'susilowati', 'susilowati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('HENI HARYANI', 'heni.haryani', 'heni.haryani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('AI MULYANI', 'ai.mulyani', 'ai.mulyani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('TENI WARTINI', 'teni.wartini', 'teni.wartini@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('RD.ELIH JAMILAH', 'rdelih.jamilah', 'rdelih.jamilah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('ABILLA PRIMA HASTUTI', 'abilla.prima.hastuti', 'abilla.prima.hastuti@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('YATI KURHAYATI', 'yati.kurhayati', 'yati.kurhayati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('AYI KANA', 'ayi.kana', 'ayi.kana@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('LILIS LISNAWATI', 'lilis.lisnawati', 'lilis.lisnawati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SRI WULANDARI', 'sri.wulandari', 'sri.wulandari@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('WIWI WIHARYATI', 'wiwi.wiharyati', 'wiwi.wiharyati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('INDAYANI', 'indayani', 'indayani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

-- Wali Kelas - SMP (unit_id = 4)
INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('RIKI AHMAD ZAELANI, S.Si.', 'riki.ahmad.zaelani.ssi', 'riki.ahmad.zaelani.ssi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('VANNY GUSTIKASARI, S.Pd., M.M.', 'vanny.gustikasari.spd.mm', 'vanny.gustikasari.spd.mm@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Hj. KUSMAYATI, S.Pd.', 'hj.kusmayati.spd', 'hj.kusmayati.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Dra. IPAH SARIPAH', 'dra.ipah.saripah', 'dra.ipah.saripah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('ELIS MASITOH, S.P.', 'elis.masitoh.sp', 'elis.masitoh.sp@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('WINA NURNINGSARI, M.Pd.', 'wina.nurningsari.mpd', 'wina.nurningsari.mpd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Drs. RONY HERMANSYAH', 'drs.rony.hermansyah', 'drs.rony.hermansyah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('HANNY HADIYANTI, S.Pd.', 'hanny.hadiyanti.spd', 'hanny.hadiyanti.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('NELA NAYLA, S. Pd', 'nela.nayla.s.pd', 'nela.nayla.s.pd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SUCI LESTARI, M. Pd', 'suci.lestari.m.pd', 'suci.lestari.m.pd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('CHAERUL WILDAN, S.Pd.', 'chaerul.wildan.spd', 'chaerul.wildan.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('KURNIAWATI, S.Ag.', 'kurniawati.sag', 'kurniawati.sag@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('RITA PUSPITASARI, S.Pd', 'rita.puspitasari.spd', 'rita.puspitasari.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('TINTIN SURTINI, Dra', 'tintin.surtini.dra', 'tintin.surtini.dra@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('EUIS NURYANTI DEWI, S.TP', 'euis.nuryanti.dewi.stp', 'euis.nuryanti.dewi.stp@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('MOMON , S.S', 'momon.ss', 'momon.ss@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Drs. MUHAMMAD RIDWAN, M.M.', 'drs.muhammad.ridwan.mm', 'drs.muhammad.ridwan.mm@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('INDRIANI APSARI, S.Pd', 'indriani.apsari.spd', 'indriani.apsari.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SATIVA , SE', 'sativa.se', 'sativa.se@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('HASYIDAH BR MANURUNG ,S.Pd', 'hasyidah.br.manurung.spd', 'hasyidah.br.manurung.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SUMARTONO, S.Pd', 'sumartono.spd', 'sumartono.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('IIS NURHAYATI,S.Si', 'iis.nurhayatissi', 'iis.nurhayatissi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SOLEH, S.Ag', 'soleh.sag', 'soleh.sag@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('IMAS SUNARTI S.Pd', 'imas.sunarti.spd', 'imas.sunarti.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('SITI SUWARNI, S.Pd', 'siti.suwarni.spd', 'siti.suwarni.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('YUKARSA', 'yukarsa', 'yukarsa@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('ANI SOPYANI', 'ani.sopyani', 'ani.sopyani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('DADANG SUPRIYANTO', 'dadang.supriyanto', 'dadang.supriyanto@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('AHMAD SODIKIN', 'ahmad.sodikin', 'ahmad.sodikin@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

-- Wali Kelas - SMA (unit_id = 5)
INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Lilis Nurlaela Hasanah, S.Pd.', 'lilis.nurlaela.hasanah.spd', 'lilis.nurlaela.hasanah.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Kosim Saputra, Drs.', 'kosim.saputra.drs', 'kosim.saputra.drs@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('M. Ady Saefurohman S. Y., S.H.', 'm.ady.saefurohman.s.y.sh', 'm.ady.saefurohman.s.y.sh@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Raihani Ariefah, S.Pd.', 'raihani.ariefah.spd', 'raihani.ariefah.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Risni Tristania, S.Si.', 'risni.tristania.ssi', 'risni.tristania.ssi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Rina Fauziyyah Hasna, S.S.', 'rina.fauziyyah.hasna.ss', 'rina.fauziyyah.hasna.ss@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Anggia Perbawa, S.Pd.', 'anggia.perbawa.spd', 'anggia.perbawa.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Hj. Eros Rosmawati, Dra., M.M.', 'hj.eros.rosmawati.dra.mm', 'hj.eros.rosmawati.dra.mm@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Dadang Supriatna, S.Pd.', 'dadang.supriatna.spd', 'dadang.supriatna.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Eva Nur Indah Hayati, S.Pd.', 'eva.nur.indah.hayati.spd', 'eva.nur.indah.hayati.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Aida Mariyah, S.Pd.', 'aida.mariyah.spd', 'aida.mariyah.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Hj. Euis Nuraeni, S.Pd.', 'hj.euis.nuraeni.spd', 'hj.euis.nuraeni.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Didik Permana, S.Pd.', 'didik.permana.spd', 'didik.permana.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Fitria Dwi Farina, M.Pd.', 'fitria.dwi.farina.mpd', 'fitria.dwi.farina.mpd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Yunita Puji Astutik, S.Kom.', 'yunita.puji.astutik.skom', 'yunita.puji.astutik.skom@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Rita Permata, S.Pd.', 'rita.permata.spd', 'rita.permata.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Mochtar Arief Kamis, Drs.', 'mochtar.arief.kamis.drs', 'mochtar.arief.kamis.drs@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Yani Taryani, S.Pd.', 'yani.taryani.spd', 'yani.taryani.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Budi Jayadi, S.Pd.', 'budi.jayadi.spd', 'budi.jayadi.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Tati Rohayati, S.S.', 'tati.rohayati.ss', 'tati.rohayati.ss@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Hanny Fitriani Yusuf, S.Si.', 'hanny.fitriani.yusuf.ssi', 'hanny.fitriani.yusuf.ssi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('H. Udin Nashruddin, S.Si., M.M.', 'h.udin.nashruddin.ssi.mm', 'h.udin.nashruddin.ssi.mm@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Abas Basari, S.Si. M.M.', 'abas.basari.ssi.mm', 'abas.basari.ssi.mm@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Dadan Rohaendi, S.Pd.', 'dadan.rohaendi.spd', 'dadan.rohaendi.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Wiwit Widorini, S.Si', 'wiwit.widorini.ssi', 'wiwit.widorini.ssi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Endang Jamaludin H., S.Ag.', 'endang.jamaludin.h.sag', 'endang.jamaludin.h.sag@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Noval Anwari Faiz, S.S.', 'noval.anwari.faiz.ss', 'noval.anwari.faiz.ss@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Endang Rahmat, Drs.', 'endang.rahmat.drs', 'endang.rahmat.drs@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

INSERT IGNORE INTO users (nama, username, email, password, role, status) 
VALUES ('Wawan Gunawan, S.Ag.', 'wawan.gunawan.sag', 'wawan.gunawan.sag@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif');

COMMIT;