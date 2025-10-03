<?php
require '../config.php';
checkAuth();

$customers = $pdo->query("SELECT * FROM customers WHERE status='active'")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE status='active'")->fetchAll();
$availableImeis = $pdo->query("SELECT * FROM imei_pool WHERE status='available' LIMIT 100")->fetchAll();
?>
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Servis Talebi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addServiceForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Müşteri *</label>
                                <select name="customer_id" class="form-select" required>
                                    <option value="">Seçiniz</option>
                                    <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?> - <?= $customer['phone'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ürün *</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">Seçiniz</option>
                                    <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>"><?= $product['name'] ?> - <?= $product['model'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">IMEI Numarası</label>
                                <select name="imei_id" class="form-select">
                                    <option value="">IMEI Atanmayacak</option>
                                    <?php foreach ($availableImeis as $imei): ?>
                                    <option value="<?= $imei['id'] ?>"><?= $imei['imei_number'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tahmini Tamamlanma Tarihi</label>
                                <input type="date" name="estimated_completion_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Arıza Tanımı *</label>
                        <textarea name="issue_description" class="form-control" rows="4" placeholder="Müşterinin belirttiği arıza detaylarını yazın..." required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tahmini Maliyet (TL)</label>
                                <input type="number" step="0.01" name="estimated_cost" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Durum</label>
                                <select name="status" class="form-select">
                                    <option value="pending">Bekliyor</option>
                                    <option value="in_progress">Devam Ediyor</option>
                                    <option value="completed">Tamamlandı</option>
                                    <option value="cancelled">İptal Edildi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#addServiceForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        ajaxRequest('add_service', formData, function(response) {
            $('.modal').modal('hide');
            loadModule('services');
        });
    });
});
</script>