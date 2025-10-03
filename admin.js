// Modül yükleme fonksiyonu
function loadModule(module, id = null) {
    const url = id ? `${module}.php?id=${id}` : `${module}.php`;

    $('#content').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
            <p class="mt-2">Yükleniyor...</p>
        </div>
    `);

    $.get(url)
        .done(function (data) {
            $('#content').html(data);
            updatePageTitle(module);
            initializeDataTables();
        })
        .fail(function () {
            $('#content').html(`
                <div class="alert alert-danger">
                    Modül yüklenirken hata oluştu.
                </div>
            `);
        });
}

// Dashboard yükleme
function loadDashboard() {
    loadModule('dashboard');
}

// Sayfa başlığını güncelle
function updatePageTitle(module) {
    const titles = {
        'dashboard': 'Dashboard',
        'products': 'Ürün Yönetimi',
        'customers': 'Müşteri Yönetimi',
        'suppliers': 'Tedarikçi Yönetimi',
        'purchases': 'Alış İşlemleri',
        'sales': 'Satış İşlemleri',
        'imei': 'IMEI Yönetimi',
        'services': 'Servis Yönetimi',
        'reports': 'Raporlar'
    };
    $('#pageTitle').text(titles[module] || 'Dashboard');
}

// DataTables başlatma
function initializeDataTables() {
    $('table').each(function() {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json'
                },
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']]
            });
        }
    });
}

// Modal yükleme
function loadModal(modal, id = null) {
    const url = id ? `modals/${modal}.php?id=${id}` : `modals/${modal}.php`;
    
    // Önceki modalı temizle
    $('#modalContainer').empty();
    
    $.get(url)
        .done(function(data) {
            $('#modalContainer').html(data);
            const modalElement = $('.modal');
            modalElement.modal('show');
            
            // Modal gizlendiğinde container'ı temizle
            modalElement.on('hidden.bs.modal', function() {
                $('#modalContainer').empty();
            });
        })
        .fail(function(xhr, status, error) {
            console.error('Modal yükleme hatası:', error);
            showAlert('danger', 'Modal yüklenirken hata oluştu: ' + error);
        });
}

// AJAX işlemleri
function ajaxRequest(url, data, successCallback) {
    $.ajax({
        url: 'admin_operations.php',
        type: 'POST',
        data: { ...data, action: url },
        success: function (response) {
            if (response.success) {
                if (successCallback) successCallback(response);
                showAlert('success', response.message || 'İşlem başarılı!');
            } else {
                showAlert('danger', response.message || 'İşlem başarısız!');
            }
        },
        error: function () {
            showAlert('danger', 'Sunucu hatası!');
        }
    });
}

// Bildirim göster
function showAlert(type, message) {
    const alert = $(`
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    $('body').append(alert);
    setTimeout(() => alert.alert('close'), 5000);
}

// Sayfa yüklendiğinde dashboard'u yükle
$(document).ready(function () {
    loadDashboard();
});