<?php
require 'config.php';
checkAuth();

// Servis istatistikleri
$totalServices = $pdo->query("SELECT COUNT(*) FROM service_requests")->fetchColumn();
$pendingServices = $pdo->query("SELECT COUNT(*) FROM service_requests WHERE status='pending'")->fetchColumn();
$inProgressServices = $pdo->query("SELECT COUNT(*) FROM service_requests WHERE status='in_progress'")->fetchColumn();
$completedServices = $pdo->query("SELECT COUNT(*) FROM service_requests WHERE status='completed'")->fetchColumn();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Servis Yönetimi</h4>
        <button class="btn btn-success" onclick="loadModal('add_service_modal')">
            <i class="fas fa-tools me-2"></i>Yeni Servis Talebi
        </button>
    </div>

    <!-- Servis İstatistikleri -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= number_format($totalServices) ?></h3>
                        <p class="text-muted">Toplam Servis</p>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-tools fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= number_format($pendingServices) ?></h3>
                        <p class="text-muted">Bekleyen</p>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= number_format($inProgressServices) ?></h3>
                        <p class="text-muted">Devam Eden</p>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-spinner fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?= number_format($completedServices) ?></h3>
                        <p class="text-muted">Tamamlanan</p>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servis Talepleri -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="servicesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Müşteri</th>
                            <th>Ürün</th>
                            <th>IMEI</th>
                            <th>Arıza Tanımı</th>
                            <th>Tahmini Maliyet</th>
                            <th>Gerçek Maliyet</th>
                            <th>Durum</th>
                            <th>Oluşturulma</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT sr.*, c.name as customer_name, p.name as product_name, i.imei_number
                            FROM service_requests sr 
                            LEFT JOIN customers c ON sr.customer_id = c.id 
                            LEFT JOIN products p ON sr.product_id = p.id 
                            LEFT JOIN imei_pool i ON sr.imei_id = i.id 
                            ORDER BY sr.id DESC
                        ");
                        while ($row = $stmt->fetch()):
                            $statusClass = [
                                'pending' => 'bg-warning',
                                'in_progress' => 'bg-info',
                                'completed' => 'bg-success',
                                'cancelled' => 'bg-danger'
                            ][$row['status']] ?? 'bg-secondary';
                            
                            $statusText = [
                                'pending' => 'Bekliyor',
                                'in_progress' => 'Devam Ediyor',
                                'completed' => 'Tamamlandı',
                                'cancelled' => 'İptal Edildi'
                            ][$row['status']] ?? $row['status'];
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['customer_name'] ?></td>
                            <td><?= $row['product_name'] ?></td>
                            <td>
                                <?php if ($row['imei_number']): ?>
                                    <code><?= $row['imei_number'] ?></code>
                                <?php else: ?>
                                    <span class="text-muted">Atanmadı</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span title="<?= htmlspecialchars($row['issue_description']) ?>">
                                    <?= strlen($row['issue_description']) > 50 ? substr($row['issue_description'], 0, 50) . '...' : $row['issue_description'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['estimated_cost']): ?>
                                    ₺<?= number_format($row['estimated_cost'], 2) ?>
                                <?php else: ?>
                                    <span class="text-muted">Belirtilmedi</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['actual_cost']): ?>
                                    <strong>₺<?= number_format($row['actual_cost'], 2) ?></strong>
                                <?php else: ?>
                                    <span class="text-muted">Belirtilmedi</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td><?= date('d.m.Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="viewServiceDetails(<?= $row['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-warning" onclick="loadModal('edit_service_modal', <?= $row['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($row['status'] != 'completed' && $row['status'] != 'cancelled'): ?>
                                        <button class="btn btn-success" onclick="loadModal('service_steps_modal', <?= $row['id'] ?>)">
                                            <i class="fas fa-list-check"></i>
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
function viewServiceDetails(id) {
    loadModal('view_service', id);
}

// DataTables başlatma
$(document).ready(function() {
    $('#servicesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json'
        },
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>