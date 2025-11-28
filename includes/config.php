<?php
// includes/config.local.php
// Local override for production / hosted server

return [
    'db' => [
        'host' => 'localhost:3306',             // Your MySQL host with port
        'name' => 'adatecmu_adatech_cms',      // Your database name
        'user' => 'your_db_username',          // Replace with your cPanel DB user
        'pass' => 'your_db_password',          // Replace with your DB password
        'charset' => 'utf8mb4',
    ],
    'site' => [
        'name' => 'Adatech Solutions',
    ],
];
