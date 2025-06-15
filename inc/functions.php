<?php
// File: inc/functions.php
// ──────────────────────────────────────────────────────────────────────────
// Helper functions for access control and audit logging.
// Requires: inc/config.php (which defines $conn as a valid mysqli connection).
// Must NOT call session_start() here—sessions are started in inc/session.php.
// ──────────────────────────────────────────────────────────────────────────

// Include the database connection ($conn).
require_once __DIR__ . '/config.php';

/**
 * ensureLoggedIn()
 *
 * If there is no $_SESSION['user_id'], redirect to login.php and exit.
 * Use this at the very top of any page that requires a logged‐in user.
 */
function ensureLoggedIn()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * ensureAdmin()
 *
 * If there is no $_SESSION['user_id'], redirect to login.php (from an admin page).
 * If role ≠ 'admin', redirect to user_dashboard.php and exit.
 * Use at the very top of any admin‐only page.
 */
function ensureAdmin()
{
    if (!isset($_SESSION['user_id'])) {
        // Not logged in at all
        header('Location: ../login.php');
        exit;
    }
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        // Logged in but not an admin
        header('Location: ../user_dashboard.php');
        exit;
    }
}

/**
 * logAction($user_id, $action)
 *
 * Inserts a new row into the 'audit_logs' table:
 *   (user_id, action, timestamp)
 *
 * Example usage:
 *   logAction($_SESSION['user_id'], "Logged in");
 */
function logAction($user_id, $action)
{
    // Ensure we have a valid connection
    global $conn;

    if (empty($user_id) || empty($action)) {
        // Nothing to log
        return;
    }

    $stmt = $conn->prepare("
        INSERT INTO audit_logs (user_id, action, timestamp)
        VALUES (?, ?, NOW())
    ");
    if ($stmt) {
        $stmt->bind_param('is', $user_id, $action);
        $stmt->execute();
        $stmt->close();
    }
}
?>
