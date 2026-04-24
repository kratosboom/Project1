<?php

return [

    /*
    |--------------------------------------------------------------------------
    | URL slug → slug lain (opsional, mengalahkan pencocokan otomatis)
    |--------------------------------------------------------------------------
    |
    | Secara bawaan, HomeController menyamakan slug lewat normalisasi
    | (hilangkan strip & spasi): pgsoft ≈ pg-soft, spadegaming ≈ spade-gaming,
    | microgaming ≈ micro-gaming, lalu memilih baris dengan game terbanyak.
    |
    | Tambahkan di sini hanya jika ada kasus khusus yang tidak tertangkap.
    |
    */
    'slug_aliases' => [
        // 'contoh-lama' => 'contoh-baru',
    ],

];
