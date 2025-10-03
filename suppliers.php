<?php
require 'config.php';
checkAuth();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Tedarikçi Yönetimi</h4>
        <button class="btn btn-success" onclick="loadModal('add_supplier')">
            <i class="fas fa-plus me-2"></i>Yeni Tedarikçi Ekle
        </button>
    </div>

    <!-- Tedarikçi Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="suppliersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tedarikçi Adı</th>
                            <th>Telefon</th>
                            <th>E-posta</th>
                            <th>Vergi No</th>
                            <th>İletişim Kişisi</th>
                            <th>Durum</th>
                            <th>Kayıt Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY id DESC");
                        while ($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <strong><?= $row['name'] ?></strong>
                                <?php if ($row['address']): ?>
                                    <br><small class="text-muted"><?= substr($row['address'], 0, 50) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['phone'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['tax_number'] ?></td>
                            <td><?= $row['contact_person'] ?></td>
                            <td>
                                <span class="badge <?= $row['status'] == 'active' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $row['status'] == 'active' ? 'Aktif' : 'Pasif' ?>
                                </span>
                            </td>
                            <td><?= date('d.m.Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-warning" onclick="loadModal('edit_supplier', <?= $row['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-info" onclick="viewSupplierDetails(<?= $row['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteSupplier(<?= $row['id'] ?>)">
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
function viewSupplierDetails(id) {
    // Tedarikçi detaylarını göster
    loadModal('view_supplier', id);
}

function deleteSupplier(id) {
    if (confirm('Bu tedarikçiyi silmek istediğinizden emin misiniz?')) {
        ajaxRequest('delete_supplier', {id: id}, function(response) {
            loadModule('suppliers');
        });
    }
}

// DataTables başlatma
$(document).ready(function() {
    $('#suppliersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json'
        },
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>