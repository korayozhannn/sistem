<?php
require 'config.php';
checkAuth();

// Marka ve kategori verilerini al
$brands = $pdo->query("SELECT * FROM brands WHERE status='active'")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories WHERE status='active'")->fetchAll();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Ürün Yönetimi</h4>
        <div>
            <button class="btn btn-success me-2" onclick="loadModal('add_product')">
                <i class="fas fa-plus me-2"></i>Yeni Ürün Ekle
            </button>
            <button class="btn btn-primary" onclick="loadModal('purchase_modal')">
                <i class="fas fa-shopping-cart me-2"></i>Stok Girişi
            </button>
        </div>
    </div>

    <!-- Filtreleme -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Marka</label>
                    <select class="form-select" id="brandFilter" onchange="filterProducts()">
                        <option value="">Tüm Markalar</option>
                        <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-select" id="categoryFilter" onchange="filterProducts()">
                        <option value="">Tüm Kategoriler</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stok Durumu</label>
                    <select class="form-select" id="stockFilter" onchange="filterProducts()">
                        <option value="">Tümü</option>
                        <option value="low">Düşük Stok</option>
                        <option value="out">Stokta Yok</option>
                        <option value="available">Stokta Var</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Arama</label>
                    <input type="text" class="form-control" id="searchFilter" placeholder="Ürün adı veya model..." onkeyup="filterProducts()">
                </div>
            </div>
        </div>
    </div>

    <!-- Ürün Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="productsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ürün Adı</th>
                            <th>Model</th>
                            <th>Marka</th>
                            <th>Kategori</th>
                            <th>Seri No</th>
                            <th>Maliyet (USD)</th>
                            <th>Maliyet (TL)</th>
                            <th>Satış Fiyatı (TL)</th>
                            <th>Stok</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT p.*, b.name as brand_name, c.name as category_name 
                            FROM products p 
                            LEFT JOIN brands b ON p.brand_id = b.id 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            ORDER BY p.id DESC
                        ");
                        while ($row = $stmt->fetch()):
                            $stockClass = $row['stock_quantity'] == 0 ? 'bg-danger' : 
                                        ($row['stock_quantity'] <= $row['min_stock_level'] ? 'bg-warning' : 'bg-success');
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <strong><?= $row['name'] ?></strong>
                                <?php if ($row['stock_quantity'] <= $row['min_stock_level'] && $row['stock_quantity'] > 0): ?>
                                    <span class="badge bg-warning ms-1" title="Düşük Stok">!</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['model'] ?></td>
                            <td><?= $row['brand_name'] ?></td>
                            <td><?= $row['category_name'] ?></td>
                            <td>
                                <code><?= $row['serial_number'] ?></code>
                                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="copyToClipboard('<?= $row['serial_number'] ?>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                            <td>$<?= number_format($row['cost_price_usd'], 2) ?></td>
                            <td>₺<?= number_format($row['cost_price_try'], 2) ?></td>
                            <td>
                                <strong>₺<?= number_format($row['selling_price_try'], 2) ?></strong>
                            </td>
                            <td>
                                <span class="badge <?= $stockClass ?>">
                                    <?= $row['stock_quantity'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $row['status'] == 'active' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $row['status'] == 'active' ? 'Aktif' : 'Pasif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-warning" onclick="loadModal('edit_product', <?= $row['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-info" onclick="viewProductDetails(<?= $row['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteProduct(<?= $row['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function filterProducts() {
    const brand = $('#brandFilter').val();
    const category = $('#categoryFilter').val();
    const stock = $('#stockFilter').val();
    const search = $('#searchFilter').val().toLowerCase();
    
    $('#productsTable tbody tr').each(function() {
        const row = $(this);
        const brandMatch = !brand || row.find('td:eq(3)').text() === $('#brandFilter option[value="' + brand + '"]').text();
        const categoryMatch = !category || row.find('td:eq(4)').text() === $('#categoryFilter option[value="' + category + '"]').text();
        const searchMatch = !search || row.find('td:eq(1)').text().toLowerCase().includes(search) || 
                           row.find('td:eq(2)').text().toLowerCase().includes(search);
        
        let stockMatch = true;
        if (stock === 'low') {
            stockMatch = row.find('.badge').hasClass('bg-warning');
        } else if (stock === 'out') {
            stockMatch = row.find('.badge').hasClass('bg-danger');
        } else if (stock === 'available') {
            stockMatch = !row.find('.badge').hasClass('bg-danger');
        }
        
        if (brandMatch && categoryMatch && stockMatch && searchMatch) {
            row.show();
        } else {
            row.hide();
        }
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showAlert('success', 'Seri numarası kopyalandı: ' + text);
    });
}

function viewProductDetails(id) {
    // Ürün detaylarını göster
    loadModal('view_product', id);
}

function deleteProduct(id) {
    if (confirm('Bu ürünü silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
        ajaxRequest('delete_product', {id: id}, function(response) {
            loadModule('products');
        });
    }
}

// DataTables başlatma
$(document).ready(function() {
    $('#productsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json'
        },
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>