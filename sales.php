<?php
require 'config.php';
checkAuth();

// Müşterileri ve ürünleri al
$customers = $pdo->query("SELECT * FROM customers WHERE status='active'")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE status='active' AND stock_quantity > 0")->fetchAll();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Satış İşlemleri</h4>
        <button class="btn btn-success" onclick="loadModal('sale_modal')">
            <i class="fas fa-cash-register me-2"></i>Yeni Satış Yap
        </button>
    </div>

    <!-- Satış Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="salesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Müşteri</th>
                            <th>Ürün</th>
                            <th>IMEI</th>
                            <th>Miktar</th>
                            <th>Birim Fiyat (TL)</th>
                            <th>Toplam Tutar (TL)</th>
                            <th>Satış Tarihi</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT s.*, c.name as customer_name, p.name as product_name, i.imei_number
                            FROM sales s 
                            LEFT JOIN customers c ON s.customer_id = c.id 
                            LEFT JOIN products p ON s.product_id = p.id 
                            LEFT JOIN imei_pool i ON s.imei_id = i.id 
                            ORDER BY s.id DESC
                        ");
                        while ($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['customer_name'] ?></td>
                            <td><?= $row['product_name'] ?></td>
                            <td>
                                <?php if ($row['imei_number']): ?>
                                    <code><?= $row['imei_number'] ?></code>
                                    <button class="btn btn-sm btn-outline-secondary ms-1" onclick="copyToClipboard('<?= $row['imei_number'] ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">Atanmadı</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['quantity'] ?></td>
                            <td>₺<?= number_format($row['unit_price_try'], 2) ?></td>
                            <td><strong>₺<?= number_format($row['total_amount_try'], 2) ?></strong></td>
                            <td><?= date('d.m.Y', strtotime($row['sale_date'])) ?></td>
                            <td>
                                <span class="badge <?= $row['status'] == 'completed' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $row['status'] == 'completed' ? 'Tamamlandı' : 'İptal Edildi' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="viewSaleDetails(<?= $row['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($row['status'] == 'completed'): ?>
                                        <button class="btn btn-warning" onclick="cancelSale(<?= $row['id'] ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
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
function viewSaleDetails(id) {
    loadModal('view_sale', id);
}

function cancelSale(id) {
    if (confirm('Bu satışı iptal etmek istediğinizden emin misiniz? Stoklar geri işlenecektir.')) {
        ajaxRequest('cancel_sale', {id: id}, function(response) {
            loadModule('sales');
        });
    }
}

// DataTables başlatma
$(document).ready(function() {
    $('#salesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json'
        },
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>