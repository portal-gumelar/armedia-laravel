<?php

return [
    'mikrotik' => [
        // Token rahasia agar endpoint Webhook tidak bisa dipanggil oleh orang asing
        'webhook_secret' => env('MIKROTIK_WEBHOOK_SECRET', 'secret-token-armedia-123'),
        
        // Konfigurasi RouterOS API v7 (untuk metode PULL)
        'host' => env('MIKROTIK_HOST', '192.168.88.1'),
        'user' => env('MIKROTIK_USER', 'admin'),
        'pass' => env('MIKROTIK_PASS', ''),
    ],
    
    'olt' => [
        'host' => env('OLT_HOST', '192.168.88.2'),
        'snmp_community' => env('OLT_SNMP_COMMUNITY', 'public'),
        'brand' => env('OLT_BRAND', 'zte'), // zte atau huawei
    ]
];
