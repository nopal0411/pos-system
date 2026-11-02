<?php
require 'c:/laragon/www/SkripsiFibril/pos_system/config/database.php';

$dir = UPLOAD_PATH;

$garam = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
  <rect width="100" height="100" fill="#f0fdf4" rx="20"/>
  <path d="M40 20 h20 v10 h-20 z" fill="#d1d5db"/>
  <path d="M35 30 h30 v50 c0 5 -5 10 -15 10 s-15 -5 -15 -10 z" fill="#ffffff" stroke="#9ca3af" stroke-width="2"/>
  <text x="50" y="60" font-family="Arial" font-size="16" fill="#4b5563" font-weight="bold" text-anchor="middle">SALT</text>
  <circle cx="45" cy="25" r="1.5" fill="#4b5563"/>
  <circle cx="50" cy="25" r="1.5" fill="#4b5563"/>
  <circle cx="55" cy="25" r="1.5" fill="#4b5563"/>
</svg>';

$kopi = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
  <rect width="100" height="100" fill="#fefce8" rx="20"/>
  <path d="M30 40 h40 v30 c0 15 -40 15 -40 0 z" fill="#78350f"/>
  <path d="M70 45 h10 c5 0 5 15 0 15 h-10" fill="none" stroke="#78350f" stroke-width="6"/>
  <!-- Steam -->
  <path d="M40 30 q-5 -10 5 -20" fill="none" stroke="#d1d5db" stroke-width="3"/>
  <path d="M50 35 q5 -10 -5 -20" fill="none" stroke="#d1d5db" stroke-width="3"/>
  <path d="M60 30 q-5 -10 5 -20" fill="none" stroke="#d1d5db" stroke-width="3"/>
  <text x="50" y="60" font-family="Arial" font-size="14" fill="#fff" font-weight="bold" text-anchor="middle">KOPI</text>
</svg>';

$pilus = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
  <rect width="100" height="100" fill="#fff1f2" rx="20"/>
  <path d="M30 20 l40 0 l5 60 l-50 0 z" fill="#ef4444"/>
  <path d="M25 20 l5 -5 l5 5 l5 -5 l5 5 l5 -5 l5 5 l5 -5 l5 5 l5 -5 l5 5" fill="none" stroke="#ef4444" stroke-width="4"/>
  <path d="M25 80 l5 5 l5 -5 l5 5 l5 -5 l5 5 l5 -5 l5 5 l5 -5 l5 5 l5 -5" fill="none" stroke="#ef4444" stroke-width="4"/>
  <circle cx="50" cy="50" r="15" fill="#fef08a"/>
  <circle cx="45" cy="45" r="3" fill="#ca8a04"/>
  <circle cx="55" cy="55" r="3" fill="#ca8a04"/>
  <circle cx="53" cy="46" r="3" fill="#ca8a04"/>
  <text x="50" y="70" font-family="Arial" font-size="14" fill="#fff" font-weight="bold" text-anchor="middle">PILUS</text>
</svg>';

file_put_contents($dir . 'prod_5.svg', $garam);
file_put_contents($dir . 'prod_8.svg', $kopi);
file_put_contents($dir . 'prod_15.svg', $pilus);

$pdo->query("UPDATE produk SET gambar = 'prod_5.svg' WHERE id_produk = 5");
$pdo->query("UPDATE produk SET gambar = 'prod_8.svg' WHERE id_produk = 8");
$pdo->query("UPDATE produk SET gambar = 'prod_15.svg' WHERE id_produk = 15");

echo "SVG icons updated!";
