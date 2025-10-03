<?php
require '../config.php';
checkAuth();

$service_id = $_GET['id'] ?? 0;
$service = $pdo->prepare("
    SELECT sr.*, c.name as customer_name, p.name as product_name 
    FROM service_requests sr 
    LEFT JOIN customers c ON sr.customer_id = c.id 
    LEFT JOIN products p ON sr.product_id = p.id 
    WHERE sr.id = ?
");
$service->execute([$service_id]);
$service_data = $service->fetch();

$steps = $pdo->prepare("SELECT * FROM service_steps WHERE service_request_id = ? ORDER BY id");
$steps->execute([$service_id]);
$service_steps = $steps->fetchAll();
?>
<div class="modal fade" id="serviceStepsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Servis Adımları - #<?= $service_id ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Servis Bilgileri -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6>Servis Bilgileri</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Müşteri:</strong> <?= $service_data['customer_name'] ?><br>
                                <strong>Ürün:</strong> <?= $service_data['product_name'] ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Durum:</strong> 
                                <span class="badge <?= [
                                    'pending' => 'bg-warning',
                                    'in_progress' => 'bg-info',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger'
                                ][$service_data['status']] ?? 'bg-secondary' ?>">
                                    <?= [
                                        'pending' => 'Bekliyor',
                                        'in_progress' => 'Devam Ediyor',
                                        'completed' => 'Tamamlandı',
                                        'cancelled' => 'İptal Edildi'
                                    ][$service_data['status']] ?? $service_data['status'] ?>
                                </span><br>
                                <strong>Arıza:</strong> <?= $service_data['issue_description'] ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Servis Adımları -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6>Servis Adımları</h6>
                    <button type="button" class="btn btn-sm btn-success" onclick="addServiceStep(<?= $service_id ?>)">
                        <i class="fas fa-plus me-1"></i>Adım Ekle
                    </button>
                </div>

                <div id="serviceStepsList">
                    <?php if (empty($service_steps)): ?>
                        <div class="alert alert-info">Henüz servis adımı eklenmemiş.</div>
                    <?php else: ?>
                        <?php foreach ($service_steps as $step): ?>
                        <div class="card mb-2 step-card" data-step-id="<?= $step['id'] ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= $step['step_name'] ?></h6>
                                        <p class="mb-1"><?= $step['step_description'] ?></p>
                                        <small class="text-muted">
                                            Durum: 
                                            <span class="badge <?= [
                                                'pending' => 'bg-warning',
                                                'in_progress' => 'bg-info',
                                                'completed' => 'bg-success'
                                            ][$step['status']] ?? 'bg-secondary' ?>">
                                                <?= [
                                                    'pending' => 'Bekliyor',
                                                    'in_progress' => 'Devam Ediyor',
                                                    'completed' => 'Tamamlandı'
                                                ][$step['status']] ?? $step['status'] ?>
                                            </span>
                                            <?php if ($step['completed_at']): ?>
                                                - Tamamlanma: <?= date('d.m.Y H:i', strtotime($step['completed_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div class="btn-group btn-group-sm ms-3">
                                        <button class="btn btn-warning" onclick="editServiceStep(<?= $step['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteServiceStep(<?= $step['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Maliyet Güncelleme -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6>Maliyet Bilgileri</h6>
                        <form id="updateCostForm">
                            <input type="hidden" name="service_id" value="<?= $service_id ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gerçek Maliyet (TL)</label>
                                        <input type="number" step="0.01" name="actual_cost" class="form-control" value="<?= $service_data['actual_cost'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gerçek Tamamlanma Tarihi</label>
                                        <input type="date" name="actual_completion_date" class="form-control" value="<?= $service_data['actual_completion_date'] ?>">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Maliyetleri Güncelle</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<script>
function addServiceStep(serviceId) {
    const stepName = prompt('Adım adını girin:');
    if (!stepName) return;

    const stepDescription = prompt('Adım açıklamasını girin:');
    if (!stepDescription) return;

    ajaxRequest('add_service_step', {
        service_request_id: serviceId,
        step_name: stepName,
        step_description: stepDescription
    }, function(response) {
        loadModal('service_steps_modal', serviceId);
    });
}

function editServiceStep(stepId) {
    const newStatus = prompt('Yeni durumu seçin:\n1. Bekliyor\n2. Devam Ediyor\n3. Tamamlandı', '1');
    if (!newStatus) return;

    const statusMap = {'1': 'pending', '2': 'in_progress', '3': 'completed'};
    const status = statusMap[newStatus];

    if (!status) {
        alert('Geçersiz seçim!');
        return;
    }

    ajaxRequest('update_service_step', {
        step_id: stepId,
        status: status
    }, function(response) {
        const serviceId = <?= $service_id ?>;
        loadModal('service_steps_modal', serviceId);
    });
}

function deleteServiceStep(stepId) {
    if (confirm('Bu adımı silmek istediğinizden emin misiniz?')) {
        ajaxRequest('delete_service_step', {
            step_id: stepId
        }, function(response) {
            const serviceId = <?= $service_id ?>;
            loadModal('service_steps_modal', serviceId);
        });
    }
}

$(document).ready(function() {
    $('#updateCostForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        ajaxRequest('update_service_cost', formData, function(response) {
            showAlert('success', 'Maliyet bilgileri güncellendi');
        });
    });
});
</script>