<?php
require '../config.php';
checkAuth();

$brands = $pdo->query("SELECT * FROM brands WHERE status='active'")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories WHERE status='active'")->fetchAll();
?>
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Ürün Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addProductForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ürün Adı *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Model *</label>
                                <input type="text" name="model" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Marka *</label>
                                <select name="brand_id" class="form-select" required>
                                    <option value="">Seçiniz</option>
                                    <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori *</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Seçiniz</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Seri Numarası *</label>
                                <input type="text" name="serial_number" class="form-control" required>
                                <div class="form-text">Bu numara ürünün benzersiz kimliğidir.</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Maliyet Fiyatı (USD)</label>
                                <input type="number" step="0.01" name="cost_price_usd" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Satış Fiyatı (TL) *</label>
                                <input type="number" step="0.01" name="selling_price_try" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Stok Miktarı</label>
                                <input type="number" name="stock_quantity" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Minimum Stok Seviyesi</label>
                                <input type="number" name="min_stock_level" class="form-control" value="5" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Durum</label>
                                <select name="status" class="form-select">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Pasif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Ürün açıklaması..."></textarea>
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
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // TL maliyet hesaplama (USD kuru üzerinden)
        const usdPrice = parseFloat(formData.get('cost_price_usd')) || 0;
        // Burada güncel USD kuru alınabilir
        const exchangeRate = 41.5; // Örnek kur
        formData.append('cost_price_try', (usdPrice * exchangeRate).toFixed(2));
        
        ajaxRequest('add_product', formData, function(response) {
            $('.modal').modal('hide');
            loadModule('products');
        });
    });
});
</script>