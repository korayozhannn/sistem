<?php
// index.php
require 'alert.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xiaomi Store - Ürün Kataloğu</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --xiaomi-orange: #FF6900;
            --xiaomi-dark: #1A1A1A;
            --xiaomi-light: #F8F9FA;
            --xiaomi-gray: #666666;
            --whatsapp-green: #25D366;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
        }
        
        .navbar {
            background: var(--xiaomi-orange);
            box-shadow: 0 2px 20px rgba(255, 105, 0, 0.3);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        /* Product Card Styles */
        .product-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
            margin-bottom: 25px;
            height: 100%;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(255, 105, 0, 0.15);
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--xiaomi-orange), #ff8c00);
        }
        
        .product-image-container {
            height: 220px;
            overflow: hidden;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .product-image {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--xiaomi-orange);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }
        
        .product-content {
            padding: 20px;
        }
        
        .product-title {
            font-weight: 700;
            color: var(--xiaomi-dark);
            font-size: 1.3rem;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .specs-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin: 20px 0;
        }
        
        .spec-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .spec-item:last-child {
            border-bottom: none;
        }
        
        .spec-icon {
            color: var(--xiaomi-orange);
            margin-right: 12px;
            width: 24px;
            text-align: center;
            font-size: 1.2rem;
        }
        
        .spec-label {
            font-weight: 600;
            color: var(--xiaomi-dark);
            min-width: 120px;
        }
        
        .spec-value {
            color: var(--xiaomi-gray);
            margin-left: auto;
            text-align: right;
            font-weight: 500;
        }
        
        .price-section {
            background: linear-gradient(135deg, #fff5f0, #ffe8d6);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border: 1px solid #ffddd0;
        }
        
        .price-usd {
            font-size: 2rem;
            font-weight: 700;
            color: var(--xiaomi-orange);
            line-height: 1;
        }
        
        .price-try {
            font-size: 1.1rem;
            color: var(--xiaomi-gray);
            margin-top: 5px;
        }
        
        .stock-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #28a745;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        
        .btn-whatsapp {
            flex: 1;
            background: var(--whatsapp-green);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-whatsapp:hover {
            background: #128C7E;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            color: white;
        }
        
        .btn-outline-custom {
            flex: 1;
            background: transparent;
            border: 2px solid var(--xiaomi-orange);
            color: var(--xiaomi-orange);
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-outline-custom:hover {
            background: var(--xiaomi-orange);
            color: white;
            transform: translateY(-2px);
        }
        
        .search-container {
            background: white;
            border-radius: 50px;
            padding: 12px 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }
        
        .search-container:focus-within {
            border-color: var(--xiaomi-orange);
            box-shadow: 0 6px 25px rgba(255, 105, 0, 0.2);
        }
        
        .search-container input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 1.1rem;
            padding: 0;
        }
        
        .section-title {
            font-weight: 700;
            color: var(--xiaomi-dark);
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--xiaomi-orange);
            border-radius: 2px;
        }
        
        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--xiaomi-orange), #ff8c00);
            color: white;
            border-radius: 20px 20px 0 0;
            border: none;
            padding: 25px;
        }
        
        .modal-title {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .whatsapp-icon {
            background: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--whatsapp-green);
            font-weight: bold;
            font-size: 14px;
        }
 /* IMEI Badge */

.imei-badge {
    position: absolute;
    top: 15px;
    left: 15px; /* sağ yerine sol */
    background: var(--xiaomi-orange); /* aynı renk */
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    z-index: 2;
}


/* Server Kayıtlı */
.imei-badge.kayitli {
    background: #0dcaf0; /* Mavi */
    color: white;
}

/* Kayıtsız */
.imei-badge.kayitsiz {
    background: #ffc107; /* Sarı */
    color: black;
}

    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="material-icons me-2">phone_iphone</span>Xiaomi Store
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">
                            <span class="material-icons me-1">admin_panel_settings</span>Admin Paneli
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-lg-8">
                <h1 class="section-title">Xiaomi Ürün Kataloğu</h1>
                <p class="text-muted">En yeni Xiaomi ürünlerini keşfedin. Yüksek kalite, uygun fiyat.</p>
            </div>
            <div class="col-lg-4">
                <div class="search-container">
                    <div class="input-group">
                        <input type="text" class="form-control border-0" placeholder="Model ara... (13T Pro, Note 14, vb.)" id="searchInput">
                        <span class="input-group-text border-0 bg-transparent">
                            <span class="material-icons">search</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Update -->
        <div class="alert alert-info d-flex align-items-center mb-4">
            <span class="material-icons me-2">info</span>
            <span id="lastUpdate">Son güncelleme: Yükleniyor...</span>
        </div>

        <!-- Products Grid -->
        <div class="row g-4" id="productGrid">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
                <p class="mt-3 text-muted">Xiaomi ürünleri yükleniyor...</p>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Sayfalama" class="mt-5">
            <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
    </div>

    <!-- Product Detail Modal -->
    <div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Ürün Detayları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="productModalBody">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global variables
        let allProducts = [];
        let currentPage = 1;
        const productsPerPage = 9;
        const whatsappNumber = '+905323446885';

        // Load products when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            
            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                if (searchTerm.trim() === '') {
                    renderProducts(allProducts);
                } else {
                    const filteredProducts = allProducts.filter(product => {
                        const searchText = `${product.marka} ${product.model} ${product.model_kodu || ''}`.toLowerCase();
                        return searchText.includes(searchTerm);
                    });
                    renderProducts(filteredProducts);
                }
            });
        });

        // Load products from API
        function loadProducts(page = 1) {
            currentPage = page;
            fetch(`get_products.php?page=${page}&per_page=${productsPerPage}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allProducts = data.products;
                        document.getElementById('lastUpdate').textContent = `Son güncelleme: ${data.guncelleme}`;
                        renderProducts(allProducts);
                        renderPagination(data.total);
                    } else {
                        showError('Ürünler yüklenirken hata oluştu: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    showError('Ürünler yüklenirken bir hata oluştu.');
                });
        }

        // Render products grid
        function renderProducts(products) {
            const grid = document.getElementById('productGrid');
            
            if (products.length === 0) {
                grid.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <span class="material-icons display-1 text-muted mb-3">search_off</span>
                        <h4 class="text-muted">Ürün bulunamadı</h4>
                        <p class="text-muted">Arama kriterlerinize uygun ürün bulunamadı.</p>
                    </div>
                `;
                return;
            }
            
            grid.innerHTML = '';
            
            products.forEach(product => {
                const usdPrice = parseFloat(product.satis_fiyati_usd).toFixed(2);
                const tryPrice = product.satis_fiyati_try ? parseFloat(product.satis_fiyati_try).toFixed(2) : 'Hesaplanıyor';
                
// IMEI durumu badge'i
const imeiBadge = product.imei_kayit_durumu == 'kayitli' ? 
    '<div class="imei-badge">Server Kayıtlı</div>' : 
    '<div class="imei-badge">Kayıtsız</div>';

const col = document.createElement('div');
col.className = 'col-xl-4 col-lg-6 col-md-6';
col.innerHTML = `
    <div class="product-card h-100">
        ${imeiBadge}
        <!-- Diğer içerik buraya gelecek -->

                        <div class="product-badge">
                            ${product.stok > 0 ? 'Stokta' : 'Stok Yok'}
                        </div>
                       
                        <div class="product-image-container">
                            <img src="${product.resim}" class="product-image" alt="${product.model}" 
                                 onerror="this.src='https://via.placeholder.com/300x200/FF6900/FFFFFF?text=${encodeURIComponent(product.marka + ' ' + product.model)}'">
                        </div>
                        
                        <div class="product-content">
                            <h3 class="product-title">${product.marka} ${product.model}</h3>
                            
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">memory</span>
                                    <span class="spec-label">İşlemci:</span>
                                    <span class="spec-value">${product.islemci || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">desktop_windows</span>
                                    <span class="spec-label">Ekran:</span>
                                    <span class="spec-value">${product.ekran || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">speed</span>
                                    <span class="spec-label">RAM:</span>
                                    <span class="spec-value">${product.ram} GB</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">storage</span>
                                    <span class="spec-label">Hafıza:</span>
                                    <span class="spec-value">${product.hafiza} GB</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">photo_camera</span>
                                    <span class="spec-label">Kamera:</span>
                                    <span class="spec-value">${product.kamera || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">battery_charging_full</span>
                                    <span class="spec-label">Pil:</span>
                                    <span class="spec-value">${product.pil || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">water_drop</span>
                                    <span class="spec-label">Dayanıklılık:</span>
                                    <span class="spec-value">${product.dayaniklilik || 'Belirtilmemiş'}</span>
                                </div>
                            </div>
                            
                            <div class="price-section">
                                <div class="price-usd">$${usdPrice}</div>
                                <div class="price-try">≈ ₺${tryPrice}</div>
                                <div class="stock-info">
                                    <span class="material-icons">inventory_2</span>
                                    Stok: ${product.stok} adet
                                </div>
                            </div>
                            
                            <div class="action-buttons">
                                <button class="btn-whatsapp" onclick="orderViaWhatsApp(${product.id})">
                                    <span class="whatsapp-icon">✓</span>
                                    WHATSAPP İLE SİPARİŞ
                                </button>
                                <button class="btn-outline-custom" onclick="showProductDetails(${product.id})">
                                    <span class="material-icons">visibility</span>
                                    DETAYLAR
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                grid.appendChild(col);
            });
        }

        // Render pagination
        function renderPagination(total) {
            const totalPages = Math.ceil(total / productsPerPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadProducts(${currentPage - 1})">
                <span class="material-icons">chevron_left</span>
            </a>`;
            pagination.appendChild(prevLi);

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadProducts(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadProducts(${currentPage + 1})">
                <span class="material-icons">chevron_right</span>
            </a>`;
            pagination.appendChild(nextLi);
        }

        // Order via WhatsApp
        function orderViaWhatsApp(productId) {
            const product = allProducts.find(p => p.id === productId);
            if (product) {
                const usdPrice = parseFloat(product.satis_fiyati_usd).toFixed(2);
                const tryPrice = product.satis_fiyati_try ? parseFloat(product.satis_fiyati_try).toFixed(2) : 'Hesaplanıyor';
                
                // Create WhatsApp message
                const message = `Merhaba! ${product.marka} ${product.model} (${product.ram}GB RAM, ${product.hafiza}GB) ürünü ile ilgileniyorum.\n\n` +
                               `Özellikler:\n` +
                               `- Marka: ${product.marka}\n` +
                               `- Model: ${product.model}\n` +
                               `- RAM: ${product.ram}GB\n` +
                               `- Depolama: ${product.hafiza}GB\n` +
                               `- Fiyat: $${usdPrice} (≈ ₺${tryPrice})\n` +
                               `- Stok: ${product.stok} adet\n\n` +
                               `Bu ürün hakkında bilgi almak ve sipariş vermek istiyorum.`;
                
                // Encode message for URL
                const encodedMessage = encodeURIComponent(message);
                
                // Create WhatsApp URL
                const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
                
                // Open WhatsApp in new tab
                window.open(whatsappUrl, '_blank');
            }
        }

        // Show product details modal
        function showProductDetails(productId) {
            fetch(`get_product_details.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayProductModal(data.product);
                    } else {
                        showError('Ürün detayları yüklenirken hata oluştu: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    showError('Ürün detayları yüklenirken bir hata oluştu.');
                });
        }

        // Display product modal
        function displayProductModal(product) {
            const usdPrice = parseFloat(product.satis_fiyati_usd).toFixed(2);
            const tryPrice = product.satis_fiyati_try ? parseFloat(product.satis_fiyati_try).toFixed(2) : 'Hesaplanıyor';
            
            // Create image carousel
            let carouselHTML = '';
            if (product.gorseller && product.gorseller.length > 1) {
                let indicators = '';
                let items = '';
                
                product.gorseller.forEach((img, index) => {
                    const active = index === 0 ? 'active' : '';
                    indicators += `<button type="button" data-bs-target="#productCarousel" data-bs-slide-to="${index}" class="${active}"></button>`;
                    items += `
                        <div class="carousel-item ${active}">
                            <img src="${img}" class="d-block w-100" style="height: 400px; object-fit: contain;" alt="Ürün görseli ${index + 1}">
                        </div>
                    `;
                });
                
                carouselHTML = `
                    <div id="productCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                        <div class="carousel-indicators">${indicators}</div>
                        <div class="carousel-inner">${items}</div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                `;
            } else {
                const imgSrc = product.gorseller && product.gorseller.length > 0 ? product.gorseller[0] : (product.resim || 'upload/default.jpg');
                carouselHTML = `<img src="${imgSrc}" class="img-fluid rounded mb-4" style="height: 400px; object-fit: contain;" alt="${product.model}">`;
            }

            document.getElementById('productModalTitle').textContent = `${product.marka} ${product.model}`;
            document.getElementById('productModalBody').innerHTML = `
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="p-4">
                            ${carouselHTML}
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="p-4">
                            <!-- Price Section -->
                            <div class="price-section mb-4">
                                <div class="price-usd">$${usdPrice}</div>
                                <div class="price-try">≈ ₺${tryPrice}</div>
                                <div class="stock-info">
                                    <span class="material-icons">inventory_2</span>
                                    Stok: ${product.stok} adet
                                </div>
                            </div>

                            <!-- Detailed Specifications -->
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">memory</span>
                                    <span class="spec-label">İşlemci:</span>
                                    <span class="spec-value">${product.islemci || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">desktop_windows</span>
                                    <span class="spec-label">Ekran:</span>
                                    <span class="spec-value">${product.ekran || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">speed</span>
                                    <span class="spec-label">RAM:</span>
                                    <span class="spec-value">${product.ram} GB</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">storage</span>
                                    <span class="spec-label">Hafıza:</span>
                                    <span class="spec-value">${product.hafiza} GB</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">photo_camera</span>
                                    <span class="spec-label">Kamera:</span>
                                    <span class="spec-value">${product.kamera || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">battery_charging_full</span>
                                    <span class="spec-label">Pil:</span>
                                    <span class="spec-value">${product.pil || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">water_drop</span>
                                    <span class="spec-label">Dayanıklılık:</span>
                                    <span class="spec-value">${product.dayaniklilik || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">bolt</span>
                                    <span class="spec-label">Şarj:</span>
                                    <span class="spec-value">${product.sarj || 'Belirtilmemiş'}</span>
                                </div>
                                
                                <div class="spec-item">
                                    <span class="material-icons spec-icon">qr_code</span>
                                    <span class="spec-label">Model Kodu:</span>
                                    <span class="spec-value">${product.model_kodu || 'Belirtilmemiş'}</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons mt-4">
                                <button class="btn-whatsapp" onclick="orderViaWhatsApp(${product.id})">
                                    <span class="whatsapp-icon">✓</span>
                                    WHATSAPP İLE SİPARİŞ
                                </button>
                                <button class="btn-outline-custom" data-bs-dismiss="modal">
                                    <span class="material-icons">close</span>
                                    KAPAT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('productDetailModal'));
            modal.show();
        }

        // Show error message
        function showError(message) {
            const grid = document.getElementById('productGrid');
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <span class="material-icons display-1 text-danger mb-3">error</span>
                    <h4 class="text-danger mt-3">Hata</h4>
                    <p class="text-muted">${message}</p>
                    <button class="btn btn-primary mt-3" onclick="loadProducts()">
                        <span class="material-icons me-2">refresh</span>Tekrar Dene
                    </button>
                </div>
            `;
        }
    </script>
</body>
</html>