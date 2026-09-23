</main>
<footer class="app-footer">&copy; <?= date('Y') ?> <?= e(COPYRIGHT_HOLDER) ?></footer>
</div></div>
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Konfirmasi Hapus</h5>
      <button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body" id="confirmText">Yakin ingin menghapus data ini?</div>
    <div class="modal-footer">
      <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
      <a href="#" class="btn btn-danger" id="confirmOk">Ya, Hapus</a>
    </div>
  </div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
console.log('Loading Chart.js...');
var script = document.createElement('script');
script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js';
script.onload = function() {
    console.log('Chart.js loaded successfully');
};
script.onerror = function() {
    console.error('Chart.js failed to load');
};
document.head.appendChild(script);
</script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body></html>
