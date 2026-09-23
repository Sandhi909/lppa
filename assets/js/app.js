(function () {
  const body = document.body;
  document.getElementById('btnCollapse')?.addEventListener('click', () => {
    body.classList.toggle('sidebar-collapsed');
    localStorage.setItem('lppa_sidebar', body.classList.contains('sidebar-collapsed') ? '1' : '0');
  });
  if (localStorage.getItem('lppa_sidebar') === '1') body.classList.add('sidebar-collapsed');
  document.getElementById('btnMobileMenu')?.addEventListener('click', () => body.classList.toggle('sidebar-open'));
  document.getElementById('sidebarBackdrop')?.addEventListener('click', () => body.classList.remove('sidebar-open'));

  const rp = (n) => 'Rp' + Math.round(Number(n) || 0).toLocaleString('id-ID');

  // Modal konfirmasi hapus
  document.addEventListener('click', function (ev) {
    const el = ev.target.closest('[data-confirm]');
    if (!el) return;
    ev.preventDefault();
    document.getElementById('confirmText').textContent = el.getAttribute('data-confirm');
    document.getElementById('confirmOk').setAttribute('href', el.getAttribute('href'));
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
  });

  /* ---------- Form Data Siswa ---------- */
  const f = document.getElementById('formSiswa');
  if (f) {
    const unit = document.getElementById('unit_id');
    const ws = document.getElementById('wali_santri_id');
    const infoWs = document.getElementById('infoWaliSantri');
    const labelWs = document.getElementById('labelWaliSantri');
    const isPsam = document.getElementById('is_psam');
    const biayaPendaftaran = document.getElementById('biaya_pendaftaran');
    const biayaProgram = document.getElementById('biaya_program');
    const totalBiaya = document.getElementById('total_biaya');

    // Aturan Wali Santri berdasarkan field is_psam
    const terapkanAturanPsam = () => {
      const psam = isPsam && isPsam.value === 'Ya';
      if (psam) {
        ws.disabled = false;
        ws.required = true;
        labelWs.innerHTML = 'Wali Santri Perekomendasi <span class="text-danger">*</span>';
        infoWs.className = 'form-text text-danger';
        infoWs.textContent = 'Wajib diisi jika siswa PSAM.';
      } else {
        ws.value = '';
        ws.required = false;
        ws.disabled = true;
        labelWs.textContent = 'Wali Santri Perekomendasi';
        infoWs.className = 'form-text';
        infoWs.textContent = 'Tidak diperlukan untuk non-PSAM.';
      }
    };

    const hitungTotalBiaya = () => {
      const bp = parseFloat(biayaPendaftaran.value || '0') || 0;
      const bprog = parseFloat(biayaProgram.value || '0') || 0;
      const total = bp + bprog;
      totalBiaya.value = rp(total);
      document.getElementById('prevBiayaPendaftaran').textContent = rp(bp);
      document.getElementById('prevBiayaProgram').textContent = rp(bprog);
    };

    if (isPsam) {
      isPsam.addEventListener('change', terapkanAturanPsam);
    }
    [biayaPendaftaran, biayaProgram].forEach((el) => el.addEventListener('input', hitungTotalBiaya));
    terapkanAturanPsam();
    hitungTotalBiaya();

    f.addEventListener('submit', function (ev) {
      const psam = isPsam && isPsam.value === 'Ya';
      let pesan = '';
      if (!unit.value) pesan = 'Unit wajib dipilih.';
      else if (psam && !ws.value) pesan = 'Untuk siswa PSAM, data Wali Santri wajib diisi.';
      if (pesan) { ev.preventDefault(); alert(pesan); return false; }
      // Wali Santri non-PSAM tidak dikirim ke server
      if (!psam) ws.value = '';
      ws.disabled = false; // agar field tetap terkirim (nilai kosong)
    });
  }

  // Auto-submit filter
  document.querySelectorAll('[data-autosubmit]').forEach(el =>
    el.addEventListener('change', () => el.form.submit()));

  // Loading indicator
  document.querySelectorAll('form:not([data-noload])').forEach(fm =>
    fm.addEventListener('submit', () => body.classList.add('loading')));
})();
