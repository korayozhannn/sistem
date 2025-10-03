<?php
require 'config.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Geçersiz istek methodu');
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add_product':
            addProduct();
            break;
        case 'update_product':
            updateProduct();
            break;
        case 'delete_product':
            deleteProduct();
            break;
        case 'add_customer':
            addCustomer();
            break;
        case 'update_customer':
            updateCustomer();
            break;
        case 'delete_customer':
            deleteCustomer();
            break;
        case 'add_supplier':
            addSupplier();
            break;
        case 'update_supplier':
            updateSupplier();
            break;
        case 'delete_supplier':
            deleteSupplier();
            break;
        case 'generate_imei':
            generateImei();
            break;
        case 'generate_imei_serial':
            generateImeiSerial();
            break;
        case 'delete_imei':
            deleteImei();
            break;
        case 'add_purchase':
            addPurchase();
            break;
        case 'delete_purchase':
             deletePurchase();
            break;
        case 'add_sale':
            addSale();
            break;
        case 'cancel_sale':
            cancelSale();
            break;
        case 'add_service':
            addService();
            break;
         case 'add_service_step':
             addServiceStep();
             break;
          case 'update_service_step':
             updateServiceStep();
             break;
         case 'delete_service_step':
            deleteServiceStep();
            break;
        case 'update_service_cost':
            updateServiceCost();
            break;
        default:
            jsonResponse(false, 'Geçersiz işlem');
    }
} catch (Exception $e) {
    jsonResponse(false, $e->getMessage());
}

// Ürün ekleme fonksiyonu
function addProduct() {
    global $pdo;
    
    $data = [
        'name' => sanitize($_POST['name']),
        'model' => sanitize($_POST['model']),
        'brand_id' => (int)$_POST['brand_id'],
        'category_id' => (int)$_POST['category_id'],
        'serial_number' => sanitize($_POST['serial_number']),
        'cost_price_usd' => (float)$_POST['cost_price_usd'],
        'cost_price_try' => (float)$_POST['cost_price_try'],
        'selling_price_try' => (float)$_POST['selling_price_try'],
        'stock_quantity' => (int)$_POST['stock_quantity'],
        'min_stock_level' => (int)$_POST['min_stock_level'],
        'description' => sanitize($_POST['description']),
        'status' => sanitize($_POST['status'])
    ];
    
    // Seri numarası kontrolü
    $check = $pdo->prepare("SELECT id FROM products WHERE serial_number = ?");
    $check->execute([$data['serial_number']]);
    if ($check->fetch()) {
        jsonResponse(false, 'Bu seri numarası zaten kayıtlı!');
    }
    
    $stmt = $pdo->prepare("INSERT INTO products (name, model, brand_id, category_id, serial_number, 
                          cost_price_usd, cost_price_try, selling_price_try, stock_quantity, min_stock_level, description, status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute(array_values($data))) {
        jsonResponse(true, 'Ürün başarıyla eklendi', ['id' => $pdo->lastInsertId()]);
    } else {
        jsonResponse(false, 'Ürün eklenirken hata oluştu');
    }
}

// Ürün silme fonksiyonu
function deleteProduct() {
    global $pdo;
    $id = (int)$_POST['id'];
    
    // Ürünün satış veya servis kaydı var mı kontrol et
    $checkSales = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE product_id = ?");
    $checkSales->execute([$id]);
    $salesCount = $checkSales->fetchColumn();
    
    $checkServices = $pdo->prepare("SELECT COUNT(*) FROM service_requests WHERE product_id = ?");
    $checkServices->execute([$id]);
    $servicesCount = $checkServices->fetchColumn();
    
    if ($salesCount > 0 || $servicesCount > 0) {
        // Soft delete: durumu pasif yap
        $stmt = $pdo->prepare("UPDATE products SET status = 'inactive' WHERE id = ?");
        if ($stmt->execute([$id])) {
            jsonResponse(true, 'Ürün pasif hale getirildi (ilişkili kayıtlar var)');
        } else {
            jsonResponse(false, 'Ürün pasif hale getirilemedi');
        }
    } else {
        // Hard delete
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt->execute([$id])) {
            jsonResponse(true, 'Ürün tamamen silindi');
        } else {
            jsonResponse(false, 'Ürün silinirken hata oluştu');
        }
    }
}
// IMEI üretme fonksiyonu
function generateImei() {
    global $pdo;
    
    $count = (int)$_POST['count'];
    $generated = 0;
    $errors = 0;
    
    if ($count > 1000) {
        jsonResponse(false, 'En fazla 1000 IMEI aynı anda üretilebilir!');
    }
    
    try {
        $pdo->beginTransaction();
        
        for ($i = 0; $i < $count; $i++) {
            $imei = generateValidImei();
            
            // IMEI'nin zaten var olup olmadığını kontrol et
            $check = $pdo->prepare("SELECT id FROM imei_pool WHERE imei_number = ?");
            $check->execute([$imei]);
            
            if (!$check->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO imei_pool (imei_number) VALUES (?)");
                if ($stmt->execute([$imei])) {
                    $generated++;
                } else {
                    $errors++;
                }
            } else {
                $errors++; // Zaten varsa hata say
            }
        }
        
        $pdo->commit();
        
        $message = "$generated IMEI başarıyla üretildi";
        if ($errors > 0) {
            $message .= " ($errors IMEI atlandı - zaten mevcut)";
        }
        
        jsonResponse(true, $message, ['generated' => $generated, 'errors' => $errors]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(false, 'IMEI üretilirken hata: ' . $e->getMessage());
    }
}

// Geçerli IMEI üretme fonksiyonu
function generateValidImei() {
    $imei14 = '';
    for ($i = 0; $i < 14; $i++) {
        $imei14 .= mt_rand(0, 9);
    }
    
    $checkDigit = calculateLuhnCheckDigit($imei14);
    return $imei14 . $checkDigit;
}

// Luhn algoritması ile check digit hesaplama
function calculateLuhnCheckDigit($imei14) {
    $position = 0;
    $total = 0;
    
    while ($position < 14) {
        $digit = (int)$imei14[$position];
        
        if ($position % 2 == 1) { // Çift pozisyonlar (2,4,6...14)
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        $total += $digit;
        $position++;
    }
    
    $checkDigit = (10 - ($total % 10)) % 10;
    return $checkDigit;
}

// IMEI silme fonksiyonu
function deleteImei() {
    global $pdo;
    $id = (int)$_POST['id'];
    
    // IMEI'nin kullanım durumunu kontrol et
    $check = $pdo->prepare("SELECT status FROM imei_pool WHERE id = ?");
    $check->execute([$id]);
    $imeiStatus = $check->fetchColumn();
    
    if ($imeiStatus !== 'available') {
        jsonResponse(false, 'Sadece müsait durumdaki IMEI\'ler silinebilir!');
    }
    
    $stmt = $pdo->prepare("DELETE FROM imei_pool WHERE id = ?");
    if ($stmt->execute([$id])) {
        jsonResponse(true, 'IMEI başarıyla silindi');
    } else {
        jsonResponse(false, 'IMEI silinirken hata oluştu');
    }
}

function addCustomer() {
    global $pdo;
    
    $data = [
        'name' => sanitize($_POST['name']),
        'phone' => sanitize($_POST['phone']),
        'email' => sanitize($_POST['email']),
        'address' => sanitize($_POST['address']),
        'tax_number' => sanitize($_POST['tax_number']),
        'id_number' => sanitize($_POST['id_number']),
        'status' => sanitize($_POST['status'])
    ];
    
    $stmt = $pdo->prepare("INSERT INTO customers (name, phone, email, address, tax_number, id_number, status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute(array_values($data))) {
        jsonResponse(true, 'Müşteri başarıyla eklendi', ['id' => $pdo->lastInsertId()]);
    } else {
        jsonResponse(false, 'Müşteri eklenirken hata oluştu');
    }
}

// Müşteri silme fonksiyonu
function deleteCustomer() {
    global $pdo;
    $id = (int)$_POST['id'];
    
    // Müşterinin satış veya servis kaydı var mı kontrol et
    $checkSales = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE customer_id = ?");
    $checkSales->execute([$id]);
    $salesCount = $checkSales->fetchColumn();
    
    $checkServices = $pdo->prepare("SELECT COUNT(*) FROM service_requests WHERE customer_id = ?");
    $checkServices->execute([$id]);
    $servicesCount = $checkServices->fetchColumn();
    
    if ($salesCount > 0 || $servicesCount > 0) {
        // Soft delete: durumu pasif yap
        $stmt = $pdo->prepare("UPDATE customers SET status = 'inactive' WHERE id = ?");
        if ($stmt->execute([$id])) {
            jsonResponse(true, 'Müşteri pasif hale getirildi (ilişkili kayıtlar var)');
        } else {
            jsonResponse(false, 'Müşteri pasif hale getirilemedi');
        }
    } else {
        // Hard delete
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
        if ($stmt->execute([$id])) {
            jsonResponse(true, 'Müşteri tamamen silindi');
        } else {
            jsonResponse(false, 'Müşteri silinirken hata oluştu');
        }
    }
}
// Alış ekleme fonksiyonu
function addPurchase() {
    global $pdo;
    
    $data = [
        'supplier_id' => (int)$_POST['supplier_id'],
        'product_id' => (int)$_POST['product_id'],
        'quantity' => (int)$_POST['quantity'],
        'unit_price_usd' => (float)$_POST['unit_price_usd'],
        'unit_price_try' => (float)$_POST['unit_price_try'],
        'total_amount_usd' => (float)$_POST['total_amount_usd'],
        'total_amount_try' => (float)$_POST['total_amount_try'],
        'exchange_rate' => (float)$_POST['exchange_rate'],
        'purchase_date' => sanitize($_POST['purchase_date']),
        'notes' => sanitize($_POST['notes'])
    ];
    
    $stmt = $pdo->prepare("INSERT INTO purchases (supplier_id, product_id, quantity, unit_price_usd, unit_price_try, 
                          total_amount_usd, total_amount_try, exchange_rate, purchase_date, notes) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute(array_values($data))) {
        // Stok miktarını güncelle
        updateProductStock($data['product_id'], $data['quantity']);
        
        jsonResponse(true, 'Alış işlemi başarıyla kaydedildi', ['id' => $pdo->lastInsertId()]);
    } else {
        jsonResponse(false, 'Alış işlemi kaydedilirken hata oluştu');
    }
}

// Ürün stok güncelleme
function updateProductStock($productId, $quantity) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
    $stmt->execute([$quantity, $productId]);
}

// Alış silme fonksiyonu
function deletePurchase() {
    global $pdo;
    $id = (int)$_POST['id'];
    
    // Alış bilgilerini al
    $purchase = $pdo->prepare("SELECT * FROM purchases WHERE id = ?");
    $purchase->execute([$id]);
    $purchaseData = $purchase->fetch();
    
    if ($purchaseData) {
        // Stok miktarını geri al
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $stmt->execute([$purchaseData['quantity'], $purchaseData['product_id']]);
        
        // Alış kaydını sil
        $deleteStmt = $pdo->prepare("DELETE FROM purchases WHERE id = ?");
        if ($deleteStmt->execute([$id])) {
            jsonResponse(true, 'Alış kaydı silindi ve stok güncellendi');
        } else {
            jsonResponse(false, 'Alış kaydı silinirken hata oluştu');
        }
    } else {
        jsonResponse(false, 'Alış kaydı bulunamadı');
    }
}
// Satış ekleme fonksiyonu
function addSale() {
    global $pdo;
    
    $data = [
        'customer_id' => (int)$_POST['customer_id'],
        'product_id' => (int)$_POST['product_id'],
        'imei_id' => !empty($_POST['imei_id']) ? (int)$_POST['imei_id'] : null,
        'quantity' => (int)$_POST['quantity'],
        'unit_price_try' => (float)$_POST['unit_price_try'],
        'total_amount_try' => (float)$_POST['total_amount_try'],
        'sale_date' => sanitize($_POST['sale_date']),
        'notes' => sanitize($_POST['notes'])
    ];
    
    // Stok kontrolü
    $stockCheck = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
    $stockCheck->execute([$data['product_id']]);
    $currentStock = $stockCheck->fetchColumn();
    
    if ($currentStock < $data['quantity']) {
        jsonResponse(false, 'Yetersiz stok! Mevcut stok: ' . $currentStock);
    }
    
    // IMEI kontrolü (eğer seçilmişse)
    if ($data['imei_id']) {
        $imeiCheck = $pdo->prepare("SELECT status FROM imei_pool WHERE id = ?");
        $imeiCheck->execute([$data['imei_id']]);
        $imeiStatus = $imeiCheck->fetchColumn();
        
        if ($imeiStatus !== 'available') {
            jsonResponse(false, 'Seçilen IMEI kullanımda veya müsait değil!');
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Satış kaydını ekle
        $stmt = $pdo->prepare("INSERT INTO sales (customer_id, product_id, imei_id, quantity, unit_price_try, total_amount_try, sale_date, notes) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array_values($data));
        $saleId = $pdo->lastInsertId();
        
        // Stok miktarını güncelle
        $updateStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $updateStock->execute([$data['quantity'], $data['product_id']]);
        
        // IMEI durumunu güncelle (eğer seçilmişse)
        if ($data['imei_id']) {
            $updateImei = $pdo->prepare("UPDATE imei_pool SET status = 'assigned' WHERE id = ?");
            $updateImei->execute([$data['imei_id']]);
        }
        
        $pdo->commit();
        
        jsonResponse(true, 'Satış işlemi başarıyla tamamlandı', ['id' => $saleId]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(false, 'Satış işlemi sırasında hata: ' . $e->getMessage());
    }
}

// Satış iptal etme fonksiyonu
function cancelSale() {
    global $pdo;
    $id = (int)$_POST['id'];
    
    try {
        $pdo->beginTransaction();
        
        // Satış bilgilerini al
        $sale = $pdo->prepare("SELECT * FROM sales WHERE id = ? AND status = 'completed'");
        $sale->execute([$id]);
        $saleData = $sale->fetch();
        
        if (!$saleData) {
            throw new Exception('Satış kaydı bulunamadı veya zaten iptal edilmiş!');
        }
        
        // Stok miktarını geri al
        $updateStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
        $updateStock->execute([$saleData['quantity'], $saleData['product_id']]);
        
        // IMEI durumunu geri al (eğer atanmışsa)
        if ($saleData['imei_id']) {
            $updateImei = $pdo->prepare("UPDATE imei_pool SET status = 'available' WHERE id = ?");
            $updateImei->execute([$saleData['imei_id']]);
        }
        
        // Satış durumunu iptal olarak güncelle
        $updateSale = $pdo->prepare("UPDATE sales SET status = 'cancelled' WHERE id = ?");
        $updateSale->execute([$id]);
        
        $pdo->commit();
        
        jsonResponse(true, 'Satış işlemi başarıyla iptal edildi');
        
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(false, 'Satış iptal edilirken hata: ' . $e->getMessage());
    }
}
// Servis talebi ekleme fonksiyonu
function addService() {
    global $pdo;
    
    $data = [
        'customer_id' => (int)$_POST['customer_id'],
        'product_id' => (int)$_POST['product_id'],
        'imei_id' => !empty($_POST['imei_id']) ? (int)$_POST['imei_id'] : null,
        'issue_description' => sanitize($_POST['issue_description']),
        'estimated_cost' => !empty($_POST['estimated_cost']) ? (float)$_POST['estimated_cost'] : null,
        'estimated_completion_date' => !empty($_POST['estimated_completion_date']) ? sanitize($_POST['estimated_completion_date']) : null,
        'status' => sanitize($_POST['status'])
    ];
    
    // IMEI kontrolü (eğer seçilmişse)
    if ($data['imei_id']) {
        $imeiCheck = $pdo->prepare("SELECT status FROM imei_pool WHERE id = ?");
        $imeiCheck->execute([$data['imei_id']]);
        $imeiStatus = $imeiCheck->fetchColumn();
        
        if ($imeiStatus !== 'available') {
            jsonResponse(false, 'Seçilen IMEI kullanımda veya müsait değil!');
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Servis talebini ekle
        $stmt = $pdo->prepare("INSERT INTO service_requests (customer_id, product_id, imei_id, issue_description, estimated_cost, estimated_completion_date, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array_values($data));
        $serviceId = $pdo->lastInsertId();
        
        // IMEI durumunu güncelle (eğer seçilmişse)
        if ($data['imei_id']) {
            $updateImei = $pdo->prepare("UPDATE imei_pool SET status = 'used' WHERE id = ?");
            $updateImei->execute([$data['imei_id']]);
        }
        
        $pdo->commit();
        
        jsonResponse(true, 'Servis talebi başarıyla oluşturuldu', ['id' => $serviceId]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(false, 'Servis talebi oluşturulurken hata: ' . $e->getMessage());
    }
}

// Servis adımı ekleme fonksiyonu
function addServiceStep() {
    global $pdo;
    
    $data = [
        'service_request_id' => (int)$_POST['service_request_id'],
        'step_name' => sanitize($_POST['step_name']),
        'step_description' => sanitize($_POST['step_description']),
        'status' => 'pending'
    ];
    
    $stmt = $pdo->prepare("INSERT INTO service_steps (service_request_id, step_name, step_description, status) 
                          VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute(array_values($data))) {
        jsonResponse(true, 'Servis adımı başarıyla eklendi');
    } else {
        jsonResponse(false, 'Servis adımı eklenirken hata oluştu');
    }
}

// Servis adımı güncelleme fonksiyonu
function updateServiceStep() {
    global $pdo;
    
    $step_id = (int)$_POST['step_id'];
    $status = sanitize($_POST['status']);
    
    $completed_at = $status === 'completed' ? date('Y-m-d H:i:s') : null;
    
    $stmt = $pdo->prepare("UPDATE service_steps SET status = ?, completed_at = ? WHERE id = ?");
    
    if ($stmt->execute([$status, $completed_at, $step_id])) {
        jsonResponse(true, 'Servis adımı başarıyla güncellendi');
    } else {
        jsonResponse(false, 'Servis adımı güncellenirken hata oluştu');
    }
}

// Servis adımı silme fonksiyonu
function deleteServiceStep() {
    global $pdo;
    $step_id = (int)$_POST['step_id'];
    
    $stmt = $pdo->prepare("DELETE FROM service_steps WHERE id = ?");
    
    if ($stmt->execute([$step_id])) {
        jsonResponse(true, 'Servis adımı başarıyla silindi');
    } else {
        jsonResponse(false, 'Servis adımı silinirken hata oluştu');
    }
}

// Servis maliyet güncelleme fonksiyonu
function updateServiceCost() {
    global $pdo;
    
    $service_id = (int)$_POST['service_id'];
    $actual_cost = !empty($_POST['actual_cost']) ? (float)$_POST['actual_cost'] : null;
    $actual_completion_date = !empty($_POST['actual_completion_date']) ? sanitize($_POST['actual_completion_date']) : null;
    
    // Eğer gerçek maliyet girilmişse ve tamamlanma tarihi girilmemişse, bugünün tarihini ata
    if ($actual_cost && !$actual_completion_date) {
        $actual_completion_date = date('Y-m-d');
    }
    
    $stmt = $pdo->prepare("UPDATE service_requests SET actual_cost = ?, actual_completion_date = ? WHERE id = ?");
    
    if ($stmt->execute([$actual_cost, $actual_completion_date, $service_id])) {
        jsonResponse(true, 'Servis maliyet bilgileri başarıyla güncellendi');
    } else {
        jsonResponse(false, 'Servis maliyet bilgileri güncellenirken hata oluştu');
    }
}
// Seri IMEI üretme fonksiyonu
function generateImeiSerial() {
    global $pdo;
    
    $base_imei_raw = $_POST['base_imei'];
    $adet = (int)$_POST['adet'];
    $yontem = $_POST['yontem'];
    
    // İlk 14 haneyi al, 14'ten kısa ise sola sıfır ekle
    $base_imei = substr($base_imei_raw, 0, 14);
    $base_imei = str_pad($base_imei, 14, '0', STR_PAD_LEFT);
    
    $generated = 0;
    $skipped = 0;
    
    if ($adet > 1000) {
        jsonResponse(false, 'En fazla 1000 IMEI aynı anda üretilebilir!');
    }
    
    try {
        $pdo->beginTransaction();
        
        for ($i = 0; $i < $adet; $i++) {
            $current14 = $yontem == '+1' 
                ? str_pad((string)((int)$base_imei + $i), 14, '0', STR_PAD_LEFT)
                : str_pad((string)((int)$base_imei - $i), 14, '0', STR_PAD_LEFT);
                
            $checkDigit = calculateLuhnCheckDigit($current14);
            $imei = $current14 . $checkDigit;

            // IMEI'nin zaten var olup olmadığını kontrol et
            $check = $pdo->prepare("SELECT id FROM imei_pool WHERE imei_number = ?");
            $check->execute([$imei]);
            
            if (!$check->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO imei_pool (imei_number) VALUES (?)");
                if ($stmt->execute([$imei])) {
                    $generated++;
                } else {
                    $skipped++;
                }
            } else {
                $skipped++;
            }
        }
        
        $pdo->commit();
        
        $message = "$generated IMEI başarıyla üretildi";
        if ($skipped > 0) {
            $message .= " ($skipped IMEI atlandı - zaten mevcut)";
        }
        
        jsonResponse(true, $message, ['generated' => $generated, 'skipped' => $skipped]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(false, 'IMEI üretilirken hata: ' . $e->getMessage());
    }
}
// Diğer CRUD fonksiyonları buraya eklenecek...
?>