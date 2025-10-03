<?php
require 'config.php';
checkAuth();

// İstatistikleri getir
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM customers WHERE status='active'")->fetchColumn();
$totalSales = $pdo->query("SELECT COUNT(*) FROM sales WHERE status='completed'")->fetchColumn();
$totalServices = $pdo->query("SELECT COUNT(*) FROM service_requests")->fetchColumn();

// Son satışlar
$recentSales = $pdo->query("
    SELECT s.*, c.name as customer_name, p.name as product_name 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    LEFT JOIN products p ON s.product_id = p.id 
    ORDER BY s.created_at DESC 
    LIMIT 5
")->fetchAll();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= $totalProducts ?></h3>
                        <p class="text-muted">Toplam Ürün</p>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-mobile-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= $totalCustomers ?></h3>
                        <p class="text-muted">Toplam Müşteri</p>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= $totalSales ?></h3>
                        <p class="text-muted">Toplam Satış</p>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-cash-register fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= $totalServices ?></h3>
                        <p class="text-muted">Servis Talebi</p>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-tools fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="stat-card">
                <h5>Son Satışlar</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Müşteri</th>
                                <th>Ürün</th>
                                <th>Tutar</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSales as $sale): ?>
                            <tr>
                                <td><?= $sale['customer_name'] ?></td>
                                <td><?= $sale['product_name'] ?></td>
                                <td><?= number_format($sale['total_amount_try'], 2) ?> ₺</td>
                                <td><?= date('d.m.Y', strtotime($sale['sale_date'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h5>Hızlı İşlemler</h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="loadModal('add_product')">
                        <i class="fas fa-plus me-2"></i>Ürün Ekle
                    </button>
                    <button class="btn btn-success" onclick="loadModal('add_sale')">
                        <i class="fas fa-cash-register me-2"></i>Satış Yap
                    </button>
                    <button class="btn btn-info" onclick="loadModal('add_service')">
                        <i class="fas fa-tools me-2"></i>Servis Talebi
                    </button>
                    <button class="btn btn-warning" onclick="loadModule('imei')">
                        <i class="fas fa-barcode me-2"></i>IMEI Üret
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>