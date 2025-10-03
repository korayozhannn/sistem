<?php
require '../config.php';
checkAuth();

$customers = $pdo->query("SELECT * FROM customers WHERE status='active'")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE status='active' AND stock_quantity > 0")->fetchAll();
$availableImeis = $pdo->query("SELECT * FROM imei_pool WHERE status='available' LIMIT 100")->fetchAll();
?>
<div class="modal fade" id="saleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Satış İşlemi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saleForm">
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
                                <select name="product_id" id="productSelect" class="form-select" required onchange="updateProductInfo()">
                                    <option value="">Seçiniz</option>
                                    <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" 
                                            data-price="<?= $product['selling_price_try'] ?>" 
                                            data-stock="<?= $product['stock_quantity'] ?>"
                                            data-serial="<?= $product['serial_number'] ?>">
                                        <?= $product['name'] ?> - <?= $product['model'] ?> (Stok: <?= $product['stock_quantity'] ?>)
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
                                <input type="number" name="quantity" id="quantityInput" class="form-control" value="1" min="1" required onchange="updateTotals()">
                                <div class="form-text" id="stockInfo">Mevcut stok: 0</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Birim Fiyat (TL) *</label>
                                <input type="number" step="0.01" name="unit_price_try" id="unitPriceInput" class="form-control" required onchange="updateTotals()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Toplam Tutar (TL)</label>
                                <input type="text" id="totalAmount" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">IMEI Numarası</label>
                                <select name="imei_id" class="form-select" id="imeiSelect">
                                    <option value="">IMEI Atanmayacak</option>
                                    <?php foreach ($availableImeis as $imei): ?>
                                    <option value="<?= $imei['id'] ?>"><?= $imei['imei_number'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="loadModule('imei')">
                                        <i class="fas fa-plus me-1"></i>Yeni IMEI Üret
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Satış Tarihi *</label>
                                <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notlar</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Satış ile ilgili notlar..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-cash-register me-2"></i>Satışı Tamamla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateProductInfo() {
    const selectedOption = $('#productSelect').find('option:selected');
    const sellingPrice = selectedOption.data('price');
    const stockQuantity = selectedOption.data('stock');
    const serialNumber = selectedOption.data('serial');
    
    if (sellingPrice) {
        $('#unitPriceInput').val(sellingPrice);
        $('#stockInfo').text('Mevcut stok: ' + stockQuantity + ' - Seri No: ' + serialNumber);
        $('#quantityInput').attr('max', stockQuantity);
    } else {
        $('#unitPriceInput').val('');
        $('#stockInfo').text('Mevcut stok: 0');
        $('#quantityInput').attr('max', 0);
    }
    
    updateTotals();
}

function updateTotals() {
    const quantity = parseFloat($('#quantityInput').val()) || 0;
    const unitPrice = parseFloat($('#unitPriceInput').val()) || 0;
    const totalAmount = quantity * unitPrice;
    
    $('#totalAmount').val('₺' + totalAmount.toFixed(2));
}

$(document).ready(function() {
    $('#saleForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Toplam tutarı form verisine ekle
        const quantity = parseFloat(formData.get('quantity'));
        const unitPrice = parseFloat(formData.get('unit_price_try'));
        formData.append('total_amount_try', (quantity * unitPrice).toFixed(2));
        
        ajaxRequest('add_sale', formData, function(response) {
            $('.modal').modal('hide');
            loadModule('sales');
        });
    });

    // İlk yüklemede toplamları hesapla
    updateTotals();
});
</script>