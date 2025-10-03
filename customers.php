<?php
require 'config.php';
checkAuth();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Müşteri Yönetimi</h4>
        <button class="btn btn-success" onclick="loadModal('add_customer')">
            <i class="fas fa-plus me-2"></i>Yeni Müşteri Ekle
        </button>
    </div>

    <!-- Müşteri Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="customersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Müşteri Adı</th>
                            <th>Telefon</th>
                            <th>E-posta</th>
                            <th>TC Kimlik No</th>
                            <th>Vergi No</th>
                            <th>Durum</th>
                            <th>Kayıt Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM customers ORDER BY id DESC");
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
                            <td><?= $row['id_number'] ?></td>
                            <td><?= $row['tax_number'] ?></td>
                            <td>
                                <span class="badge <?= $row['status'] == 'active' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $row['status'] == 'active' ? 'Aktif' : 'Pasif' ?>
                                </span>
                            </td>
                            <td><?= date('d.m.Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-warning" onclick="loadModal('edit_customer', <?= $row['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-info" onclick="viewCustomerDetails(<?= $row['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteCustomer(<?= $row['id'] ?>)">
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
function viewCustomerDetails(id) {
    // Müşteri detaylarını göster
    loadModal('view_customer', id);
}

function deleteCustomer(id) {
    if (confirm('Bu müşteriyi silmek istediğinizden emin misiniz?')) {
        ajaxRequest('delete_customer', {id: id}, function(response) {
            loadModule('customers');
        });
    }
}

// DataTables başlatma
$(document).ready(function() {
    $('#customersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json'
        },
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>