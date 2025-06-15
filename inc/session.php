<?php
// inc/session.php
// ——————————————————————————————————————————————————————————————————
// Centralize session_start() so it runs exactly once per request.
// ——————————————————————————————————————————————————————————————————
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
