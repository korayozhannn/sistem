<?php
function getExchangeRate() {
    // Önbellek dosyası
    $cacheFile = 'doviz_cache.json';
    $cacheTime = 3600; // 1 saat önbellek

    // Önbellek kontrolü
    if (file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $cacheTime))) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        if (isset($cacheData['rate'])) {
            return $cacheData['rate'];
        }
    }

    // API anahtarları
    $apiKey = '47e71308e3ec917ce43165b7';
    
    // 1. Ana API (exchangerate-api.com)
    $url1 = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD";
    
    // 2. Yedek API (TCMB)
    $url2 = "https://api.genelpara.com/embed/doviz.json";
    
    // 3. Yedek API 2 (freecurrencyapi.net)
    $url3 = "https://api.freecurrencyapi.com/v1/latest?apikey=fca_live_5vF5U8dIxX0K0zYhU4K4jGZJZ2sFmYdZ2Xe1X2J3&base_currency=USD";
    
    $rate = null;
    
    // İlk API'den dene
    $response = @file_get_contents($url1);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['conversion_rates']['TRY'])) {
            $rate = $data['conversion_rates']['TRY'];
        }
    }
    
    // İkinci API'den dene (TCMB verileri)
    if (!$rate) {
        $response = @file_get_contents($url2);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['USD']['satis'])) {
                $rate = floatval($data['USD']['satis']);
            }
        }
    }
    
    // Üçüncü API'den dene
    if (!$rate) {
        $response = @file_get_contents($url3);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['data']['TRY'])) {
                $rate = $data['data']['TRY'];
            }
        }
    }
    
    // Eğer hiçbir API'den kur alınamazsa
    if (!$rate) {
        // Önbellekteki son değeri kullan
        if (file_exists($cacheFile)) {
            $cacheData = json_decode(file_get_contents($cacheFile), true);
            if (isset($cacheData['rate'])) {
                return $cacheData['rate'];
            }
        }
        return 41,57; // Varsayılan değer
    }
    
    // Önbelleğe yaz
    $cacheData = ['rate' => $rate, 'time' => time()];
    file_put_contents($cacheFile, json_encode($cacheData));
    
    return $rate;
}
?>