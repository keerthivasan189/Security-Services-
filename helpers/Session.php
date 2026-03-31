<?php
class Session {
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/index.php?url=auth/login');
            exit;
        }
    }

    public static function requireRole(string $role): void {
        self::requireLogin();
        if ($_SESSION['user_role'] !== $role && $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/index.php?url=dashboard/index&error=unauthorized');
            exit;
        }
    }

    public static function requireAdmin(): void {
        self::requireLogin();
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . '/index.php?url=dashboard/index&error=unauthorized');
            exit;
        }
    }

    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key) {
        return $_SESSION[$key] ?? null;
    }

    public static function flash(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    public static function destroy(): void {
        session_destroy();
        session_start();
    }

    public static function userId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function userName(): string {
        return $_SESSION['user_name'] ?? 'User';
    }

    public static function userRole(): string {
        return $_SESSION['user_role'] ?? 'viewer';
    }
}
