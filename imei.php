<?php
require 'config.php';
checkAuth();

// IMEI istatistikleri
$totalImeis = $pdo->query("SELECT COUNT(*) FROM imei_pool")->fetchColumn();
$availableImeis = $pdo->query("SELECT COUNT(*) FROM imei_pool WHERE status='available'")->fetchColumn();
$assignedImeis = $pdo->query("SELECT COUNT(*) FROM imei_pool WHERE status='assigned'")->fetchColumn();
$usedImeis = $pdo->query("SELECT COUNT(*) FROM imei_pool WHERE status='used'")->fetchColumn();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>IMEI Yönetimi</h4>
        <button class="btn btn-primary" onclick="exportImeis()">
            <i class="fas fa-download me-2"></i>IMEI Listesini İndir
        </button>
    </div>

    <!-- IMEI Üretim Formu -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-cogs me-2"></i> IMEI Üret</h5>
                </div>
                <div class="card-body">
                    <form id="productionForm" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="base_imei" class="form-label">IMEI (14 veya 15 Hane)</label>
                                <input type="text" class="form-control" id="base_imei" name="base_imei" 
                                       maxlength="15" pattern="\d{14,15}" 
                                       title="14 veya 15 haneli IMEI girin" required
                                       oninput="this.value = this.value.replace(/\D/g, '').slice(0,15);">
                                <div class="form-text">Not: 15 haneli IMEI girerseniz, son hane (kontrol hanesi) dikkate alınmaz.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Adet</label>
                                <input type="number" class="form-control" name="adet" value="10" min="1" max="1000" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Yöntem</label>
                                <select name="yontem" class="form-select">
                                    <option value="+1">+1 Artır</option>
                                    <option value="-1">-1 Azalt</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="uretim" class="btn btn-success w-100">
                                    <i class="fa-solid fa-gears me-2"></i> IMEI Üret
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- IMEI İstatistikleri -->
        <div class="col-lg-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3><?= number_format($totalImeis) ?></h3>
                                <p class="text-muted">Toplam IMEI</p>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-barcode fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3><?= number_format($availableImeis) ?></h3>
                                <p class="text-muted">Müsait IMEI</p>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3><?= number_format($assignedImeis) ?></h3>
                                <p class="text-muted">Atanmış IMEI</p>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3><?= number_format($usedImeis) ?></h3>
                                <p class="text-muted">Kullanılan IMEI</p>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-mobile-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- IMEI Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="imeiTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IMEI Numarası</th>
                            <th>Durum</th>
                            <th>Oluşturulma Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM imei_pool ORDER BY id DESC LIMIT 500");
                        while ($row = $stmt->fetch()):
                            $statusClass = [
                                'available' => 'bg-success',
                                'assigned' => 'bg-warning', 
                                'used' => 'bg-info'
                            ][$row['status']] ?? 'bg-secondary';
                            
                            $statusText = [
                                'available' => 'Müsait',
                                'assigned' => 'Atanmış',
                                'used' => 'Kullanılan'
                            ][$row['status']] ?? $row['status'];
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <code><?= $row['imei_number'] ?></code>
                                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="copyToClipboard('<?= $row['imei_number'] ?>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                            <td>
                                <span class="badge <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="viewImeiDetails('<?= $row['imei_number'] ?>')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($row['status'] == 'available'): ?>
                                        <button class="btn btn-warning" onclick="assignImei(<?= $row['id'] ?>)">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteImei(<?= $row['id'] ?>)">
                                            <i class="fas fa-trash"></i>
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
function viewImeiDetails(imei) {
    alert('IMEI Detayları:\n\n' + imei + '\n\nBu IMEI numarasını e-devlet üzerinden sorgulayabilirsiniz.');
}

function assignImei(id) {
    // IMEI atama işlemi - satış modalını aç
    loadModal('sale_modal');
    // Seçili IMEI'yi seç
    setTimeout(() => {
        $('#imeiSelect').val(id);
    }, 500);
}

function deleteImei(id) {
    if (confirm('Bu IMEI numarasını silmek istediğinizden emin misiniz?')) {
        ajaxRequest('delete_imei', {id: id}, function(response) {
            loadModule('imei');
        });
    }
}

function exportImeis() {
    // IMEI listesini indir
    window.open('export_imeis.php', '_blank');
}

// IMEI Üretim Formu İşleme
$(document).ready(function() {
    $('#productionForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Üretiliyor...');
        
        ajaxRequest('generate_imei_serial', formData, function(response) {
            showAlert('success', response.message);
            loadModule('imei');
            submitBtn.prop('disabled', false).html('<i class="fa-solid fa-gears me-2"></i>IMEI Üret');
        });
    });

    // DataTables başlatma
    $('#imeiTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json'
        },
        pageLength: 50,
        order: [[0, 'desc']]
    });
});
</script>