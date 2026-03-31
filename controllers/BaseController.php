<?php
class BaseController {
    protected PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        Session::requireLogin();
    }

    protected function view(string $viewPath, array $data = []): void {
        extract($data);
        $pageTitle = $pageTitle ?? 'HRMS';
        $active    = $active    ?? '';
        require BASE_PATH . '/views/layout/header.php';
        require BASE_PATH . '/views/' . $viewPath . '.php';
        require BASE_PATH . '/views/layout/footer.php';
    }

    protected function json(array $data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $path): void {
        Helper::redirect($path);
    }

    protected function getLedgers(): array {
        return $this->pdo
            ->query("SELECT id, account_name, account_type, current_balance FROM ledger_accounts ORDER BY account_name")
            ->fetchAll();
    }

    protected function allClients(): array {
        return $this->pdo
            ->query("SELECT id, company_name FROM clients WHERE status='active' ORDER BY company_name")
            ->fetchAll();
    }

    protected function allEmployees(): array {
        return $this->pdo
            ->query("SELECT id, emp_code, name, designation FROM employees WHERE status='active' ORDER BY name")
            ->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): array|bool {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    protected function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function execute(string $sql, array $params = []): bool {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
