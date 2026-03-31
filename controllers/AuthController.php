<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class AuthController {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function login(?string $param = null): void {
        if (Session::isLoggedIn()) {
            Helper::redirect('dashboard/index');
        }

        $error = '';
        if (Helper::isPost()) {
            $username = Helper::post('username');
            $password = Helper::post('password');

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? AND active = 1 LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                Helper::redirect('dashboard/index');
            } else {
                $error = 'Invalid username or password. Please try again.';
            }
        }
        require BASE_PATH . '/views/auth/login.php';
    }

    public function logout(?string $param = null): void {
        Session::destroy();
        Helper::redirect('auth/login');
    }
}
