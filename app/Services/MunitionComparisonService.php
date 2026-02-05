<?php

namespace App\Services;

/**
 * Mühimmat karşılaştırma verilerini yöneten servis sınıfı
 */
class MunitionComparisonService
{
    /**
     * Statik karşılaştırma verilerini döndürür
     * 
     * @return array
     */
    public static function getComparisonData(): array
    {
        // Statik veri dosyasından veriyi yükle
        return require __DIR__ . '/Data/munition-comparison-data.php';
    }

    /**
     * Belirli kategori, hedef ve hava durumuna göre sonuçları döndürür
     * 
     * @param string $categoryName
     * @param string $targetName
     * @param string $weather
     * @return array
     */
    public static function getResults(string $categoryName, string $targetName, string $weather): array
    {
        $data = self::getComparisonData();
        
        return $data[$categoryName][$targetName][$weather] ?? [];
    }
}
