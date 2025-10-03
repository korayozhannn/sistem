<?php
require 'config.php';
checkAuth();

// Varsayılan tarih aralığı (bu ay)
$start_date = date('Y-m-01');
$end_date = date('Y-m-d');

// Eğer filtreleme yapılıyorsa
if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
    $start_date = $_GET['start_date'];
}
if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $end_date = $_GET['end_date'];
}

// Satış raporları
$sales_report = $pdo->prepare("
    SELECT 
        COUNT(*) as total_sales,
        SUM(total_amount_try) as total_revenue,
        AVG(total_amount_try) as avg_sale_amount,
        SUM(quantity) as total_units_sold
    FROM sales 
    WHERE status = 'completed' 
    AND sale_date BETWEEN ? AND ?
");
$sales_report->execute([$start_date, $end_date]);
$sales_data = $sales_report->fetch();

// En çok satan ürünler
$top_products = $pdo->prepare("
    SELECT 
        p.name,
        p.model,
        SUM(s.quantity) as total_sold,
        SUM(s.total_amount_try) as total_revenue
    FROM sales s
    LEFT JOIN products p ON s.product_id = p.id
    WHERE s.status = 'completed' 
    AND s.sale_date BETWEEN ? AND ?
    GROUP BY p.id, p.name, p.model
    ORDER BY total_sold DESC
    LIMIT 10
");
$top_products->execute([$start_date, $end_date]);

// En iyi müşteriler
$top_customers = $pdo->prepare("
    SELECT 
        c.name,
        c.phone,
        COUNT(s.id) as total_purchases,
        SUM(s.total_amount_try) as total_spent
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    WHERE s.status = 'completed' 
    AND s.sale_date BETWEEN ? AND ?
    GROUP BY c.id, c.name, c.phone
    ORDER BY total_spent DESC
    LIMIT 10
");
$top_customers->execute([$start_date, $end_date]);

// Günlük satışlar (son 30 gün)
$daily_sales = $pdo->prepare("
    SELECT 
        sale_date,
        COUNT(*) as sales_count,
        SUM(total_amount_try) as daily_revenue
    FROM sales 
    WHERE status = 'completed' 
    AND sale_date BETWEEN DATE_SUB(?, INTERVAL 30 DAY) AND ?
    GROUP BY sale_date 
    ORDER BY sale_date
");
$daily_sales->execute([$end_date, $end_date]);

// Servis raporları
$service_report = $pdo->prepare("
    SELECT 
        status,
        COUNT(*) as count
    FROM service_requests 
    WHERE created_at BETWEEN ? AND ?
    GROUP BY status
");
$service_report->execute([$start_date, $end_date]);
$service_data = $service_report->fetchAll();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Raporlar ve Analizler</h4>
        <div>
            <button class="btn btn-primary me-2" onclick="exportReports()">
                <i class="fas fa-download me-2"></i>Raporu İndir
            </button>
            <button class="btn btn-success" onclick="printReports()">
                <i class="fas fa-print me-2"></i>Yazdır
            </button>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="reportFilterForm" method="GET" action="reports.php">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bitiş Tarihi</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Filtrele
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Satış Özeti -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= number_format($sales_data['total_sales'] ?? 0) ?></h3>
                        <p class="text-muted">Toplam Satış</p>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3>₺<?= number_format($sales_data['total_revenue'] ?? 0, 2) ?></h3>
                        <p class="text-muted">Toplam Ciro</p>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3>₺<?= number_format($sales_data['avg_sale_amount'] ?? 0, 2) ?></h3>
                        <p class="text-muted">Ortalama Satış</p>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= number_format($sales_data['total_units_sold'] ?? 0) ?></h3>
                        <p class="text-muted">Satılan Birim</p>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-mobile-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- En Çok Satan Ürünler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">En Çok Satan Ürünler</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ürün</th>
                                    <th>Satılan Adet</th>
                                    <th>Toplam Ciro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = $top_products->fetch()): ?>
                                <tr>
                                    <td><?= $product['name'] ?> - <?= $product['model'] ?></td>
                                    <td><?= number_format($product['total_sold']) ?></td>
                                    <td>₺<?= number_format($product['total_revenue'], 2) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- En İyi Müşteriler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">En İyi Müşteriler</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Müşteri</th>
                                    <th>Toplam Alışveriş</th>
                                    <th>Toplam Harcama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($customer = $top_customers->fetch()): ?>
                                <tr>
                                    <td><?= $customer['name'] ?><br><small><?= $customer['phone'] ?></small></td>
                                    <td><?= number_format($customer['total_purchases']) ?></td>
                                    <td>₺<?= number_format($customer['total_spent'], 2) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Servis Durumu -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Servis Durumu</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Durum</th>
                                    <th>Adet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($service_data as $service): ?>
                                <tr>
                                    <td>
                                        <span class="badge <?= [
                                            'pending' => 'bg-warning',
                                            'in_progress' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger'
                                        ][$service['status']] ?? 'bg-secondary' ?>">
                                            <?= [
                                                'pending' => 'Bekleyen',
                                                'in_progress' => 'Devam Eden',
                                                'completed' => 'Tamamlanan',
                                                'cancelled' => 'İptal Edilen'
                                            ][$service['status']] ?? $service['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($service['count']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Günlük Satışlar -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Son 30 Günlük Satışlar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Satış Adeti</th>
                                    <th>Günlük Ciro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($daily = $daily_sales->fetch()): ?>
                                <tr>
                                    <td><?= date('d.m.Y', strtotime($daily['sale_date'])) ?></td>
                                    <td><?= number_format($daily['sales_count']) ?></td>
                                    <td>₺<?= number_format($daily['daily_revenue'], 2) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportReports() {
    // Raporları PDF veya Excel olarak indir
    const startDate = $('input[name="start_date"]').val();
    const endDate = $('input[name="end_date"]').val();
    window.open(`export_reports.php?start_date=${startDate}&end_date=${endDate}`, '_blank');
}

function printReports() {
    window.print();
}

// Sayfa yüklendiğinde filtre formunu AJAX ile çalıştır
$(document).ready(function() {
    $('#reportFilterForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        window.history.pushState({}, '', 'reports.php?' + formData);
        location.reload();
    });
});
</script>