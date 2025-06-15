<?php
// Simply echoes a bcrypt hash of “newpass123”
$plaintext = 'newpass123';
$hash = password_hash($plaintext, PASSWORD_BCRYPT);
echo "bcrypt hash for “{$plaintext}”:<br><br>";
echo htmlspecialchars($hash);

$plaintext = 'pass123';
$hash = password_hash($plaintext, PASSWORD_BCRYPT);
echo "bcrypt hash for “{$plaintext}”:<br><br>";
echo htmlspecialchars($hash);
