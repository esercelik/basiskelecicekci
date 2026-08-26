<?php

return [
    'name' => env('STORE_NAME', 'Başiskele Çiçek'),
    'phone' => env('STORE_PHONE', '+90 262 000 00 00'),
    'phone_url' => 'tel:'.preg_replace('/[^0-9+]/', '', (string) env('STORE_PHONE', '+90 262 000 00 00')),
    'whatsapp_number' => env('STORE_WHATSAPP_NUMBER', '905550000000'),
    'address' => env('STORE_ADDRESS', 'Barbaros Mahallesi, Pınar Caddesi, Başiskele/Kocaeli'),
    'instagram_url' => env('STORE_INSTAGRAM_URL', 'https://www.instagram.com/basiskelecicek'),
    'map_url' => env('STORE_MAP_URL', 'https://maps.google.com/?q=Barbaros+Mahallesi,+Pınar+Caddesi,+Başiskele,+Kocaeli'),
];
