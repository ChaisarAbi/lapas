<?php
// Test script to verify the new remisi status colors

echo "=== Testing Remisi Status Colors ===\n\n";

// Test values
$testValues = [0.90, 0.85, 0.80, 0.75, 0.70, 0.65, 0.50, 0.40, 0.30];

echo "Nilai Preferensi | Warna Badge       | Status Remisi\n";
echo "---------------- | ----------------- | -------------\n";

foreach ($testValues as $value) {
    // Tentukan warna berdasarkan status remisi
    $badgeClass = 'badge-danger'; // Default: Tidak Layak Remisi
    if ($value >= 0.85) {
        $badgeClass = 'badge-success'; // Remisi Penuh
    } elseif ($value >= 0.75) {
        $badgeClass = 'badge-warning'; // Remisi Separuh
    }
    
    // Tentukan status remisi
    $statusRemisi = '';
    if ($value >= 0.85) {
        $statusRemisi = 'Remisi Penuh';
    } elseif ($value >= 0.75) {
        $statusRemisi = 'Remisi Separuh';
    } else {
