<?php
// getExchangeRate.php
function getExchangeRate() {
    // Önbellek dosyası
    $cacheFile = 'doviz_cache.json';
    $cacheTime = 300; // 5 dakika önbellek

    // Önbellek kontrolü
    if (file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $cacheTime))) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        if (isset($cacheData['rate']) && $cacheData['rate'] > 0) {
            error_log("Önbellekten kur alındı: " . $cacheData['rate']);
            return $cacheData['rate'];
        }
    }

    $rate = 32.50; // Varsayılan değer

    try {
        // CollectAPI ayarları
        $apiKey = "apikey 6UjTRd1ZTCEliwjjJ60WRG:4a5gnVUqwm9pRVHmlVsasm";
        $url = "https://api.collectapi.com/economy/exchange?int=10&base=USD";

        // cURL ile API isteği
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "authorization: $apiKey",
            "content-type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response !== false && $httpCode === 200) {
            $data = json_decode($response, true);
            error_log("API Response: " . print_r($data, true));

            if (isset($data["success"]) && $data["success"] === true && isset($data["result"])) {
                foreach ($data["result"] as $currency) {
                    if ($currency["code"] === "USD/TRY") {
                        $rate = floatval($currency["selling"]);
                        error_log("CollectAPI'den kur alındı: " . $rate);
                        break;
                    }
                }
            }
        } else {
            error_log("API isteği başarısız. HTTP Code: " . $httpCode);
        }
    } catch (Exception $e) {
        error_log("API hatası: " . $e->getMessage());
    }

    // Önbelleğe yaz
    $cacheData = ['rate' => $rate, 'time' => time()];
    file_put_contents($cacheFile, json_encode($cacheData));

    return $rate;
}

// Eğer doğrudan çağrılıyorsa kur değerini döndür
if (isset($_GET['api'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'rate' => getExchangeRate()]);
}
?>