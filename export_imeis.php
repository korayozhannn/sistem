<?php
require 'config.php';
checkAuth();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=imei_listesi_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['IMEI Numarası', 'Durum', 'Oluşturulma Tarihi']);

$stmt = $pdo->query("SELECT imei_number, status, created_at FROM imei_pool ORDER BY id DESC");
while ($row = $stmt->fetch()) {
    $status = [
        'available' => 'Müsait',
        'assigned' => 'Atanmış',
        'used' => 'Kullanılan'
    ][$row['status']] ?? $row['status'];
    
    fputcsv($output, [
        $row['imei_number'],
        $status,
        date('d.m.Y H:i', strtotime($row['created_at']))
    ]);
}

fclose($output);
exit;