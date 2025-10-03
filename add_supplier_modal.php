<?php
require '../config.php';
checkAuth();
?>
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Tedarikçi Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSupplierForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tedarikçi Adı *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Vergi Numarası</label>
                                <input type="text" name="tax_number" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">İletişim Kişisi</label>
                        <input type="text" name="contact_person" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select name="status" class="form-select">
                            <option value="active">Aktif</option>
                            <option value="inactive">Pasif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#addSupplierForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        ajaxRequest('add_supplier', formData, function(response) {
            $('.modal').modal('hide');
            loadModule('suppliers');
        });
    });
});
</script>