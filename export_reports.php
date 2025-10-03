<?php
require 'config.php';
checkAuth();

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="rapor_' . date('Y-m-d') . '.xls"');

// Satış raporları
$sales_report = $pdo->prepare("
    SELECT 
        s.sale_date,
        c.name as customer_name,
        p.name as product_name,
        s.quantity,
        s.unit_price_try,
        s.total_amount_try
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    LEFT JOIN products p ON s.product_id = p.id
    WHERE s.status = 'completed' 
    AND s.sale_date BETWEEN ? AND ?
    ORDER BY s.sale_date DESC
");
$sales_report->execute([$start_date, $end_date]);

echo "Satış Raporu\n";
echo "Tarih Aralığı: $start_date - $end_date\n\n";
echo "Tarih\tMüşteri\tÜrün\tMiktar\tBirim Fiyat\tToplam Tutar\n";

while ($sale = $sales_report->fetch()) {
    echo $sale['sale_date'] . "\t";
    echo $sale['customer_name'] . "\t";
    echo $sale['product_name'] . "\t";
    echo $sale['quantity'] . "\t";
    echo number_format($sale['unit_price_try'], 2) . "\t";
    echo number_format($sale['total_amount_try'], 2) . "\n";
}

exit;