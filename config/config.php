<?php
// config/config.php
// Update DB credentials as per your XAMPP setup.
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'giftshop',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],

    // App settings
    'app' => [
        'name' => 'Perfect Gifts',
        // If your folder URL is: http://localhost/giftshop_php/ then set base_path to '/giftshop_php'
        // If your folder URL is: http://localhost/ then set base_path to ''
        'base_path' => '/giftshop_php',
        'timezone' => 'Asia/Kolkata',
        // Change this to any random long string for sessions/CSRF.
        'secret' => 'change_this_to_a_random_long_secret_string_1234567890',
    ],

    // Payment demo settings
    'payment' => [
        'provider' => 'demo', // 'demo' or 'razorpay'
        'currency' => 'INR',
        // Optional: If you want Razorpay test mode later, fill these:
        'razorpay_key_id' => '',
        'razorpay_key_secret' => '',
    ],
];
