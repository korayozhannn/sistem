<?php
require 'config.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Telefon Yönetim Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: #2c3e50;
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            border-bottom: 1px solid #34495e;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #34495e;
            color: #3498db;
        }
        .main-content {
            background: #ecf0f1;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="text-center py-3 border-bottom">
                    <h5>Telefon Yönetim</h5>
                    <small>Hoş geldiniz, <?= $_SESSION['full_name'] ?></small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="#" onclick="loadDashboard()">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="#" onclick="loadModule('products')">
                        <i class="fas fa-mobile-alt me-2"></i>Ürün Yönetimi
                    </a>
                    <a class="nav-link" href="#" onclick="loadModule('customers')">
                        <i class="fas fa-users me-2"></i>Müşteri Yönetimi
                    </a>
                    <a class="nav-link" href="#" onclick="loadModule('suppliers')">
                        <i class="fas fa-truck me-2"></i>Tedarikçi Yönetimi
                    </a>
                    <a class="nav-link" href="#" onclick="loadModule('purchases')">
                        <i class="fas fa-shopping-cart me-2"></i>Alış İşlemleri
                    </a>
                    <a class="nav-link" href="#" onclick="loadModule('sales')">
                        <i class="fas fa-cash-register me-2"></i>Satış İşlemleri
                    </a>
                    <a class="nav-link" href="#" onclick="loadModule('imei')">
                        <i class="fas fa-barcode me-2"></i>IMEI Yönetimi
                    </a>
                    <a class="nav-link" href="#" onclick="loadModule('services')">
                        <i class="fas fa-tools me-2"></i>Servis Yönetimi
                    </a>
                    <a class="nav-link" href="#" onclick="loadModule('reports')">
                        <i class="fas fa-chart-bar me-2"></i>Raporlar
                    </a>
                    <a class="nav-link" href="admin_logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Çıkış
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="p-3 border-bottom bg-white">
                    <h4 id="pageTitle">Dashboard</h4>
                </div>
                <div class="p-3" id="content">
                    <!-- Dinamik içerik buraya yüklenecek -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Container -->
    <div id="modalContainer"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>