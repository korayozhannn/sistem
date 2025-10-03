<?php
require '../config.php';
checkAuth();
?>
<div class="modal fade" id="generateImeiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">IMEI Üret</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="generateImeiForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Üretilecek IMEI Sayısı *</label>
                        <input type="number" name="count" class="form-control" value="10" min="1" max="1000" required>
                        <div class="form-text">En fazla 1000 IMEI aynı anda üretilebilir.</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>IMEI Üretim Bilgisi</h6>
                        <p class="mb-0">Üretilen IMEI'ler Luhn algoritması ile doğrulanmış 15 haneli benzersiz numaralardır.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-cogs me-2"></i>IMEI Üret
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#generateImeiForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Üretiliyor...');
        
        ajaxRequest('generate_imei', formData, function(response) {
            $('.modal').modal('hide');
            loadModule('imei');
            submitBtn.prop('disabled', false).html('<i class="fas fa-cogs me-2"></i>IMEI Üret');
        });
    });
});
</script>