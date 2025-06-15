<?php
// generateQR.php
require_once 'phpqrcode/qrlib.php';
$text = $_GET['code'] ?? 'No data';
header('Content-Type: image/png');
QRcode::png($text, false, QR_ECLEVEL_L, 5);
// (error correction level L, size 5)
