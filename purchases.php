<?php
require 'config.php';
checkAuth();

// Tedarikçileri ve ürünleri al
$suppliers = $pdo->query("SELECT * FROM suppliers WHERE status='active'")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE status='active'")->fetchAll();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Alış İşlemleri</h4>
        <button class="btn btn-success" onclick="loadModal('purchase_modal')">
            <i class="fas fa-plus me-2"></i>Yeni Alış Yap
        </button>
    </div>

    <!-- Alış Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="purchasesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tedarikçi</th>
                            <th>Ürün</th>
                            <th>Miktar</th>
                            <th>Birim Fiyat (USD)</th>
                            <th>Birim Fiyat (TL)</th>
                            <th>Toplam (USD)</th>
                            <th>Toplam (TL)</th>
                            <th>Alış Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT p.*, s.name as supplier_name, pr.name as product_name 
                            FROM purchases p 
                            LEFT JOIN suppliers s ON p.supplier_id = s.id 
                            LEFT JOIN products pr ON p.product_id = pr.id 
                            ORDER BY p.id DESC
                        ");
                        while ($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['supplier_name'] ?></td>
                            <td><?= $row['product_name'] ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td>$<?= number_format($row['unit_price_usd'], 2) ?></td>
                            <td>₺<?= number_format($row['unit_price_try'], 2) ?></td>
                            <td>$<?= number_format($row['total_amount_usd'], 2) ?></td>
                            <td>₺<?= number_format($row['total_amount_try'], 2) ?></td>
                            <td><?= date('d.m.Y', strtotime($row['purchase_date'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewPurchaseDetails(<?= $row['id'] ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deletePurchase(<?= $row['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
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
function viewPurchaseDetails(id) {
    // Alış detaylarını göster
    loadModal('view_purchase', id);
}

function deletePurchase(id) {
    if (confirm('Bu alış kaydını silmek istediğinizden emin misiniz?')) {
        ajaxRequest('delete_purchase', {id: id}, function(response) {
            loadModule('purchases');
        });
    }
}

// DataTables başlatma
$(document).ready(function() {
    $('#purchasesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json'
        },
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>