<?php
require '../config.php';
checkAuth();

$suppliers = $pdo->query("SELECT * FROM suppliers WHERE status='active'")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE status='active'")->fetchAll();

// Güncel döviz kuru (örnek)
$exchange_rate = 32.5;
?>
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Alış İşlemi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="purchaseForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tedarikçi *</label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">Seçiniz</option>
                                    <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
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
                                    <option value="<?= $product['id'] ?>" data-cost="<?= $product['cost_price_usd'] ?>">
                                        <?= $product['name'] ?> - <?= $product['model'] ?> (SN: <?= $product['serial_number'] ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Miktar *</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Birim Fiyat (USD) *</label>
                                <input type="number" step="0.01" name="unit_price_usd" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Döviz Kuru (USD/TRY)</label>
                                <input type="number" step="0.0001" name="exchange_rate" class="form-control" value="<?= $exchange_rate ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Toplam Tutar (USD)</label>
                                <input type="text" id="total_usd" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Toplam Tutar (TL)</label>
                                <input type="text" id="total_try" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Alış Tarihi *</label>
                                <input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notlar</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
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
    // Ürün seçildiğinde maliyet fiyatını otomatik doldur
    $('select[name="product_id"]').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const costPrice = selectedOption.data('cost');
        if (costPrice) {
            $('input[name="unit_price_usd"]').val(costPrice);
            calculateTotals();
        }
    });

    // Fiyat veya miktar değiştiğinde toplamları hesapla
    $('input[name="unit_price_usd"], input[name="quantity"], input[name="exchange_rate"]').on('input', function() {
        calculateTotals();
    });

    function calculateTotals() {
        const quantity = parseFloat($('input[name="quantity"]').val()) || 0;
        const unitPriceUSD = parseFloat($('input[name="unit_price_usd"]').val()) || 0;
        const exchangeRate = parseFloat($('input[name="exchange_rate"]').val()) || 0;

        const totalUSD = quantity * unitPriceUSD;
        const totalTRY = totalUSD * exchangeRate;

        $('#total_usd').val('$' + totalUSD.toFixed(2));
        $('#total_try').val('₺' + totalTRY.toFixed(2));
    }

    $('#purchaseForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Toplam tutarları form verisine ekle
        const quantity = parseFloat(formData.get('quantity'));
        const unitPriceUSD = parseFloat(formData.get('unit_price_usd'));
        const exchangeRate = parseFloat(formData.get('exchange_rate'));
        
        formData.append('unit_price_try', (unitPriceUSD * exchangeRate).toFixed(2));
        formData.append('total_amount_usd', (quantity * unitPriceUSD).toFixed(2));
        formData.append('total_amount_try', (quantity * unitPriceUSD * exchangeRate).toFixed(2));
        
        ajaxRequest('add_purchase', formData, function(response) {
            $('.modal').modal('hide');
            loadModule('purchases');
        });
    });

    // İlk yüklemede toplamları hesapla
    calculateTotals();
});
</script>